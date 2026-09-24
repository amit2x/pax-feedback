<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackDepartment;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(): View
    {
        return view('admin.departments.index', [
            'departments' => FeedbackDepartment::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:feedback_departments,code'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $dept = FeedbackDepartment::create([
            'uuid' => (string) Str::uuid(),
            ...$validated,
            'is_active' => true,
        ]);

        $this->audit->log('department.created', $dept, [], $validated);

        return back()->with('status', 'Department created.');
    }

    public function update(Request $request, FeedbackDepartment $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        $department->update($validated);
        $this->audit->log('department.updated', $department, [], $validated);

        return back()->with('status', 'Department updated.');
    }

    public function destroy(FeedbackDepartment $department): RedirectResponse
    {
        $this->audit->log('department.deleted', $department);
        $department->delete();

        return back()->with('status', 'Department deleted.');
    }
}
