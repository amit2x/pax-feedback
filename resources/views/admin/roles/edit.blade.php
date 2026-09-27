@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Edit Role</h1>
        <div class="paf-admin-page-subtitle">{{ $role->name }}</div>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn paf-btn-outline">← Back to list</a>
</div>

<div class="paf-card">
    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf
        @method('PATCH')
        @include('admin.roles._form', [
        'role' => $role,
        'isProtected' => $isProtected,
        ])
    </form>
</div>
@endsection