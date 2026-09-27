@extends('layouts.admin')

@section('title', 'Add Role')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Add Role</h1>
        <div class="paf-admin-page-subtitle">Create a new role and assign permissions</div>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn paf-btn-outline">← Back to list</a>
</div>

<div class="paf-card">
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        @include('admin.roles._form', ['role' => null, 'isProtected' => false])
    </form>
</div>
@endsection