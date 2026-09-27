<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(): View
    {
        $users = User::with('roles')->latest()->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::orderBy('name')->get(),
            'permissionsByRole' => $this->permissionMapByRole(),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
            'permissionsByRole' => $this->permissionMapByRole(),
        ]);
    }

    /**
     * Map of role name => [permission, ...] for the UI preview.
     */
    private function permissionMapByRole(): array
    {
        return Role::with('permissions')
            ->get()
            ->mapWithKeys(fn ($role) => [
                $role->name => $role->permissions->pluck('name')->all(),
            ])
            ->toArray();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['roles']);

        $this->audit->log('user.created', $user, [], [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->all(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:12', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $old = [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->all(),
        ];

        $user->name = $validated['name'];
        $user->email = strtolower($validated['email']);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles($validated['roles']);

        $this->audit->log('user.updated', $user, $old, [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->all(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        // Prevent deleting the last Super Admin
        if ($user->hasRole('Super Admin')
            && User::role('Super Admin')->count() <= 1) {
            return back()->withErrors(['user' => 'Cannot delete the last Super Admin.']);
        }

        $this->audit->log('user.deleted', $user, [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->all(),
        ], []);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'User deleted.');
    }

    /* public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['roles']);

        $this->audit->log('user.created', $user, [], ['email' => $user->email]);

        return redirect()->route('admin.users.index')
            ->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles($validated['roles']);

        $this->audit->log('user.updated', $user);

        return redirect()->route('admin.users.index')
            ->with('status', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        $this->audit->log('user.deleted', $user, ['email' => $user->email], []);
        $user->delete();

        return back()->with('status', 'User deleted.');
    } */
}
