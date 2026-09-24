@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<h1 class="paf-admin-title">Dashboard</h1>

<div class="paf-stat-grid">
    <div class="paf-stat-card">
        <div class="paf-stat-label">Total Feedback</div>
        <div class="paf-stat-value">{{ number_format($stats['total']) }}</div>
    </div>

    <div class="paf-stat-card paf-stat-card--accent">
        <div class="paf-stat-label">Today</div>
        <div class="paf-stat-value">{{ number_format($stats['today']) }}</div>
    </div>

    <div class="paf-stat-card">
        <div class="paf-stat-label">Average Rating</div>
        <div class="paf-stat-value">{{ $stats['avg_rating'] }}</div>
    </div>

    <div class="paf-stat-card paf-stat-card--danger">
        <div class="paf-stat-label">Complaints</div>
        <div class="paf-stat-value">{{ number_format($stats['complaints']) }}</div>
    </div>

    <div class="paf-stat-card paf-stat-card--info">
        <div class="paf-stat-label">Suggestions</div>
        <div class="paf-stat-value">{{ number_format($stats['suggestions']) }}</div>
    </div>

    <div class="paf-stat-card paf-stat-card--success">
        <div class="paf-stat-label">Compliments</div>
        <div class="paf-stat-value">{{ number_format($stats['compliments']) }}</div>
    </div>

    <div class="paf-stat-card">
        <div class="paf-stat-label">Open</div>
        <div class="paf-stat-value">{{ number_format($stats['open']) }}</div>
    </div>

    <div class="paf-stat-card">
        <div class="paf-stat-label">Resolved</div>
        <div class="paf-stat-value">{{ number_format($stats['resolved']) }}</div>
    </div>
</div>

<div class="paf-card mt-4">
    <h2 class="paf-step-title mb-3">Recent Feedback</h2>

    @if ($recentFeedback->isEmpty())
    <p class="text-muted mb-0">No feedback yet.</p>
    @else
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Type</th>
                    <th>Rating</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentFeedback as $fb)
                <tr>
                    <td><code>{{ $fb->reference_no }}</code></td>
                    <td>{{ ucfirst($fb->feedback_type) }}</td>
                    <td>{{ $fb->overall_rating }}/5</td>
                    <td>{{ $fb->category?->localizedName() ?? '—' }}</td>
                    <td>{{ $fb->location?->name ?? '—' }}</td>
                    <td>{{ $fb->submitted_at?->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
