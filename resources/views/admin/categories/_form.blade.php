<div class="paf-admin-form">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="code">Code <span style="color:red;">*</span></label>
            <input type="text" id="code" name="code" class="form-control"
                   value="{{ old('code', $category->code ?? '') }}"
                   required maxlength="40" pattern="[A-Z0-9_-]+"
                   placeholder="e.g. SEC">
            <div class="form-text">Uppercase letters, numbers, dash, underscore only.</div>
        </div>

        <div class="col-md-4">
            <label for="icon">Icon (emoji)</label>
            <input type="text" id="icon" name="icon" class="form-control"
                   value="{{ old('icon', $category->icon ?? '') }}"
                   maxlength="20" placeholder="🛡️">
        </div>

        <div class="col-md-4">
            <label for="sort_order">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control"
                   value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                   min="0" max="9999">
        </div>

        <div class="col-md-4">
            <label for="name_en">English Name <span style="color:red;">*</span></label>
            <input type="text" id="name_en" name="name_en" class="form-control"
                   value="{{ old('name_en', $category->name_en ?? '') }}"
                   required maxlength="100">
        </div>

        <div class="col-md-4">
            <label for="name_hi">Hindi Name</label>
            <input type="text" id="name_hi" name="name_hi" class="form-control"
                   value="{{ old('name_hi', $category->name_hi ?? '') }}"
                   maxlength="100">
        </div>

        <div class="col-md-4">
            <label for="name_bn">Bengali Name</label>
            <input type="text" id="name_bn" name="name_bn" class="form-control"
                   value="{{ old('name_bn', $category->name_bn ?? '') }}"
                   maxlength="100">
        </div>

        <div class="col-md-8">
            <label for="default_department_id">Default Department</label>
            <select id="default_department_id" name="default_department_id" class="form-select">
                <option value="">— None —</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}"
                        @selected(old('default_department_id', $category->default_department_id ?? null) == $dept->id)>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">Used for routing when a feedback of this category is submitted.</div>
        </div>

        <div class="col-md-4">
            <label>Status</label>
            <div class="form-check form-switch" style="padding-top:0.4rem;">
                <input class="form-check-input" type="checkbox" role="switch"
                       id="is_active" name="is_active" value="1"
                       @checked(old('is_active', $category->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>

    <div class="paf-divider"></div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn paf-btn-primary">Save Category</button>
        <a href="{{ route('admin.categories.index') }}" class="btn paf-btn-outline">Cancel</a>
    </div>
</div>
