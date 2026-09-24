@extends('layouts.admin')

@section('title', 'Departments')

@section('content')
<div class="paf-admin-page-header">
    <h1 class="paf-admin-title mb-0">Departments</h1>
    @can('departments.manage')
    <button type="button" class="btn paf-btn-primary btn-sm" data-toggle-target="#new-dept-form">+ Add
        Department</button>
    @endcan
</div>

@can('departments.manage')
<div class="paf-card mb-3" id="new-dept-form" hidden>
    <h2 class="paf-step-title mb-3">New Department</h2>
    <form method="POST" action="{{ route('admin.departments.store') }}">
        @csrf
        <div class="row g-2">
            <div class="col-md-2">
                <label class="form-label small">Code</label>
                <input type="text" name="code" class="form-control form-control-sm" required maxlength="30">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Name</label>
                <input type="text" name="name" class="form-control form-control-sm" required maxlength="120">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Email</label>
                <input type="email" name="email" class="form-control form-control-sm" maxlength="255">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Contact Person</label>
                <input type="text" name="contact_person" class="form-control form-control-sm" maxlength="120">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Phone</label>
                <input type="tel" name="contact_phone" class="form-control form-control-sm" maxlength="20">
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn paf-btn-primary btn-sm">Create</button>
            <button type="button" class="btn paf-btn-outline btn-sm" data-toggle-target="#new-dept-form">Cancel</button>
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
                    <th>Email</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departments as $d)
                <tr>
                    <td><code>{{ $d->code }}</code></td>
                    <td class="fw-semibold">{{ $d->name }}</td>
                    <td>{{ $d->email ?? '—' }}</td>
                    <td>{{ $d->contact_person ?? '—' }}</td>
                    <td>{{ $d->contact_phone ?? '—' }}</td>
                    <td>
                        @if ($d->is_active)
                        <span class="paf-badge paf-badge--status-resolved">Active</span>
                        @else
                        <span class="paf-badge paf-badge--status-closed">Inactive</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="paf-empty">
                            <span class="paf-empty-icon">🏢</span>
                            No departments yet.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
