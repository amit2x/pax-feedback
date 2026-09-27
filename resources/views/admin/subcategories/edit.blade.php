@extends('layouts.admin')

@section('title', 'Edit Subcategory')

@section('content')
<div class="paf-admin-page-header">
    <div>
        <h1 class="paf-admin-title mb-0">Edit Subcategory</h1>
        <div class="paf-admin-page-subtitle">
            {{ $subcategory->name_en }} ({{ $subcategory->code }})
            — {{ $subcategory->category?->name_en }}
        </div>
    </div>
    <a href="{{ route('admin.subcategories.index') }}" class="btn paf-btn-outline">← Back to list</a>
</div>

<div class="paf-card">
    <form method="POST" action="{{ route('admin.subcategories.update', $subcategory) }}">
        @csrf
        @method('PATCH')
        @include('admin.subcategories._form', ['subcategory' => $subcategory])
    </form>
</div>
@endsection