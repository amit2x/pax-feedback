@extends('layouts.admin')

@section('title', 'Add Permission')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Add Permission</h1>
        <div class="paf-admin-page-subtitle">
            Create a new atomic permission
        </div>
    </div>
    <a href="{{ route('admin.permissions.index') }}" class="btn paf-btn-outline">← Back to list</a>
</div>

<div class="paf-card">
    <form method="POST" action="{{ route('admin.permissions.store') }}">
        @csrf
        @include('admin.permissions._form', [
        'permission' => null,
        'isLocked' => false,
        'assignedRoleCount' => 0,
        ])
    </form>
</div>
@endsection