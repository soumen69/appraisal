<div class="row g-4">

    <input type="hidden" name="id" id="id">

    <div class="col-md-6">
        <label class="form-label">
            Module <span class="text-danger">*</span>
        </label>

        <select
            class="form-select"
            id="module_id"
            name="module_id">
        </select>

        <div class="invalid-feedback"></div>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Parent Menu
        </label>

        <select
            class="form-select"
            id="parent_id"
            name="parent_id">

            <option value="">
                Root Menu
            </option>

        </select>

        <div class="invalid-feedback"></div>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Menu Title <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            class="form-control"
            id="title"
            name="title">

        <div class="invalid-feedback"></div>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Route
        </label>

        <input
            type="text"
            class="form-control"
            id="route"
            name="route"
            placeholder="employees/index">
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Bootstrap Icon
        </label>

        <div class="input-group">

            <span class="input-group-text">
                <i id="iconPreview" class="bi bi-grid"></i>
            </span>

            <input
                type="text"
                class="form-control"
                id="icon"
                name="icon"
                placeholder="bi bi-people">
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Permission
        </label>

        <select
            class="form-select"
            id="permission_id"
            name="permission_id">

            <option value="">
                No Permission
            </option>

        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">
            Sort Order
        </label>

        <input
            type="number"
            class="form-control"
            name="sort_order"
            value="1"
            min="1">
    </div>

    <div class="col-md-4">
        <label class="form-label">
            Sidebar
        </label>

        <select
            class="form-select"
            name="is_sidebar">

            <option value="1">
                Yes
            </option>

            <option value="0">
                No
            </option>

        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">
            Visible
        </label>

        <select
            class="form-select"
            name="is_visible">

            <option value="1">
                Yes
            </option>

            <option value="0">
                No
            </option>

        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Status
        </label>

        <select
            class="form-select"
            name="status">

            <option value="active">
                Active
            </option>

            <option value="inactive">
                Inactive
            </option>

        </select>
    </div>

</div>