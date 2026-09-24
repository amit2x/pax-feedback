<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackAttachment;
use App\Models\FeedbackCategory;
use App\Models\FeedbackDepartment;
use App\Models\FeedbackLocation;
use App\Models\FeedbackStatusHistory;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeedbackController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(Request $request): View
    {
        return view('admin.feedback.index', [
            'categories' => FeedbackCategory::orderBy('name_en')->get(),
            'locations' => FeedbackLocation::orderBy('name')->get(),
            'departments' => FeedbackDepartment::orderBy('name')->get(),
        ]);
    }

    /**
     * Server-side DataTables JSON feed.
     */
    public function datatable(Request $request): JsonResponse
    {
        $query = Feedback::query()
            ->with(['category:id,name_en', 'location:id,name']);

        // ---------- Filters ----------
        if ($v = $request->input('status')) {
            $query->where('status', $v);
        }
        if ($v = $request->input('feedback_type')) {
            $query->where('feedback_type', $v);
        }
        if ($v = $request->input('priority')) {
            $query->where('priority', $v);
        }
        if ($v = $request->input('category_id')) {
            $query->where('category_id', $v);
        }
        if ($v = $request->input('location_id')) {
            $query->where('location_id', $v);
        }
        if ($v = $request->input('from')) {
            $query->where('submitted_at', '>=', $v.' 00:00:00');
        }
        if ($v = $request->input('to')) {
            $query->where('submitted_at', '<=', $v.' 23:59:59');
        }

        // Global search (DataTables search box)
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // ---------- Ordering ----------
        $orderable = [
            0 => 'reference_no',
            1 => 'submitted_at',
            2 => 'feedback_type',
            3 => 'overall_rating',
            6 => 'status',
            7 => 'priority',
        ];
        $orderCol = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($orderable[$orderCol] ?? 'submitted_at', $orderDir);

        // ---------- Pagination ----------
        $total = (clone $query)->count();
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);
        $length = max(1, min($length, 200));

        $rows = $query->skip($start)->take($length)->get();

        $data = $rows->map(function (Feedback $fb) {
            $ratingClass = match (true) {
                $fb->overall_rating <= 2 => 'is-low',
                $fb->overall_rating === 3 => 'is-mid',
                default => 'is-high',
            };

            return [
                'reference_no' => '<a href="'.route('admin.feedback.show', $fb->uuid).'" class="text-decoration-none fw-semibold">'
                                    .e($fb->reference_no).'</a>',
                'submitted_at' => $fb->submitted_at?->format('d M Y, H:i') ?? '—',
                'feedback_type' => '<span class="paf-badge paf-badge--'.e($fb->feedback_type).'">'
                                    .e(ucfirst($fb->feedback_type)).'</span>',
                'overall_rating' => '<span class="paf-rating-pill '.$ratingClass.'">'
                                    .$fb->overall_rating.'/5</span>',
                'category_name' => e($fb->category?->name_en ?? '—'),
                'location_name' => e($fb->location?->name ?? '—'),
                'status' => '<span class="paf-badge paf-badge--status-'.e($fb->status).'">'
                                    .e(str_replace('_', ' ', ucfirst($fb->status))).'</span>',
                'priority' => '<span class="paf-badge paf-badge--priority-'.e($fb->priority).'">'
                                    .e(ucfirst($fb->priority)).'</span>',
                'action' => '<a href="'.route('admin.feedback.show', $fb->uuid).'" class="btn btn-sm paf-btn-outline">View</a>',
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
        ]);
    }

    public function show(Feedback $feedback): View
    {
        $feedback->load([
            'category', 'subcategory', 'location', 'terminal', 'zone', 'service',
            'qrCode', 'attachments', 'statusHistories.changedBy',
            'assignments.department', 'assignments.assignedTo', 'department',
        ]);

        $this->audit->log('feedback.viewed', $feedback);

        return view('admin.feedback.show', [
            'feedback' => $feedback,
            'departments' => FeedbackDepartment::orderBy('name')->get(),
        ]);
    }

    public function updateStatus(Request $request, Feedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:submitted,acknowledged,assigned,in_progress,resolved,closed'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $old = $feedback->status;

        DB::transaction(function () use ($feedback, $validated, $old) {
            $feedback->update(['status' => $validated['status']]);

            FeedbackStatusHistory::create([
                'feedback_id' => $feedback->id,
                'from_status' => $old,
                'to_status' => $validated['status'],
                'changed_by' => auth()->id(),
                'note' => $validated['note'] ?? null,
            ]);
        });

        $this->audit->log('feedback.status_changed', $feedback,
            ['status' => $old], ['status' => $validated['status']]);

        return back()->with('status', 'Status updated.');
    }

    public function updatePriority(Request $request, Feedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'priority' => ['required', 'in:low,medium,high,critical'],
        ]);

        $old = $feedback->priority;
        $feedback->update(['priority' => $validated['priority']]);

        $this->audit->log('feedback.priority_changed', $feedback,
            ['priority' => $old], ['priority' => $validated['priority']]);

        return back()->with('status', 'Priority updated.');
    }

    public function assign(Request $request, Feedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'department_id' => ['required', 'exists:feedback_departments,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($feedback, $validated) {
            $feedback->update(['department_id' => $validated['department_id']]);

            $feedback->assignments()->create([
                'department_id' => $validated['department_id'],
                'assigned_by' => auth()->id(),
                'note' => $validated['note'] ?? null,
                'assigned_at' => now(),
            ]);
        });

        $this->audit->log('feedback.assigned', $feedback, [],
            ['department_id' => $validated['department_id']]);

        return back()->with('status', 'Assigned to department.');
    }

    public function downloadAttachment(Feedback $feedback, FeedbackAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->feedback_id === $feedback->id, 404);

        $disk = Storage::disk($attachment->storage_disk);
        abort_unless($disk->exists($attachment->storage_path), 404);

        $this->audit->log('feedback.attachment_downloaded', $feedback,
            [], ['attachment_uuid' => $attachment->uuid]);

        $filename = "feedback-{$feedback->reference_no}-{$attachment->type}.{$attachment->extension}";

        return $disk->download(
            $attachment->storage_path,
            $filename,
            ['Content-Type' => $attachment->mime_type]
        );
    }

    public function destroy(Feedback $feedback): RedirectResponse
    {
        $this->audit->log('feedback.deleted', $feedback);
        $feedback->delete();

        return redirect()->route('admin.feedback.index')
            ->with('status', 'Feedback moved to trash.');
    }
}
