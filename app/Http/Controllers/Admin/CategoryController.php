<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackCategory;
use App\Models\FeedbackDepartment;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => FeedbackCategory::with(['defaultDepartment', 'subcategories'])
                ->orderBy('sort_order')
                ->get(),
            'departments' => FeedbackDepartment::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:40', 'unique:feedback_categories,code'],
            'name_en' => ['required', 'string', 'max:100'],
            'name_hi' => ['nullable', 'string', 'max:100'],
            'name_bn' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:20'],
            'default_department_id' => ['nullable', 'exists:feedback_departments,id'],
        ]);

        $category = FeedbackCategory::create([
            'uuid' => (string) Str::uuid(),
            ...$validated,
            'sort_order' => (int) FeedbackCategory::max('sort_order') + 1,
            'is_active' => true,
        ]);

        $this->audit->log('category.created', $category, [], $validated);

        return back()->with('status', 'Category created.');
    }

    public function update(Request $request, FeedbackCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:100'],
            'name_hi' => ['nullable', 'string', 'max:100'],
            'name_bn' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'default_department_id' => ['nullable', 'exists:feedback_departments,id'],
        ]);

        $old = $category->only(array_keys($validated));
        $category->update($validated);

        $this->audit->log('category.updated', $category, $old, $validated);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(FeedbackCategory $category): RedirectResponse
    {
        $this->audit->log('category.deleted', $category);
        $category->delete();

        return back()->with('status', 'Category deleted.');
    }
}
