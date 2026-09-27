<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\FeedbackCategory;
use App\Models\FeedbackDepartment;
use App\Models\FeedbackSubcategory;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(Request $request): View
    {
        $categories = FeedbackCategory::active()->get();

        $query = FeedbackSubcategory::with(['category', 'defaultDepartment'])
            ->orderBy('sort_order')
            ->orderBy('name_en');

        // Optional filter by category
        $categoryFilter = $request->query('category_id');
        if ($categoryFilter && is_numeric($categoryFilter)) {
            $query->where('category_id', (int) $categoryFilter);
        }

        $subcategories = $query->get();

        return view('admin.subcategories.index', compact('subcategories', 'categories', 'categoryFilter'));
    }

    public function create(Request $request): View
    {
        $categories = FeedbackCategory::active()->get();
        $departments = FeedbackDepartment::orderBy('name')->get();

        // Preselect category if passed via ?category_id=
        $preselectedCategoryId = $request->query('category_id');

        return view('admin.subcategories.create', compact('categories', 'departments', 'preselectedCategoryId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSubcategory($request);

        $subcategory = FeedbackSubcategory::create([
            'uuid' => (string) Str::uuid(),
            'category_id' => $validated['category_id'],
            'code' => $validated['code'],
            'name_en' => $validated['name_en'],
            'name_hi' => $validated['name_hi'] ?? null,
            'name_bn' => $validated['name_bn'] ?? null,
            'default_department_id' => $validated['default_department_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit->log('subcategory.created', $subcategory, [], $subcategory->toArray());

        return redirect()
            ->route('admin.subcategories.index', ['category_id' => $subcategory->category_id])
            ->with('status', 'Subcategory created successfully.');
    }

    public function edit(FeedbackSubcategory $subcategory): View
    {
        $categories = FeedbackCategory::active()->get();
        $departments = FeedbackDepartment::orderBy('name')->get();

        return view('admin.subcategories.edit', compact('subcategory', 'categories', 'departments'));
    }

    public function update(Request $request, FeedbackSubcategory $subcategory): RedirectResponse
    {
        $validated = $this->validateSubcategory($request, $subcategory->id);
        $old = $subcategory->toArray();

        $subcategory->update([
            'category_id' => $validated['category_id'],
            'code' => $validated['code'],
            'name_en' => $validated['name_en'],
            'name_hi' => $validated['name_hi'] ?? null,
            'name_bn' => $validated['name_bn'] ?? null,
            'default_department_id' => $validated['default_department_id'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->audit->log('subcategory.updated', $subcategory, $old, $subcategory->fresh()->toArray());

        return redirect()
            ->route('admin.subcategories.index', ['category_id' => $subcategory->category_id])
            ->with('status', 'Subcategory updated successfully.');
    }

    public function destroy(FeedbackSubcategory $subcategory): RedirectResponse
    {
        // Safety: don't delete if feedback references it
        $inUse = Feedback::where('subcategory_id', $subcategory->id)->exists();
        if ($inUse) {
            return back()->withErrors([
                'subcategory' => 'Cannot delete: this subcategory is referenced by existing feedback. Disable it instead.',
            ]);
        }

        $categoryId = $subcategory->category_id;
        $this->audit->log('subcategory.deleted', $subcategory, $subcategory->toArray(), []);
        $subcategory->delete();

        return redirect()
            ->route('admin.subcategories.index', ['category_id' => $categoryId])
            ->with('status', 'Subcategory deleted.');
    }

    private function validateSubcategory(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:feedback_categories,id'],
            'code' => [
                'required', 'string', 'max:60', 'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('feedback_subcategories', 'code')
                    ->where('category_id', $request->input('category_id'))
                    ->ignore($ignoreId),
            ],
            'name_en' => ['required', 'string', 'max:120'],
            'name_hi' => ['nullable', 'string', 'max:120'],
            'name_bn' => ['nullable', 'string', 'max:120'],
            'default_department_id' => ['nullable', 'integer', 'exists:feedback_departments,id'],
            'sort_order' => ['nullable', 'integer', 'between:0,9999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
