@extends('layouts.admin')

@section('title', 'Locations')

@section('content')
<div class="paf-admin-page-header">
    <h1 class="paf-admin-title mb-0">Locations</h1>
    @can('locations.manage')
    <button type="button" class="btn paf-btn-primary btn-sm" data-toggle-target="#new-location-form">
        + Add Location
    </button>
    @endcan
</div>

@can('locations.manage')
<div class="paf-card mb-3" id="new-location-form" hidden>
    <h2 class="paf-step-title mb-3">New Location</h2>
    <form method="POST" action="{{ route('admin.locations.store') }}">
        @csrf
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label small">Code</label>
                <input type="text" name="code" class="form-control form-control-sm" required maxlength="30">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Name</label>
                <input type="text" name="name" class="form-control form-control-sm" required maxlength="120">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Checkpoint Label</label>
                <input type="text" name="checkpoint_label" class="form-control form-control-sm" maxlength="60"
                    placeholder="e.g. Checkpoint 03">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Airport</label>
                <select name="airport_id" class="form-select form-select-sm" required>
                    @foreach ($airports as $a)
                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-2 mt-2">
            <div class="col-md-3">
                <label class="form-label small">Terminal</label>
                <select name="terminal_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($terminals as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Zone</label>
                <select name="zone_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($zones as $z)
                    <option value="{{ $z->id }}">{{ $z->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Service</label>
                <select name="service_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($services as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn paf-btn-primary btn-sm">Create</button>
            <button type="button" class="btn paf-btn-outline btn-sm"
                data-toggle-target="#new-location-form">Cancel</button>
        </div>
    </form>
</div>
@endcan

<div class="paf-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Terminal</th>
                    <th>Zone</th>
                    <th>Service</th>
                    <th>Checkpoint</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($locations as $loc)
                <tr>
                    <td><code>{{ $loc->code }}</code></td>
                    <td class="fw-semibold">{{ $loc->name }}</td>
                    <td>{{ $loc->terminal?->name ?? '—' }}</td>
                    <td>{{ $loc->zone?->name ?? '—' }}</td>
                    <td>{{ $loc->service?->name ?? '—' }}</td>
                    <td class="text-muted">{{ $loc->checkpoint_label ?? '—' }}</td>
                    <td>
                        @if ($loc->is_active)
                        <span class="paf-badge paf-badge--status-resolved">Active</span>
                        @else
                        <span class="paf-badge paf-badge--status-closed">Inactive</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="paf-empty">
                            <span class="paf-empty-icon">📍</span>
                            No locations yet.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
