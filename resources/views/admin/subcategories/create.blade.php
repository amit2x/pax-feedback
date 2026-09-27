@extends('layouts.admin')

@section('title', 'Add Subcategory')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Add Subcategory</h1>
        <div class="paf-admin-page-subtitle">Create a specific issue within a category</div>
    </div>
    <a href="{{ route('admin.subcategories.index') }}" class="btn paf-btn-outline">← Back to list</a>
</div>

<div class="paf-card">
    <form method="POST" action="{{ route('admin.subcategories.store') }}">
        @csrf
        @include('admin.subcategories._form', ['subcategory' => null])
    </form>
</div>
@endsection