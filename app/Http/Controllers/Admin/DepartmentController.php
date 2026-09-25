<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackDepartment;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(): View
    {
        $departments = FeedbackDepartment::orderBy('name')->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateDepartment($request);

        $department = FeedbackDepartment::create([
            'uuid' => (string) Str::uuid(),
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit->log('department.created', $department, [], $department->toArray());

        return redirect()->route('admin.departments.index')
            ->with('status', 'Department created successfully.');
    }

    public function edit(FeedbackDepartment $department): View
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, FeedbackDepartment $department): RedirectResponse
    {
        $validated = $this->validateDepartment($request, $department->id);
        $old = $department->toArray();

        $department->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit->log('department.updated', $department, $old, $department->fresh()->toArray());

        return redirect()->route('admin.departments.index')
            ->with('status', 'Department updated successfully.');
    }

    public function destroy(FeedbackDepartment $department): RedirectResponse
    {
        // Safety: check for references
        if ($department->categories()->exists()) {
            return back()->withErrors([
                'department' => 'Cannot delete: this department is assigned as default for one or more categories.',
            ]);
        }

        $this->audit->log('department.deleted', $department, $department->toArray(), []);
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('status', 'Department deleted.');
    }

    private function validateDepartment(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => [
                'required', 'string', 'max:30', 'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('feedback_departments', 'code')->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
