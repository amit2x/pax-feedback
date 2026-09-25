@extends('layouts.admin')

@section('title', 'Edit Department')

@section('content')
    <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Edit Department</h1>
            <div class="paf-admin-page-subtitle">{{ $department->name }} ({{ $department->code }})</div>
        </div>
        <a href="{{ route('admin.departments.index') }}" class="btn paf-btn-primary">← Back to list</a>
    </div>

    <div class="paf-card">
        <form method="POST" action="{{ route('admin.departments.update', $department) }}">
            @csrf
            @method('PATCH')
            @include('admin.departments._form', ['department' => $department])
        </form>
    </div>
@endsection
