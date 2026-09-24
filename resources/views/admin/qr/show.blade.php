@extends('layouts.admin')

@section('title', 'QR — ' . $qrCode->name)

@section('content')
<div class="paf-admin-page-header">
    <div>
        <a href="{{ route('admin.qr.index') }}" class="small text-muted text-decoration-none">← Back to QR Codes</a>
        <h1 class="paf-admin-title mb-0 mt-1">{{ $qrCode->name }}</h1>
        @if ($qrCode->description)
        <p class="text-muted small mb-0 mt-1">{{ $qrCode->description }}</p>
        @endif
    </div>
</div>

@if (session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

@if (! $hasToken)
<div class="alert alert-warning">
    This QR code was created before token storage was enabled.
    <strong>Regenerate it</strong> to enable viewing and downloading.
</div>
@endif

<div class="row g-3">
    {{-- LEFT: QR image --}}
    <div class="col-lg-6">
        <div class="paf-card text-center">
            <h2 class="paf-step-title mb-3">QR Code</h2>


            @if ($hasToken)
            <img src="{{ route('admin.qr.image', ['qrCode' => $qrCode->uuid, 'size' => 400, 'logo' => 1]) }}"
                alt="QR Code" width="400" height="400"
                style="max-width:100%; height:auto; margin:0 auto; display:block;">

            <p class="small text-muted mt-3 mb-0">
                Preview at 400px. Download for print-quality SVG.
            </p>
            @else
            <div class="paf-empty">
                <span class="paf-empty-icon">🔳</span>
                <p class="mb-0">No preview available.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: Metadata + actions --}}
    <div class="col-lg-6">
        <div class="paf-card mb-3">
            <h2 class="paf-step-title mb-3">Details</h2>

            <div class="paf-detail-grid">
                <div class="paf-detail-item">
                    <span class="paf-detail-label">Type</span>
                    <span class="paf-detail-value">{{ ucfirst($qrCode->type) }}</span>
                </div>

                <div class="paf-detail-item">
                    <span class="paf-detail-label">Status</span>
                    <span class="paf-detail-value">
                        <span
                            class="paf-badge paf-badge--{{ $qrCode->status === 'active' ? 'status-resolved' : 'status-closed' }}">
                            {{ ucfirst($qrCode->status) }}
                        </span>
                    </span>
                </div>

                <div class="paf-detail-item">
                    <span class="paf-detail-label">Location</span>
                    <span class="paf-detail-value">{{ $qrCode->location?->name ?? '—' }}</span>
                </div>

                <div class="paf-detail-item">
                    <span class="paf-detail-label">Terminal</span>
                    <span class="paf-detail-value">{{ $qrCode->terminal?->name ?? '—' }}</span>
                </div>

                <div class="paf-detail-item">
                    <span class="paf-detail-label">Service</span>
                    <span class="paf-detail-value">{{ $qrCode->service?->name ?? '—' }}</span>
                </div>

                <div class="paf-detail-item">
                    <span class="paf-detail-label">Usage Count</span>
                    <span class="paf-detail-value">{{ number_format($qrCode->usage_count) }}</span>
                </div>

                <div class="paf-detail-item">
                    <span class="paf-detail-label">Last Used</span>
                    <span class="paf-detail-value">{{ $qrCode->last_used_at?->diffForHumans() ?? 'Never' }}</span>
                </div>

                <div class="paf-detail-item">
                    <span class="paf-detail-label">Expires</span>
                    <span class="paf-detail-value">
                        {{ $qrCode->expires_at?->format('d M Y, H:i') ?? 'Never' }}
                    </span>
                </div>
            </div>
        </div>

        @if ($hasToken)
        <div class="paf-card mb-3">
            <h2 class="paf-step-title mb-3">Public URL</h2>
            <div class="paf-comment-block" style="font-size:0.8rem; word-break:break-all;">
                {{ $feedbackUrl }}
            </div>
            <button type="button" class="btn paf-btn-outline btn-sm mt-2" data-copy="{{ $feedbackUrl }}">
                Copy URL
            </button>
        </div>

        <div class="paf-card mb-3">
            <h2 class="paf-step-title mb-3">Download</h2>
            <p class="small text-muted mb-2">
                SVG files scale infinitely — ideal for printing on posters, kiosk stickers, or banners.
            </p>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.qr.download', ['qrCode' => $qrCode->uuid, 'size' => 600, 'logo' => 1]) }}"
                    class="btn paf-btn-primary btn-sm">
                    ⬇ Download with Logo (600px)
                </a>
                <a href="{{ route('admin.qr.download', ['qrCode' => $qrCode->uuid, 'size' => 600, 'logo' => 0]) }}"
                    class="btn paf-btn-outline btn-sm">
                    Download without Logo
                </a>
                <a href="{{ route('admin.qr.download', ['qrCode' => $qrCode->uuid, 'size' => 1200, 'logo' => 1]) }}"
                    class="btn paf-btn-outline btn-sm">
                    Download Hi-Res (1200px)
                </a>
            </div>
        </div>

        <div class="paf-card mb-3">
            <h2 class="paf-step-title mb-3">Download PNG</h2>
            <p class="small text-muted mb-2">
                PNG files work everywhere — print, kiosk stickers, posters, PDF. Higher resolution = larger file.
            </p>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.qr.download', ['qrCode' => $qrCode->uuid, 'size' => 800, 'logo' => 1]) }}"
                    class="btn paf-btn-primary btn-sm">
                    ⬇ Download with Logo (800px)
                </a>
                <a href="{{ route('admin.qr.download', ['qrCode' => $qrCode->uuid, 'size' => 1600, 'logo' => 0]) }}"
                    class="btn paf-btn-outline btn-sm">
                    Without Logo (1600px)
                </a>
                <a href="{{ route('admin.qr.download', ['qrCode' => $qrCode->uuid, 'size' => 1600, 'logo' => 1]) }}"
                    class="btn paf-btn-outline btn-sm">
                    Hi-Res (1600px)
                </a>
            </div>
        </div>

        <div class="paf-card">
            <h2 class="paf-step-title mb-3">Danger Zone</h2>

            <div class="d-flex flex-wrap gap-2">
                @can('qr.manage')
                <form method="POST" action="{{ route('admin.qr.regenerate', $qrCode->uuid) }}"
                    data-confirm="Regenerate? The old printed QR code will stop working immediately." class="d-inline">
                    @csrf
                    <button type="submit" class="btn paf-btn-outline btn-sm">Regenerate Token</button>
                </form>

                @if ($qrCode->status === 'active')
                <form method="POST" action="{{ route('admin.qr.status', $qrCode->uuid) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="inactive">
                    <button type="submit" class="btn btn-outline-warning btn-sm">Disable</button>
                </form>
                @else
                <form method="POST" action="{{ route('admin.qr.status', $qrCode->uuid) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn btn-outline-success btn-sm">Enable</button>
                </form>
                @endif
                @endcan
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
