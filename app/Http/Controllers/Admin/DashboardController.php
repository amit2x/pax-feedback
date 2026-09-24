<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total' => Feedback::count(),
            'today' => Feedback::whereDate('submitted_at', today())->count(),
            'avg_rating' => round((float) Feedback::avg('overall_rating'), 2),
            'complaints' => Feedback::where('feedback_type', 'complaint')->count(),
            'suggestions' => Feedback::where('feedback_type', 'suggestion')->count(),
            'compliments' => Feedback::where('feedback_type', 'compliment')->count(),
            'open' => Feedback::whereIn('status', ['submitted', 'acknowledged', 'assigned', 'in_progress'])->count(),
            'resolved' => Feedback::whereIn('status', ['resolved', 'closed'])->count(),
        ];

        $recentFeedback = Feedback::with(['category', 'location'])
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentFeedback'));
    }
}
