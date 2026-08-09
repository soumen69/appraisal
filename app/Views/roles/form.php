<div class="row g-4">

    <input type="hidden" id="id" name="id">

    <div class="col-md-6">

        <div class="form-floating">

            <input
                type="text"
                class="form-control"
                id="name"
                name="name"
                placeholder="Role Name">

            <label for="name">
                Role Name <span class="text-danger">*</span>
            </label>

        </div>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-md-6">

        <div class="form-floating">

            <input
                type="text"
                class="form-control"
                id="slug"
                name="slug"
                placeholder="Slug">

            <label for="slug">
                Slug <span class="text-danger">*</span>
            </label>

        </div>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-md-6">

        <div class="form-floating">

            <input
                type="text"
                class="form-control"
                id="display_name"
                name="display_name"
                placeholder="Display Name">

            <label for="display_name">
                Display Name
            </label>

        </div>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-md-6">

        <div class="form-floating">

            <select
                class="form-select"
                id="parent_role_id"
                name="parent_role_id">

                <option value="">Root Role</option>

            </select>

            <label for="parent_role_id">
                Parent Role
            </label>

        </div>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-md-6">

        <div class="input-group">

            <span class="input-group-text">

                <i id="iconPreview" class="bi bi-person-badge"></i>

            </span>

            <div class="form-floating flex-grow-1">

                <input
                    type="text"
                    class="form-control"
                    id="icon"
                    name="icon"
                    placeholder="bi bi-person-badge">

                <label for="icon">
                    Bootstrap Icon
                </label>

            </div>

        </div>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-md-6">

        <div class="form-floating">

            <input
                type="color"
                class="form-control form-control-color w-100"
                id="color"
                name="color"
                value="#0d6efd">

            <label for="color">
                Theme Color
            </label>

        </div>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-md-6">

        <div class="form-floating">

            <input
                type="number"
                class="form-control"
                id="sort_order"
                name="sort_order"
                value="1">

            <label for="sort_order">
                Sort Order
            </label>

        </div>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-md-6">

        <div class="form-floating">

            <select
                class="form-select"
                id="status"
                name="status">

                <option value="active">
                    Active
                </option>

                <option value="inactive">
                    Inactive
                </option>

            </select>

            <label for="status">
                Status
            </label>

        </div>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-12">

        <div class="form-floating">

            <textarea
                class="form-control"
                id="description"
                name="description"
                placeholder="Description"
                style="height:120px"></textarea>

            <label for="description">
                Description
            </label>

        </div>

        <div class="invalid-feedback"></div>

    </div>

</div>