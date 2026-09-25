@extends('layouts.admin')

@section('title', 'Add Department')

@section('content')
    <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Add Department</h1>
            <div class="paf-admin-page-subtitle">Create a new department</div>
        </div>
        <a href="{{ route('admin.departments.index') }}" class="btn paf-btn-primary">← Back to list</a>
    </div>

    <div class="paf-card">
        <form method="POST" action="{{ route('admin.departments.store') }}">
            @csrf
            @include('admin.departments._form', ['department' => null])
        </form>
    </div>
@endsection
