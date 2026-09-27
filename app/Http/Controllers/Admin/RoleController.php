<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Support\PermissionGroups;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const PROTECTED_ROLES = ['Super Admin'];

    public function __construct(private AuditLogger $audit) {}

    public function index(): View
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'permissionGroups' => PermissionGroups::all(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRole($request);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        $this->audit->log('role.created', $role, [], [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->all(),
        ]);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'permissionGroups' => PermissionGroups::all(),
            'selectedPermissions' => $role->permissions->pluck('name')->all(),
            'isProtected' => in_array($role->name, self::PROTECTED_ROLES, true),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return back()->withErrors([
                'role' => 'The "'.$role->name.'" role is protected and cannot be modified.',
            ]);
        }

        $validated = $this->validateRole($request, $role->id);
        $old = [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->all(),
        ];

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        $this->audit->log('role.updated', $role, $old, [
            'name' => $role->fresh()->name,
            'permissions' => $role->fresh()->permissions->pluck('name')->all(),
        ]);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return back()->withErrors([
                'role' => 'The "'.$role->name.'" role cannot be deleted.',
            ]);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors([
                'role' => 'Cannot delete: this role is assigned to '.$role->users()->count().' user(s). Reassign them first.',
            ]);
        }

        $this->audit->log('role.deleted', $role, [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->all(),
        ], []);

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role deleted.');
    }

    private function validateRole(Request $request, ?int $ignoreId = null): array
    {
        $allPermissions = PermissionGroups::flat();

        return $request->validate([
            'name' => [
                'required', 'string', 'max:80',
                Rule::unique('roles', 'name')->ignore($ignoreId),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($allPermissions)],
        ]);
    }
}
