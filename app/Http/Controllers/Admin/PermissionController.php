<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Support\PermissionGroups;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(Request $request): View
    {
        $query = Permission::withCount(['roles'])
            ->orderBy('name');

        // Optional group filter (?group=feedback)
        $groupFilter = $request->query('group');
        if (is_string($groupFilter) && $groupFilter !== '') {
            $query->where('name', 'like', $groupFilter.'.%');
        }

        $permissions = $query->get();

        // Group them for display
        $grouped = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0] ?? 'other';
        });

        // Available groups for the filter dropdown
        $knownGroups = array_map(
            fn ($group) => strtolower(str_replace(' ', '_', $group)),
            array_keys(PermissionGroups::all())
        );

        // Merge in any custom groups not defined in PermissionGroups
        $allGroups = collect($grouped->keys())
            ->merge($knownGroups)
            ->unique()
            ->sort()
            ->values();

        return view('admin.permissions.index', [
            'grouped' => $grouped,
            'allGroups' => $allGroups,
            'groupFilter' => $groupFilter,
        ]);
    }

    public function create(): View
    {
        return view('admin.permissions.create', [
            'knownPrefixes' => $this->knownPrefixes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePermission($request);

        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->audit->log('permission.created', $permission, [], [
            'name' => $permission->name,
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('status', 'Permission created successfully.');
    }

    public function edit(Permission $permission): View
    {
        $roleCount = $permission->roles()->count();

        return view('admin.permissions.edit', [
            'permission' => $permission,
            'knownPrefixes' => $this->knownPrefixes(),
            'assignedRoleCount' => $roleCount,
            'isLocked' => $roleCount > 0,
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $roleCount = $permission->roles()->count();

        if ($roleCount > 0) {
            return back()->withErrors([
                'permission' => 'Cannot modify: this permission is assigned to '
                    .$roleCount.' role(s). Remove it from those roles first.',
            ]);
        }

        $validated = $this->validatePermission($request, $permission->id);
        $old = ['name' => $permission->name];

        $permission->update(['name' => $validated['name']]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->audit->log('permission.updated', $permission, $old, [
            'name' => $permission->fresh()->name,
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('status', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $roleCount = $permission->roles()->count();

        if ($roleCount > 0) {
            return back()->withErrors([
                'permission' => 'Cannot delete: this permission is assigned to '
                    .$roleCount.' role(s). Remove it from those roles first.',
            ]);
        }

        $this->audit->log('permission.deleted', $permission, [
            'name' => $permission->name,
        ], []);

        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('admin.permissions.index')
            ->with('status', 'Permission deleted.');
    }

    /**
     * @return array<int, string> Unique prefixes from all existing permissions.
     */
    private function knownPrefixes(): array
    {
        return Permission::query()
            ->selectRaw("DISTINCT SUBSTRING_INDEX(name, '.', 1) as prefix")
            ->orderBy('prefix')
            ->pluck('prefix')
            ->filter()
            ->values()
            ->all();
    }

    private function validatePermission(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z][a-z0-9_]*\.[a-z][a-z0-9_]*$/',
                Rule::unique('permissions', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($ignoreId),
            ],
        ], [
            'name.regex' => 'Permission names must follow the "domain.action" format (e.g. "feedback.view").',
        ]);
    }
}
