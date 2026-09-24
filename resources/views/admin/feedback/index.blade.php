@extends('layouts.admin')

@section('title', 'Feedback')

@section('content')
<div class="paf-admin-page-header">
    <h1 class="paf-admin-title mb-0">Feedback</h1>
    <div class="paf-admin-page-actions">
        @can('reports.export')
        <a href="{{ route('admin.reports.export.csv', request()->query()) }}" class="btn paf-btn-outline btn-sm">Export
            CSV</a>
        @endcan
    </div>
</div>

{{-- Filters --}}
<div class="paf-card mb-3">
    <form id="feedback-filters" method="GET" class="paf-filter-form">
        <div class="paf-filter-field">
            <label for="f-status">Status</label>
            <select id="f-status" name="status" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach (['submitted', 'acknowledged', 'assigned', 'in_progress', 'resolved', 'closed'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="paf-filter-field">
            <label for="f-type">Type</label>
            <select id="f-type" name="feedback_type" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach (['compliment', 'suggestion', 'complaint', 'query'] as $t)
                <option value="{{ $t }}" @selected(request('feedback_type')===$t)>
                    {{ ucfirst($t) }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="paf-filter-field">
            <label for="f-priority">Priority</label>
            <select id="f-priority" name="priority" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach (['low', 'medium', 'high', 'critical'] as $p)
                <option value="{{ $p }}" @selected(request('priority')===$p)>
                    {{ ucfirst($p) }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="paf-filter-field">
            <label for="f-category">Category</label>
            <select id="f-category" name="category_id" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected((string) request('category_id')===(string) $c->id)>
                    {{ $c->name_en }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="paf-filter-field">
            <label for="f-location">Location</label>
            <select id="f-location" name="location_id" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach ($locations as $l)
                <option value="{{ $l->id }}" @selected((string) request('location_id')===(string) $l->id)>
                    {{ $l->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="paf-filter-field">
            <label for="f-from">From</label>
            <input type="date" id="f-from" name="from" class="form-control form-control-sm"
                value="{{ request('from') }}">
        </div>

        <div class="paf-filter-field">
            <label for="f-to">To</label>
            <input type="date" id="f-to" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
        </div>

        <div class="paf-filter-actions">
            <button type="submit" class="btn paf-btn-primary btn-sm">Filter</button>
            <button type="button" class="btn paf-btn-outline btn-sm" data-reset>Reset</button>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="paf-card">
    <div class="table-responsive">
        <table id="feedback-table" class="table table-hover align-middle mb-0"
            data-url="{{ route('admin.feedback.datatable', request()->query()) }}">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Submitted</th>
                    <th>Type</th>
                    <th>Rating</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection
