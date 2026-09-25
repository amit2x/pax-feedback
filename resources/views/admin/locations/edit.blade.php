@extends('layouts.admin')

@section('title', 'Edit Location')

@section('content')
    <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Edit Location</h1>
            <div class="paf-admin-page-subtitle">{{ $location->name }} ({{ $location->code }})</div>
        </div>
        <a href="{{ route('admin.locations.index') }}" class="btn paf-btn-primary">← Back to list</a>
    </div>

    <div class="paf-card">
        <form method="POST" action="{{ route('admin.locations.update', $location) }}">
            @csrf
            @method('PATCH')
            @include('admin.locations._form', ['location' => $location])
        </form>
    </div>
@endsection
