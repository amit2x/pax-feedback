<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackCategory;
use App\Models\FeedbackDepartment;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(): View
    {
        $categories = FeedbackCategory::with('defaultDepartment')
            ->withCount('subcategories')
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->get();

        return view('admin.categories.index', compact('categories'));
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
        $validated = $this->validateCategory($request);

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

        $this->audit->log('category.created', $category, [], $category->toArray());

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Category created successfully.');
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
