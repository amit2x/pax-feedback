<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackCategory;
use App\Models\FeedbackDepartment;
use App\Services\AuditLogger;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(): View
    {
        $departments = FeedbackDepartment::orderBy('name')->get();
        $query = FeedbackCategory::with(['defaultDepartment', 'subcategories'])
            ->orderBy('sort_order')
            ->orderBy('name_en');

        $departmentFilter = request()->query('department_id');
        if ($departmentFilter && is_numeric($departmentFilter)) {
            $query->where('default_department_id', (int) $departmentFilter);
        }

        $categories = $query->get();

        return view('admin.categories.index', compact('categories', 'departments', 'departmentFilter'));
    }

    // public function index(): View
    // {
    //     return view('admin.categories.index', [
    //         'categories' => FeedbackCategory::with(['defaultDepartment', 'subcategories'])
    //             ->orderBy('sort_order')
    //             ->get(),
    //         'departments' => FeedbackDepartment::orderBy('name')->get(),
    //     ]);
    // }

    public function create(): View
    {
        $departments = FeedbackDepartment::orderBy('name')->get();

        return view('admin.categories.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Log the incoming request payload to see what data arrived
        Log::info('Category store attempt started.', ['request_data' => $request->all()]);

        try {
            // 2. Validate data and catch if it fails here
            $validated = $this->validateCategory($request);
            Log::info('Validation passed successfully.', ['validated_data' => $validated]);

            // 3. Attempt database creation
            $category = FeedbackCategory::create([
                'uuid' => (string) Str::uuid(),
                'code' => $validated['code'],
                'name_en' => $validated['name_en'],
                'name_hi' => $validated['name_hi'] ?? null,
                'name_bn' => $validated['name_bn'] ?? null,
                'icon' => $validated['icon'] ?? null,
                'default_department_id' => $validated['default_department_id'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $request->boolean('is_active'),
            ]);

            Log::info('Category model created in memory/DB.', ['category_id' => $category->id ?? 'N/A']);

            // 4. Audit logging
            $this->audit->log('category.created', $category, [], $category->toArray());
            Log::info('Audit log saved.');

            return redirect()
                ->route('admin.categories.index')
                ->with('status', 'Category created successfully.');

        } catch (ValidationException $e) {
            // Captures validation failures (Laravel usually hides this by redirecting back automatically)
            Log::error('Validation failed!', [
                'errors' => $e->errors(),
                'inputs' => $request->all(),
            ]);

            // Re-throw so it redirects back with errors now that we logged it
            throw $e;
        } catch (Exception $e) {
            // Captures database issues, mass assignment errors, or system crashes
            Log::error('Category creation failed critically!', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create category: '.$e->getMessage());
        }
    }

    public function edit(FeedbackCategory $category): View
    {
        $departments = FeedbackDepartment::orderBy('name')->get();

        return view('admin.categories.edit', compact('category', 'departments'));
    }

    public function update(Request $request, FeedbackCategory $category): RedirectResponse
    {
        $validated = $this->validateCategory($request, $category->id);
        $old = $category->toArray();

        $category->update([
            'code' => $validated['code'],
            'name_en' => $validated['name_en'],
            'name_hi' => $validated['name_hi'] ?? null,
            'name_bn' => $validated['name_bn'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'default_department_id' => $validated['default_department_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit->log('category.updated', $category, $old, $category->fresh()->toArray());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category updated successfully.');
    }

    public function destroy(FeedbackCategory $category): RedirectResponse
    {
        // Safety: don't delete if subcategories exist
        if ($category->subcategories()->exists()) {
            return back()->withErrors([
                'category' => 'Cannot delete: this category still has subcategories. Delete them first.',
            ]);
        }

        $this->audit->log('category.deleted', $category, $category->toArray(), []);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category deleted.');
    }

    private function validateCategory(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => [
                'required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('feedback_categories', 'code')->ignore($ignoreId),
            ],
            'name_en' => ['required', 'string', 'max:100'],
            'name_hi' => ['nullable', 'string', 'max:100'],
            'name_bn' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:20'],
            'default_department_id' => ['nullable', 'integer', 'exists:feedback_departments,id'],
            'sort_order' => ['nullable', 'integer', 'between:0,9999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
