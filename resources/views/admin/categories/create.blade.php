@extends('layouts.admin')

@section('title', 'Add Category')

@section('content')
    <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Add Category</h1>
            <div class="admin-page-subtitle">Create a new feedback category</div>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn paf-btn-primary">← Back to list</a>
    </div>

    <div class="paf-card">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            @include('admin.categories._form', ['category' => null])
        </form>
    </div>
@endsection
