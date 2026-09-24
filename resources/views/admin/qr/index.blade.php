@extends('layouts.admin')

@section('title', 'QR Codes')

@section('content')
<div class="paf-admin-page-header">
    <h1 class="paf-admin-title mb-0">QR Codes</h1>
    @can('qr.manage')
    <a href="{{ route('admin.qr.create') }}" class="btn paf-btn-primary btn-sm">+ New QR Code</a>
    @endcan
</div>

@if (session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($qrCodes->isEmpty())
<div class="paf-card">
    <div class="paf-empty">
        <span class="paf-empty-icon">🔳</span>
        No QR codes yet. Create one to get started.
    </div>
</div>
@else
<div class="paf-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Usage</th>
                    <th>Last Used</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($qrCodes as $qr)
                <tr>
                    <td>
                        <a href="{{ route('admin.qr.show', $qr->uuid) }}" class="text-decoration-none fw-semibold">
                            {{ $qr->name }}
                        </a>
                        @if ($qr->description)
                        <div class="text-muted small">{{ \Illuminate\Support\Str::limit($qr->description, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="paf-badge paf-badge--suggestion">
                            {{ ucfirst($qr->type) }}
                        </span>
                    </td>
                    <td>{{ $qr->location?->name ?? '—' }}</td>
                    <td>
                        @php
                        $statusClass = match ($qr->status) {
                        'active' => 'status-resolved',
                        'inactive' => 'status-closed',
                        'expired' => 'priority-high',
                        'compromised' => 'priority-critical',
                        default => 'status-submitted',
                        };
                        @endphp
                        <span class="paf-badge paf-badge--{{ $statusClass }}">
                            {{ ucfirst($qr->status) }}
                        </span>
                    </td>
                    <td>{{ number_format($qr->usage_count) }}</td>
                    <td class="text-muted small">
                        {{ $qr->last_used_at?->diffForHumans() ?? 'Never' }}
                    </td>

                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('admin.qr.show', $qr->uuid) }}"
                                class="btn btn-sm paf-btn-primary">View</a>

                            @can('qr.manage')
                            @if ($qr->status === 'active')
                            <form method="POST" action="{{ route('admin.qr.status', $qr->uuid) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="inactive">
                                <button type="submit" class="btn btn-sm paf-btn-outline">Disable</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.qr.status', $qr->uuid) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="active">
                                <button type="submit" class="btn btn-sm paf-btn-outline">Enable</button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('admin.qr.regenerate', $qr->uuid) }}"
                                data-confirm="Regenerate this QR code? The old printed code will stop working."
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm paf-btn-outline">Regenerate</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
