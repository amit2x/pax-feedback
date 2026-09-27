<div class="admin-form">
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="name">Role Name <span style="color:red;">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $role->name ?? '') }}"
                required maxlength="80" placeholder="e.g. Security Manager" @disabled($isProtected ?? false)>
            @if (! empty($isProtected))
            <div class="form-text">This is a system role and cannot be renamed.</div>
            @endif
        </div>
    </div>

    <div class="admin-divider"></div>

    <h3 class="paf-admin-page-title mb-0" style="font-size:1rem; margin-bottom:0.5rem;">
        Permissions
    </h3>
    <p class="admin-page-subtitle" style="margin-bottom:1rem;">
        Select what users with this role can do.
    </p>

    <div class="paf-permission-groups">
        @foreach ($permissionGroups as $groupName => $permissions)
        @php
        // Check if any permission in this group is selected
        $groupHasSelection = count(array_intersect($permissions, $selectedPermissions)) > 0;
        $groupSlug = \Illuminate\Support\Str::slug($groupName);
        @endphp

        <div class="paf-permission-group" data-permission-group>
            <div class="paf-permission-group__header">
                <label class="paf-permission-group__toggle">
                    <input type="checkbox" class="form-check-input" data-group-toggle="{{ $groupSlug }}"
                        @checked($groupHasSelection) @disabled($isProtected ?? false)>
                    <span class="paf-permission-group__title">{{ $groupName }}</span>
                </label>
                <span class="paf-permission-group__count">
                    {{ count($permissions) }} permission{{ count($permissions) === 1 ? '' : 's' }}
                </span>
            </div>

            <div class="paf-permission-group__body">
                @foreach ($permissions as $permission)
                <label class="paf-permission-item">
                    <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission }}"
                        data-group-item="{{ $groupSlug }}" @checked(in_array($permission, $selectedPermissions, true))
                        @disabled($isProtected ?? false)>
                    <span class="paf-permission-item__label">
                        <code>{{ $permission }}</code>
                        <span class="paf-permission-item__human">
                            {{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $permission)) }}
                        </span>
                    </span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div class="admin-divider"></div>

    <div class="d-flex gap-2 mt-4">
        @if (empty($isProtected))
        <button type="submit" class="btn paf-btn-primary">Save Role</button>
        @endif
        <a href="{{ route('admin.roles.index') }}" class="btn paf-btn-outline">Cancel</a>
    </div>
</div>