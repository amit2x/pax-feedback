@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="paf-admin-page-header">
    <h1 class="paf-admin-title mb-0">Audit Logs</h1>
</div>

<div class="paf-card mb-3">
    <form method="GET" class="paf-filter-form">
        <div class="paf-filter-field">
            <label>Action</label>
            <input type="text" name="action" class="form-control form-control-sm" value="{{ request('action') }}"
                placeholder="e.g. feedback.viewed">
        </div>
        <div class="paf-filter-field">
            <label>From</label>
            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
        </div>
        <div class="paf-filter-field">
            <label>To</label>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
        </div>
        <div class="paf-filter-actions">
            <button type="submit" class="btn paf-btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.audit.index') }}" class="btn paf-btn-outline btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="paf-card">
    @if ($logs->isEmpty())
    <div class="paf-empty">
        <span class="paf-empty-icon">📜</span>
        No audit logs found.
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                <tr>
                    <td class="text-muted small text-nowrap">
                        {{ $log->created_at?->format('d M Y H:i') }}
                    </td>
                    <td>{{ $log->user?->name ?? 'System' }}</td>
                    <td><code>{{ $log->action }}</code></td>
                    <td class="small text-muted">
                        @if ($log->entity_type)
                        {{ $log->entity_type }} #{{ $log->entity_id }}
                        @else
                        —
                        @endif
                    </td>
                    <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
