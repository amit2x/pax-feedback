@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <a href="{{ route('admin.users.index') }}" class="small text-muted text-decoration-none">← Back</a>
        <h1 class="paf-admin-title mb-0 mt-1">Edit User</h1>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    @foreach ($errors->all() as $error)
    <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<div class="paf-card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')

        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label small">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required
                    maxlength="100">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required
                    maxlength="255">
            </div>
        </div>

        <div class="row g-2 mt-2">
            <div class="col-md-6">
                <label class="form-label small">New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" minlength="12">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="12">
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label small d-block mb-1">Roles</label>
            @php
            $userRoles = old('roles', $user->roles->pluck('name')->all());
            @endphp
            @foreach ($roles as $role)
            <div class="form-check form-check-inline">
                <input type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->name }}"
                    class="form-check-input" {{ in_array($role->name, $userRoles, true) ? 'checked' : '' }}>
                <label for="role-{{ $role->id }}" class="form-check-label">{{ $role->name }}</label>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            <button type="submit" class="btn paf-btn-primary">Save Changes</button>
            <a href="{{ route('admin.users.index') }}" class="btn paf-btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
