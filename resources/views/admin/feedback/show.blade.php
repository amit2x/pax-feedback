@extends('layouts.admin')

@section('title', 'Feedback ' . $feedback->reference_no)

@section('content')
<div class="paf-admin-page-header">
    <div>
        <a href="{{ route('admin.feedback.index') }}" class="small text-muted text-decoration-none">
            ← Back to list
        </a>
        <h1 class="paf-admin-title mb-0 mt-1">
            {{ $feedback->reference_no }}
        </h1>
    </div>

    <div class="paf-admin-page-actions">
        @can('feedback.delete')
        <form method="POST" action="{{ route('admin.feedback.destroy', $feedback->uuid) }}"
            data-confirm="Delete this feedback? It will be soft-deleted and remain recoverable." class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
        </form>
        @endcan
    </div>
</div>

{{-- Summary card --}}
<div class="paf-card mb-3">
    <div class="paf-detail-grid">
        <div class="paf-detail-item">
            <span class="paf-detail-label">Type</span>
            <span class="paf-detail-value">
                <span class="paf-badge paf-badge--{{ $feedback->feedback_type }}">
                    {{ ucfirst($feedback->feedback_type) }}
                </span>
            </span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Rating</span>
            <span class="paf-detail-value">{{ $feedback->overall_rating }} / 5</span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Status</span>
            <span class="paf-detail-value">
                <span class="paf-badge paf-badge--status-{{ $feedback->status }}">
                    {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                </span>
            </span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Priority</span>
            <span class="paf-detail-value">
                <span class="paf-badge paf-badge--priority-{{ $feedback->priority }}">
                    {{ ucfirst($feedback->priority) }}
                </span>
            </span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Submitted</span>
            <span class="paf-detail-value">{{ $feedback->submitted_at?->format('d M Y, H:i') }}</span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Language</span>
            <span class="paf-detail-value">{{ strtoupper($feedback->language_code) }}</span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Category</span>
            <span class="paf-detail-value">{{ $feedback->category?->name_en ?? '—' }}</span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Subcategory</span>
            <span class="paf-detail-value">{{ $feedback->subcategory?->name_en ?? '—' }}</span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Location</span>
            <span class="paf-detail-value">
                {{ $feedback->location?->name ?? '—' }}
                @if ($feedback->location?->checkpoint_label)
                <span class="text-muted small">({{ $feedback->location->checkpoint_label }})</span>
                @endif
            </span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Terminal</span>
            <span class="paf-detail-value">{{ $feedback->terminal?->name ?? '—' }}</span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">QR Code</span>
            <span class="paf-detail-value">{{ $feedback->qrCode?->name ?? '—' }}</span>
        </div>

        <div class="paf-detail-item">
            <span class="paf-detail-label">Source</span>
            <span class="paf-detail-value">{{ ucfirst($feedback->submission_source) }}</span>
        </div>
    </div>
</div>

{{-- Comment --}}
@if ($feedback->comment)
<div class="paf-card mb-3">
    <h2 class="paf-step-title mb-2">Comment</h2>
    <div class="paf-comment-block">{{ $feedback->comment }}</div>
</div>
@endif

{{-- Attachments --}}
@if ($feedback->attachments->isNotEmpty())
<div class="paf-card mb-3">
    <h2 class="paf-step-title mb-3">Attachments</h2>

    <div class="paf-attachment-grid">
        @foreach ($feedback->attachments as $att)
        <div class="paf-attachment-card">
            @if ($att->type === 'photo')
            <img src="{{ route('admin.feedback.attachment.download', [$feedback->uuid, $att->uuid]) }}"
                alt="Feedback photo" loading="lazy">
            @else
            <div
                style="aspect-ratio:1; background:#eef2f7; display:flex; align-items:center; justify-content:center; font-size:2rem;">
                🎙
            </div>
            @endif

            <div class="paf-attachment-meta">
                <span class="paf-attachment-type">{{ $att->type }}</span>
                <span class="paf-attachment-size">{{ number_format($att->size / 1024, 0) }} KB</span>
                <a href="{{ route('admin.feedback.attachment.download', [$feedback->uuid, $att->uuid]) }}"
                    class="paf-attachment-action">Download →</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Passenger info --}}
<div class="paf-card mb-3">
    <h2 class="paf-step-title mb-3">
        {{ $feedback->is_anonymous ? 'Anonymous Submission' : 'Passenger Information' }}
    </h2>

    @if ($feedback->is_anonymous)
    <p class="text-muted mb-0 small">This feedback was submitted anonymously.</p>
    @else
    <div class="paf-detail-grid">
        <div class="paf-detail-item">
            <span class="paf-detail-label">Name</span>
            <span class="paf-detail-value">{{ $feedback->name ?? '—' }}</span>
        </div>
        <div class="paf-detail-item">
            <span class="paf-detail-label">Mobile</span>
            <span class="paf-detail-value">{{ $feedback->mobile ?? '—' }}</span>
        </div>
        <div class="paf-detail-item">
            <span class="paf-detail-label">Email</span>
            <span class="paf-detail-value">{{ $feedback->email ?? '—' }}</span>
        </div>
        <div class="paf-detail-item">
            <span class="paf-detail-label">Preferred Contact</span>
            <span class="paf-detail-value">{{ ucfirst($feedback->preferred_contact_method) }}</span>
        </div>
    </div>
    @endif
</div>

{{-- Actions: status + priority + assignment --}}
<div class="row g-3 mb-3">
    @can('feedback.update')
    <div class="col-md-4">
        <div class="paf-card h-100">
            <h2 class="paf-step-title mb-2">Update Status</h2>
            <form method="POST" action="{{ route('admin.feedback.status', $feedback->uuid) }}">
                @csrf
                @method('PATCH')

                <div class="mb-2">
                    <select name="status" class="form-select form-select-sm">
                        @foreach (['submitted', 'acknowledged', 'assigned', 'in_progress', 'resolved', 'closed'] as $s)
                        <option value="{{ $s }}" @selected($feedback->status === $s)>
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <input type="text" name="note" class="form-control form-control-sm" placeholder="Optional note"
                        maxlength="500">
                </div>

                <button type="submit" class="btn paf-btn-primary btn-sm w-100">Update Status</button>
            </form>
        </div>
    </div>
    @endcan

    @can('feedback.update')
    <div class="col-md-4">
        <div class="paf-card h-100">
            <h2 class="paf-step-title mb-2">Update Priority</h2>
            <form method="POST" action="{{ route('admin.feedback.priority', $feedback->uuid) }}">
                @csrf
                @method('PATCH')

                <div class="mb-2">
                    <select name="priority" class="form-select form-select-sm">
                        @foreach (['low', 'medium', 'high', 'critical'] as $p)
                        <option value="{{ $p }}" @selected($feedback->priority === $p)>
                            {{ ucfirst($p) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn paf-btn-primary btn-sm w-100">Update Priority</button>
            </form>
        </div>
    </div>
    @endcan

    @can('feedback.assign')
    <div class="col-md-4">
        <div class="paf-card h-100">
            <h2 class="paf-step-title mb-2">Assign to Department</h2>
            <form method="POST" action="{{ route('admin.feedback.assign', $feedback->uuid) }}">
                @csrf

                <div class="mb-2">
                    <select name="department_id" class="form-select form-select-sm" required>
                        <option value="">Choose a department…</option>
                        @foreach ($departments as $d)
                        <option value="{{ $d->id }}" @selected($feedback->department_id === $d->id)>
                            {{ $d->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <input type="text" name="note" class="form-control form-control-sm" placeholder="Optional note"
                        maxlength="500">
                </div>

                <button type="submit" class="btn paf-btn-primary btn-sm w-100">Assign</button>
            </form>
        </div>
    </div>
    @endcan
</div>

{{-- Status history --}}
@if ($feedback->statusHistories->isNotEmpty())
<div class="paf-card">
    <h2 class="paf-step-title mb-3">Status History</h2>

    <div class="paf-timeline">
        @foreach ($feedback->statusHistories->sortByDesc('created_at') as $h)
        <div class="paf-timeline-item">
            <div class="paf-timeline-time">
                {{ $h->created_at->format('d M Y, H:i') }}
                @if ($h->changedBy) · {{ $h->changedBy->name }} @endif
            </div>
            <div class="paf-timeline-body">
                @if ($h->from_status)
                <strong>{{ ucfirst(str_replace('_', ' ', $h->from_status)) }}</strong>
                →
                @endif
                <strong>{{ ucfirst(str_replace('_', ' ', $h->to_status)) }}</strong>

                @if ($h->note)
                <div class="small text-muted mt-1">{{ $h->note }}</div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
