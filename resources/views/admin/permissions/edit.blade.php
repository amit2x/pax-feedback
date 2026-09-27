@extends('layouts.admin')

@section('title', 'Edit Permission')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Edit Permission</h1>
        <div class="paf-admin-page-subtitle">
            <code>{{ $permission->name }}</code>
        </div>
    </div>
    <a href="{{ route('admin.permissions.index') }}" class="btn paf-btn-outline">← Back to list</a>
</div>

<div class="paf-card">
    <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
        @csrf
        @method('PATCH')
        @include('admin.permissions._form', [
        'permission' => $permission,
        'isLocked' => $isLocked,
        'assignedRoleCount' => $assignedRoleCount,
        ])
    </form>
</div>
@endsection