@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
    <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Edit Category</h1>
            <div class="paf-admin-page-subtitle">{{ $category->name_en }} ({{ $category->code }})</div>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn paf-btn-primary">← Back to list</a>
    </div>

    <div class="paf-card">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PATCH')
            @include('admin.categories._form', ['category' => $category])
        </form>
    </div>
@endsection
