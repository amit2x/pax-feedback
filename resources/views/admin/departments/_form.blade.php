<div class="paf-admin-form">
    <div class="row g-3">
        <div class="col-md-3">
            <label for="code">Code <span style="color:red;">*</span></label>
            <input type="text" id="code" name="code" class="form-control"
                   value="{{ old('code', $department->code ?? '') }}"
                   required maxlength="30" pattern="[A-Z0-9_-]+"
                   placeholder="e.g. SEC">
        </div>

        <div class="col-md-5">
            <label for="name">Name <span style="color:red;">*</span></label>
            <input type="text" id="name" name="name" class="form-control"
                   value="{{ old('name', $department->name ?? '') }}"
                   required maxlength="120">
        </div>

        <div class="col-md-4">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="{{ old('email', $department->email ?? '') }}"
                   maxlength="255">
        </div>

        <div class="col-md-4">
            <label for="contact_person">Contact Person</label>
            <input type="text" id="contact_person" name="contact_person" class="form-control"
                   value="{{ old('contact_person', $department->contact_person ?? '') }}"
                   maxlength="120">
        </div>

        <div class="col-md-4">
            <label for="contact_phone">Contact Phone</label>
            <input type="text" id="contact_phone" name="contact_phone" class="form-control"
                   value="{{ old('contact_phone', $department->contact_phone ?? '') }}"
                   maxlength="20">
        </div>

        <div class="col-md-4">
            <label>Status</label>
            <div class="form-check form-switch" style="padding-top:0.4rem;">
                <input class="form-check-input" type="checkbox" role="switch"
                       id="is_active" name="is_active" value="1"
                       @checked(old('is_active', $department->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>

    <div class="admin-divider"></div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn paf-btn-primary">Save Department</button>
        <a href="{{ route('admin.departments.index') }}" class="btn paf-btn-outline">Cancel</a>
    </div>
</div>
