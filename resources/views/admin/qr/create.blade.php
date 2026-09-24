@extends('layouts.admin')

@section('title', 'New QR Code')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <a href="{{ route('admin.qr.index') }}" class="small text-muted text-decoration-none">← Back</a>
        <h1 class="paf-admin-title mb-0 mt-1">New QR Code</h1>
    </div>
</div>

<div class="paf-card">
    <form method="POST" action="{{ route('admin.qr.store') }}">
        @csrf

        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label small">Type</label>
                <select name="type" class="form-select form-select-sm" required>
                    <option value="location">Location-based</option>
                    <option value="generic">Generic</option>
                </select>
            </div>

            <div class="col-md-5">
                <label class="form-label small">Name</label>
                <input type="text" name="name" class="form-control form-control-sm" required maxlength="120"
                    placeholder="e.g. Security Screening — Checkpoint 03">
            </div>

            <div class="col-md-4">
                <label class="form-label small">Expires At (optional)</label>
                <input type="datetime-local" name="expires_at" class="form-control form-control-sm">
            </div>
        </div>

        <div class="mt-2">
            <label class="form-label small">Description</label>
            <textarea name="description" class="form-control form-control-sm" rows="2" maxlength="500"></textarea>
        </div>

        <h3 class="h6 mt-4 mb-2">Location Binding (for location-based QRs)</h3>

        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small">Airport</label>
                <select name="airport_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($airports as $a)
                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Terminal</label>
                <select name="terminal_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($terminals as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Zone</label>
                <select name="zone_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($zones as $z)
                    <option value="{{ $z->id }}">{{ $z->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-2 mt-2">
            <div class="col-md-6">
                <label class="form-label small">Location</label>
                <select name="location_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($locations as $l)
                    <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Service</label>
                <select name="service_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($services as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn paf-btn-primary">Create QR Code</button>
            <a href="{{ route('admin.qr.index') }}" class="btn paf-btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
