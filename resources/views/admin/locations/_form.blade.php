<div class="paf-admin-form">
    <div class="row g-3">
        <div class="col-md-4">
            <label for="code">Code <span style="color:red;">*</span></label>
            <input type="text" id="code" name="code" class="form-control"
                   value="{{ old('code', $location->code ?? '') }}"
                   required maxlength="30" pattern="[A-Z0-9_-]+"
                   placeholder="e.g. T2-DEP-SEC-CP03">
        </div>

        <div class="col-md-4">
            <label for="name">Name <span style="color:red;">*</span></label>
            <input type="text" id="name" name="name" class="form-control"
                   value="{{ old('name', $location->name ?? '') }}"
                   required maxlength="120">
        </div>

        <div class="col-md-4">
            <label for="checkpoint_label">Checkpoint Label</label>
            <input type="text" id="checkpoint_label" name="checkpoint_label" class="form-control"
                   value="{{ old('checkpoint_label', $location->checkpoint_label ?? '') }}"
                   maxlength="60" placeholder="e.g. Checkpoint 03">
        </div>

        <div class="col-md-6">
            <label for="airport_id">Airport <span style="color:red;">*</span></label>
            <select id="airport_id" name="airport_id" class="form-select" required>
                <option value="">— Select —</option>
                @foreach ($airports as $a)
                    <option value="{{ $a->id }}"
                        @selected(old('airport_id', $location->airport_id ?? null) == $a->id)>
                        {{ $a->name }} ({{ $a->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="terminal_id">Terminal</label>
            <select id="terminal_id" name="terminal_id" class="form-select">
                <option value="">— None —</option>
                @foreach ($terminals as $t)
                    <option value="{{ $t->id }}"
                        @selected(old('terminal_id', $location->terminal_id ?? null) == $t->id)>
                        {{ $t->name }} ({{ $t->code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="zone_id">Zone</label>
            <select id="zone_id" name="zone_id" class="form-select">
                <option value="">— None —</option>
                @foreach ($zones as $z)
                    <option value="{{ $z->id }}"
                        @selected(old('zone_id', $location->zone_id ?? null) == $z->id)>
                        {{ $z->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label for="service_id">Service</label>
            <select id="service_id" name="service_id" class="form-select">
                <option value="">— None —</option>
                @foreach ($services as $s)
                    <option value="{{ $s->id }}"
                        @selected(old('service_id', $location->service_id ?? null) == $s->id)>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-8">
            <label for="description">Description</label>
            <input type="text" id="description" name="description" class="form-control"
                   value="{{ old('description', $location->description ?? '') }}"
                   maxlength="255">
        </div>

        <div class="col-md-4">
            <label for="sort_order">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control"
                   value="{{ old('sort_order', $location->sort_order ?? 0) }}" min="0">
        </div>

        <div class="col-md-4">
            <label>Status</label>
            <div class="form-check form-switch" style="padding-top:0.4rem;">
                <input class="form-check-input" type="checkbox" role="switch"
                       id="is_active" name="is_active" value="1"
                       @checked(old('is_active', $location->is_active ?? true))>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>

    <div class="admin-divider"></div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn paf-btn-primary">Save Location</button>
        <a href="{{ route('admin.locations.index') }}" class="btn paf-btn-outline">Cancel</a>
    </div>
</div>
