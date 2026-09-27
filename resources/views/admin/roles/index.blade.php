@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('content')

<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Roles & Permissions</h1>
        <div class="paf-admin-page-subtitle">Manage user roles and their permissions</div>
    </div>
    @can('roles.manage')
    <a href="{{ route('admin.roles.create') }}" class="btn paf-btn-primary btn-sm">+ New Role</a>
    @endcan
</div>


<div class="paf-card">
    <div style="overflow-x:auto;">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Role</th>
                    <th style="text-align:center;">Permissions</th>
                    <th style="text-align:center;">Users</th>
                    <th>Type</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                @php
                $isProtected = in_array($role->name, ['Super Admin'], true);
                @endphp
                <tr>
                    <td>
                        <strong>{{ $role->name }}</strong>
                    </td>
                    <td style="text-align:center;">
                        {{ $role->permissions_count }}
                    </td>
                    <td style="text-align:center;">
                        {{ $role->users_count }}
                    </td>
                    <td>
                        @if ($isProtected)
                        <span class="admin-badge admin-badge--active">System</span>
                        @else
                        <span class="admin-badge admin-badge--inactive">Custom</span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        @if ($isProtected)
                        <span style="font-size:0.8rem; color:var(--adm-text-muted);">
                            Protected
                        </span>
                        @else
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm paf-btn-primary">Edit</a>

                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline"
                            onsubmit="return confirm('Delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        No roles defined.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection