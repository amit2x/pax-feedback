<div class="paf-admin-form">
    <div class="row g-3">
        <div class="col-md-6">
            <label for="category_id">Category <span style="color:red;">*</span></label>
            <select id="category_id" name="category_id" class="form-select" required>
                <option value="">— Select Category —</option>
                @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $subcategory->category_id ??
                    $preselectedCategoryId ?? null) == $cat->id)>
                    {{ $cat->icon ? $cat->icon . ' ' : '' }}{{ $cat->name_en }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="code">Code <span style="color:red;">*</span></label>
            <input type="text" id="code" name="code" class="form-control"
                value="{{ old('code', $subcategory->code ?? '') }}" required maxlength="60" pattern="[A-Z0-9_-]+"
                placeholder="e.g. WAIT">
            <div class="form-text">Unique within the selected category.</div>
        </div>

        <div class="col-md-3">
            <label for="sort_order">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control"
                value="{{ old('sort_order', $subcategory->sort_order ?? 0) }}" min="0" max="9999">
        </div>

        <div class="col-md-4">
            <label for="name_en">English Name <span style="color:red;">*</span></label>
            <input type="text" id="name_en" name="name_en" class="form-control"
                value="{{ old('name_en', $subcategory->name_en ?? '') }}" required maxlength="120">
        </div>

        <div class="col-md-4">
            <label for="name_hi">Hindi Name</label>
            <input type="text" id="name_hi" name="name_hi" class="form-control"
                value="{{ old('name_hi', $subcategory->name_hi ?? '') }}" maxlength="120">
        </div>

        <div class="col-md-4">
            <label for="name_bn">Bengali Name</label>
            <input type="text" id="name_bn" name="name_bn" class="form-control"
                value="{{ old('name_bn', $subcategory->name_bn ?? '') }}" maxlength="120">
        </div>

        <div class="col-md-8">
            <label for="default_department_id">Default Department</label>
            <select id="default_department_id" name="default_department_id" class="form-select">
                <option value="">— None —</option>
                @foreach ($departments as $dept)
                <option value="{{ $dept->id }}" @selected(old('default_department_id', $subcategory->
                    default_department_id ?? null) == $dept->id)>
                    {{ $dept->name }}
                </option>
                @endforeach
            </select>
            <div class="form-text">Overrides the category's default department for this specific issue.</div>
        </div>

        <div class="col-md-4">
            <label>Status</label>
            <div class="form-check form-switch" style="padding-top:0.4rem;">
                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                    @checked(old('is_active', $subcategory->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>

    <div class="admin-divider"></div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn paf-btn-primary">Save Subcategory</button>
        <a href="{{ route('admin.subcategories.index') }}" class="btn paf-btn-outline">Cancel</a>
    </div>
</div>