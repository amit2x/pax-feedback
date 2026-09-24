<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $base = Feedback::whereBetween('submitted_at', [
            $from->copy()->startOfDay(),
            $to->copy()->endOfDay(),
        ]);

        $stats = [
            'total' => (clone $base)->count(),
            'avg_rating' => round((float) (clone $base)->avg('overall_rating'), 2),
            'complaints' => (clone $base)->where('feedback_type', 'complaint')->count(),
            'suggestions' => (clone $base)->where('feedback_type', 'suggestion')->count(),
            'compliments' => (clone $base)->where('feedback_type', 'compliment')->count(),
            'queries' => (clone $base)->where('feedback_type', 'query')->count(),
        ];

        $byDay = (clone $base)
            ->selectRaw('DATE(submitted_at) as day, COUNT(*) as total, AVG(overall_rating) as avg_rating')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $byCategory = (clone $base)
            ->select('category_id', DB::raw('COUNT(*) as total'))
            ->with('category:id,name_en')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $byRating = (clone $base)
            ->select('overall_rating', DB::raw('COUNT(*) as total'))
            ->groupBy('overall_rating')
            ->orderBy('overall_rating')
            ->get();

        $byType = (clone $base)
            ->select('feedback_type', DB::raw('COUNT(*) as total'))
            ->groupBy('feedback_type')
            ->get();

        return view('admin.reports.index', compact(
            'from', 'to', 'stats', 'byDay', 'byCategory', 'byRating', 'byType'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $from = $request->date('from') ?? now()->subDays(30);
        $to = $request->date('to') ?? now();

        $filename = 'feedback-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Reference', 'Submitted', 'Type', 'Rating',
                'Category', 'Location', 'Status', 'Priority', 'Comment',
            ]);

            Feedback::with(['category:id,name_en', 'location:id,name'])
                ->whereBetween('submitted_at', [
                    $from->copy()->startOfDay(),
                    $to->copy()->endOfDay(),
                ])
                ->orderByDesc('submitted_at')
                ->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $fb) {
                        fputcsv($out, [
                            $fb->reference_no,
                            $fb->submitted_at?->format('Y-m-d H:i'),
                            $fb->feedback_type,
                            $fb->overall_rating,
                            $fb->category?->name_en,
                            $fb->location?->name,
                            $fb->status,
                            $fb->priority,
                            $this->sanitizeCsv($fb->comment),
                        ]);
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function sanitizeCsv(?string $value): string
    {
        $value = (string) $value;
        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
