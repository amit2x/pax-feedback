@extends('layouts.admin')

@section('title', 'Permissions')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Permissions</h1>
        <div class="admin-page-subtitle">
            Atomic capabilities assigned to roles
        </div>
    </div>
    <a href="{{ route('admin.permissions.create') }}" class="btn paf-btn-primary">
        + Add Permission
    </a>
</div>

{{-- Group filter --}}
<div class="paf-card">
    <form method="GET" action="{{ route('admin.permissions.index') }}" class="paf-filter-form">
        <div class="paf-filter-field">
            <label for="group">Filter by Domain</label>
            <select id="group" name="group" class="form-select">
                <option value="">— All Domains —</option>
                @foreach ($allGroups as $group)
                <option value="{{ $group }}" @selected($groupFilter===$group)>
                    {{ $group }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="paf-filter-actions">
            <button type="submit" class="btn paf-btn-primary">Apply</button>
            @if ($groupFilter)
            <a href="{{ route('admin.permissions.index') }}" class="btn paf-btn-outline">Clear</a>
            @endif
        </div>
    </form>
</div>

@forelse ($grouped as $group => $permissions)
<div class="paf-card" style="padding:0; overflow:hidden; margin-bottom:1rem;">
    <div class="paf-permission-group__header" style="padding:0.9rem 1.1rem;">
        <span class="paf-permission-group__title">
            {{ $group }}
        </span>
        <span class="paf-permission-group__count">
            {{ $permissions->count() }} permission{{ $permissions->count() === 1 ? '' : 's' }}
        </span>
    </div>

    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Permission</th>
                    <th style="text-align:center;">Assigned Roles</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                @php $isLocked = $permission->roles_count > 0; @endphp
                <tr>
                    <td>
                        <code>{{ $permission->name }}</code>
                    </td>
                    <td style="text-align:center;">
                        @if ($isLocked)
                        <span style="font-weight:600; color:var(--adm-text);">
                            {{ $permission->roles_count }}
                        </span>
                        @else
                        <span style="color:var(--adm-text-muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($isLocked)
                        <span class="admin-badge admin-badge--active">
                            In Use
                        </span>
                        @else
                        <span class="admin-badge admin-badge--inactive">
                            Unused
                        </span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        @if ($isLocked)
                        <span style="font-size:0.8rem; color:var(--adm-text-muted);"
                            title="Remove from all roles to unlock">
                            Locked
                        </span>
                        @else
                        <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn paf-btn-outline"
                            style="font-size:0.8rem;">Edit</a>

                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}"
                            class="d-inline" onsubmit="return confirm('Delete permission {{ $permission->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="font-size:0.8rem;">
                                Delete
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="paf-card">
    <div class="paf-empty">
        <span class="paf-empty-icon">🔑</span>
        No permissions found.
    </div>
</div>
@endforelse
@endsection