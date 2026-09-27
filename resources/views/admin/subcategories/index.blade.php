@extends('layouts.admin')

@section('title', 'Subcategories')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Subcategories</h1>
        <div class="paf-admin-page-subtitle">
            Specific issues within each category
            @if ($categoryFilter)
            — filtered by category
            @endif
        </div>
    </div>
    <a href="{{ route('admin.subcategories.create', $categoryFilter ? ['category_id' => $categoryFilter] : []) }}"
        class=" btn paf-btn-primary">
        + Add Subcategory
    </a>
</div>

{{-- Category filter --}}
<div class="paf-card">
    <form method="GET" action="{{ route('admin.subcategories.index') }}" class="paf-filter-form">
        <div class="paf-filter-field">
            <label for="category_id">Filter by Category</label>
            <select id="category_id" name="category_id" class="form-select">
                <option value="">— All Categories —</option>
                @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected($categoryFilter==$cat->id)>
                    {{ $cat->icon ? $cat->icon . ' ' : '' }}{{ $cat->name_en }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="paf-filter-actions">
            <button type="submit" class="btn paf-btn-primary">Apply</button>
            @if ($categoryFilter)
            <a href="{{ route('admin.subcategories.index') }}" class="btn paf-btn-outline">Clear</a>
            @endif
        </div>
    </form>
</div>

<div class="paf-card" style="padding:0; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Code</th>
                    <th>English</th>
                    <th>Hindi</th>
                    <th>Bengali</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subcategories as $sub)
                <tr>
                    <td>
                        {{ $sub->category?->icon ? $sub->category->icon . ' ' : '' }}{{ $sub->category?->name_en ?? '—'
                        }}
                    </td>
                    <td><code>{{ $sub->code }}</code></td>
                    <td>{{ $sub->name_en }}</td>
                    <td>{{ $sub->name_hi ?: '—' }}</td>
                    <td>{{ $sub->name_bn ?: '—' }}</td>
                    <td>{{ $sub->defaultDepartment?->name ?? '—' }}</td>
                    <td>
                        @if ($sub->is_active)
                        <span class="paf-badge paf-badge-active">Active</span>
                        @else
                        <span class="paf-badge paf-badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <a href="{{ route('admin.subcategories.edit', $sub) }}" class="btn paf-btn-primary"
                            style="font-size:0.8rem;">Edit</a>

                        <form method="POST" action="{{ route('admin.subcategories.destroy', $sub) }}" class="d-inline"
                            onsubmit="return confirm('Delete this subcategory?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="font-size:0.8rem;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:2rem; color:var(--adm-text-muted);">
                        No subcategories found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection