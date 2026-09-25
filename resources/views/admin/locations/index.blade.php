@extends('layouts.admin')

@section('title', 'Locations')

@section('content')
    <div class="paf-admin-page-header">
        <div>
            <h1 class="paf-admin-title mb-0">Locations</h1>
            <div class="paf-admin-page-subtitle">Physical points where feedback is collected</div>
        </div>
        @can('locations.manage')
        <a href="{{ route('admin.locations.create') }}" class="btn paf-btn-primary btn-sm">+ Add Location</a>
        @endcan
    </div>



    <div class="paf-card" >
        <div style="overflow-x:auto;">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Airport</th>
                        <th>Terminal</th>
                        <th>Zone</th>
                        <th>Service</th>
                        <th>Checkpoint</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($locations as $loc)
                        <tr>
                            <td><code>{{ $loc->code }}</code></td>
                            <td>{{ $loc->name }}</td>
                            <td>{{ $loc->airport?->code ?? '—' }}</td>
                            <td>{{ $loc->terminal?->code ?? '—' }}</td>
                            <td>{{ $loc->zone?->name ?? '—' }}</td>
                            <td>{{ $loc->service?->name ?? '—' }}</td>
                            <td>{{ $loc->checkpoint_label ?: '—' }}</td>
                            <td>
                                @if ($loc->is_active)
                                    <span class="admin-badge admin-badge--active">Active</span>
                                @else
                                    <span class="admin-badge admin-badge--inactive">Inactive</span>
                                @endif
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <a href="{{ route('admin.locations.edit', $loc) }}"
                                   class="btn btn-sm paf-btn-primary" >Edit</a>
                                <form method="POST"
                                      action="{{ route('admin.locations.destroy', $loc) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this location?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:2rem; color:var(--adm-text-muted);">
                                No locations yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
