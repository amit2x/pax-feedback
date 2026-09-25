@extends('layouts.admin')

@section('title', 'Departments')

@section('content')

    <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Departments</h1>
            <div class="paf-admin-page-subtitle">Departments responsible for resolving feedback</div>
        </div>
         @can('departments.manage')
        <a href="{{ route('admin.departments.create') }}" class="btn paf-btn-primary btn-sm">+ Add Department</a>
        @endcan
    </div>

    <div class="paf-card" >
        <div style="overflow-x:auto;">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $dept)
                        <tr>
                            <td><code>{{ $dept->code }}</code></td>
                            <td>{{ $dept->name }}</td>
                            <td>{{ $dept->email ?: '—' }}</td>
                            <td>{{ $dept->contact_person ?: '—' }}</td>
                            <td>{{ $dept->contact_phone ?: '—' }}</td>
                            <td>
                                @if ($dept->is_active)
                                    <span class="admin-badge admin-badge--active">Active</span>
                                @else
                                    <span class="admin-badge admin-badge--inactive">Inactive</span>
                                @endif
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <a href="{{ route('admin.departments.edit', $dept) }}"
                                   class="btn btn-sm paf-btn-primary">Edit</a>
                                <form method="POST"
                                      action="{{ route('admin.departments.destroy', $dept) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this department?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" >Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:2rem; color:var(--adm-text-muted);">
                                No departments yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
