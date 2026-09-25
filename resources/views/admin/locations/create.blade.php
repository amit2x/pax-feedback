@extends('layouts.admin')

@section('title', 'Add Location')

@section('content')
    <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Add Location</h1>
            <div class="paf-admin-page-subtitle">Create a new physical feedback point</div>
        </div>
        <a href="{{ route('admin.locations.index') }}" class="btn paf-btn-primary">← Back to list</a>
    </div>

    <div class="paf-card">
        <form method="POST" action="{{ route('admin.locations.store') }}">
            @csrf
            @include('admin.locations._form', ['location' => null])
        </form>
    </div>
@endsection
