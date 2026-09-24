@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="paf-admin-page-header">
    <h1 class="paf-admin-title mb-0">Categories</h1>
    @can('categories.manage')
    <button type="button" class="btn paf-btn-primary btn-sm" data-toggle-target="#new-category-form">
        + Add Category
    </button>
    @endcan
</div>

@can('categories.manage')
<div class="paf-card mb-3" id="new-category-form" hidden>
    <h2 class="paf-step-title mb-3">New Category</h2>
    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="row g-2">
            <div class="col-md-2">
                <label class="form-label small">Code</label>
                <input type="text" name="code" class="form-control form-control-sm" required maxlength="40">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Name (English)</label>
                <input type="text" name="name_en" class="form-control form-control-sm" required maxlength="100">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Name (Hindi)</label>
                <input type="text" name="name_hi" class="form-control form-control-sm" maxlength="100">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Name (Bengali)</label>
                <input type="text" name="name_bn" class="form-control form-control-sm" maxlength="100">
            </div>
            <div class="col-md-1">
                <label class="form-label small">Icon</label>
                <input type="text" name="icon" class="form-control form-control-sm" maxlength="20" placeholder="🛡️">
            </div>
        </div>

        <div class="row g-2 mt-2">
            <div class="col-md-4">
                <label class="form-label small">Default Department</label>
                <select name="default_department_id" class="form-select form-select-sm">
                    <option value="">— None —</option>
                    @foreach ($departments as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn paf-btn-primary btn-sm">Create</button>
            <button type="button" class="btn paf-btn-outline btn-sm"
                data-toggle-target="#new-category-form">Cancel</button>
        </div>
    </form>
</div>
@endcan

<div class="paf-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Code</th>
                    <th>English</th>
                    <th>Hindi</th>
                    <th>Bengali</th>
                    <th>Department</th>
                    <th>Subs</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                <tr>
                    <td style="font-size:1.2rem;">{{ $cat->icon ?? '•' }}</td>
                    <td><code>{{ $cat->code }}</code></td>
                    <td class="fw-semibold">{{ $cat->name_en }}</td>
                    <td class="text-muted">{{ $cat->name_hi ?? '—' }}</td>
                    <td class="text-muted">{{ $cat->name_bn ?? '—' }}</td>
                    <td>{{ $cat->defaultDepartment?->name ?? '—' }}</td>
                    <td>{{ $cat->subcategories->count() }}</td>
                    <td>
                        @if ($cat->is_active)
                        <span class="paf-badge paf-badge--status-resolved">Active</span>
                        @else
                        <span class="paf-badge paf-badge--status-closed">Inactive</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="paf-empty">
                            <span class="paf-empty-icon">🗂️</span>
                            No categories yet.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
