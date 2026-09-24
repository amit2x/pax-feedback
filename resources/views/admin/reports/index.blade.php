@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="paf-admin-page-header">
    <h1 class="paf-admin-title mb-0">Reports</h1>
    @can('reports.export')
    <a href="{{ route('admin.reports.export.csv', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
        class="btn paf-btn-primary btn-sm">Export CSV</a>
    @endcan
</div>

<div class="paf-card mb-3">
    <form method="GET" class="paf-filter-form">
        <div class="paf-filter-field">
            <label>From</label>
            <input type="date" name="from" class="form-control form-control-sm" value="{{ $from->toDateString() }}">
        </div>
        <div class="paf-filter-field">
            <label>To</label>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ $to->toDateString() }}">
        </div>
        <div class="paf-filter-actions">
            <button type="submit" class="btn paf-btn-primary btn-sm">Apply</button>
        </div>
    </form>
</div>

{{-- Summary stats --}}
<div class="paf-stat-grid mb-3">
    <div class="paf-stat-card">
        <div class="paf-stat-label">Total</div>
        <div class="paf-stat-value">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="paf-stat-card">
        <div class="paf-stat-label">Avg Rating</div>
        <div class="paf-stat-value">{{ $stats['avg_rating'] }}</div>
    </div>
    <div class="paf-stat-card paf-stat-card--danger">
        <div class="paf-stat-label">Complaints</div>
        <div class="paf-stat-value">{{ $stats['complaints'] }}</div>
    </div>
    <div class="paf-stat-card paf-stat-card--info">
        <div class="paf-stat-label">Suggestions</div>
        <div class="paf-stat-value">{{ $stats['suggestions'] }}</div>
    </div>
    <div class="paf-stat-card paf-stat-card--success">
        <div class="paf-stat-label">Compliments</div>
        <div class="paf-stat-value">{{ $stats['compliments'] }}</div>
    </div>
    <div class="paf-stat-card">
        <div class="paf-stat-label">Queries</div>
        <div class="paf-stat-value">{{ $stats['queries'] }}</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="paf-card h-100">
            <h2 class="paf-step-title mb-3">By Category</h2>
            @if ($byCategory->isEmpty())
            <p class="text-muted mb-0">No data.</p>
            @else
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="text-end">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($byCategory as $row)
                    <tr>
                        <td>{{ $row->category?->name_en ?? '—' }}</td>
                        <td class="text-end">{{ number_format($row->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="paf-card h-100">
            <h2 class="paf-step-title mb-3">Rating Distribution</h2>
            @if ($byRating->isEmpty())
            <p class="text-muted mb-0">No data.</p>
            @else
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Rating</th>
                        <th class="text-end">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($byRating as $row)
                    <tr>
                        <td>{{ $row->overall_rating }}/5</td>
                        <td class="text-end">{{ number_format($row->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="paf-card h-100">
            <h2 class="paf-step-title mb-3">By Type</h2>
            @if ($byType->isEmpty())
            <p class="text-muted mb-0">No data.</p>
            @else
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th class="text-end">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($byType as $row)
                    <tr>
                        <td>{{ ucfirst($row->feedback_type) }}</td>
                        <td class="text-end">{{ number_format($row->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="paf-card h-100">
            <h2 class="paf-step-title mb-3">Daily Trend</h2>
            @if ($byDay->isEmpty())
            <p class="text-muted mb-0">No data.</p>
            @else
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Avg Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($byDay as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->day)->format('d M Y') }}</td>
                        <td class="text-end">{{ number_format($row->total) }}</td>
                        <td class="text-end">{{ round($row->avg_rating, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection
