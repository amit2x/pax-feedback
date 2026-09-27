@php
$isLocked = $isLocked ?? false;
$assignedRoleCount = $assignedRoleCount ?? 0;
@endphp

<div class="admin-form">
    @if ($isLocked)
    <div class="admin-alert admin-alert--danger" style="margin-bottom:1rem;">
        <strong>Locked.</strong>
        This permission is currently assigned to
        <strong>{{ $assignedRoleCount }}</strong> role(s).
        Remove it from those roles before editing or deleting.
    </div>
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            <label for="name">
                Permission Name <span style="color:red;">*</span>
            </label>
            <input type="text" id="name" name="name" class="form-control"
                value="{{ old('name', $permission->name ?? '') }}" required maxlength="100"
                pattern="[a-z][a-z0-9_]*\.[a-z][a-z0-9_]*" placeholder="e.g. reports.export" @disabled($isLocked)>
            <div class="form-text">
                Must follow the format <code>domain.action</code> — lowercase letters, digits, and underscores only.
                Examples: <code>feedback.view</code>, <code>qr.manage</code>, <code>reports.export</code>.
            </div>
        </div>

        @if (! $isLocked && ! empty($knownPrefixes))
        <div class="col-md-4">
            <label for="prefix-hint">Existing Domains</label>
            <select id="prefix-hint" class="form-select" data-prefix-hint>
                <option value="">— Pick to autofill prefix —</option>
                @foreach ($knownPrefixes as $prefix)
                <option value="{{ $prefix }}">{{ $prefix }}.</option>
                @endforeach
            </select>
            <div class="form-text">Reuse an existing domain to keep permissions grouped.</div>
        </div>
        @endif
    </div>

    <div class="admin-divider"></div>

    <div class="d-flex gap-2 mt-4">
        @if (! $isLocked)
        <button type="submit" class="btn paf-btn-primary">Save Permission</button>
        @endif
        <a href="{{ route('admin.permissions.index') }}" class="btn paf-btn-outline">Cancel</a>
    </div>
</div>