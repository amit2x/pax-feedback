@extends('layouts.admin')

@section('title', 'Categories')

@section('content')

   <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Categories</h1>
            <div class="paf-admin-page-subtitle">Manage feedback categories and their icons</div>
        </div>
        @can('categories.manage')
        <a href="{{ route('admin.categories.create') }}" class="btn paf-btn-primary btn-sm">
            + Add Category
        </a>
        @endcan
    </div>

    <div class="paf-card">
        <div style="overflow-x:auto;">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">Icon</th>
                        <th>Code</th>
                        <th>English</th>
                        <th>Hindi</th>
                        <th>Bengali</th>
                        <th>Department</th>
                        <th style="text-align:center;">Subs</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $cat)
                        <tr>
                            <td style="font-size:1.25rem;">{{ $cat->icon ?: '•' }}</td>
                            <td><code>{{ $cat->code }}</code></td>
                            <td>{{ $cat->name_en }}</td>
                            <td>{{ $cat->name_hi ?: '—' }}</td>
                            <td>{{ $cat->name_bn ?: '—' }}</td>
                            <td>{{ $cat->defaultDepartment?->name ?? '—' }}</td>
                            <td style="text-align:center;">{{ $cat->subcategories_count }}</td>
                            <td>
                                @if ($cat->is_active)
                                    <span class="admin-badge admin-badge--active">Active</span>
                                @else
                                    <span class="admin-badge admin-badge--inactive">Inactive</span>
                                @endif
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <a href="{{ route('admin.categories.edit', $cat) }}"
                                   class="btn btn-sm paf-btn-primary" >Edit</a>

                                <form method="POST"
                                      action="{{ route('admin.categories.destroy', $cat) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" >
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:2rem; color:var(--paf-text-muted);">
                                No categories yet. Click "Add Category" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
