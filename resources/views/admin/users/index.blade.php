@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="paf-admin-page-header">
    <h1 class="paf-admin-title mb-0">Users</h1>
    <a href="{{ route('admin.users.create') }}" class="btn paf-btn-primary btn-sm">+ New User</a>
</div>

@if (session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="paf-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($users as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach ($user->roles as $role)
                        <span class="paf-badge paf-badge--suggestion">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm paf-btn-outline">Edit</a>
                            @if ($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                data-confirm="Delete {{ $user->name }}?" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach --}}

                @forelse ($users as $user)
                <tr>
                    <td>
                        <strong>{{ $user->name }}</strong>
                        @if ($user->id === auth()->id())
                        <span style="font-size:0.7rem; color:var(--adm-text-muted);">(you)</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @forelse ($user->roles as $role)
                        <span class="admin-badge admin-badge--suggestion">{{ $role->name }}</span>
                        @empty
                        <span style="color:var(--adm-text-muted);">—</span>
                        @endforelse
                    </td>
                    <td style="font-size:0.82rem; color:var(--adm-text-muted);">
                        {{ $user->created_at?->format('d M Y') }}
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm paf-btn-primary">
                            Edit</a>

                        @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline"
                            onsubmit="return confirm('Delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:2rem; color:var(--adm-text-muted);">
                        No users yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection