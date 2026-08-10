<div class="row g-3">

    <!-- Organizations -->
    <div class="col-12">

        <label
            for="departmentOrganizations"
            class="form-label">

            Organizations

            <span class="text-danger">*</span>

        </label>

        <select
            name="organization_ids[]"
            id="departmentOrganizations"
            class="form-select"
            multiple
            required>

            <?php foreach ($organizations as $organization): ?>

                <option
                    value="<?= (int) $organization['id'] ?>">

                    <?= esc($organization['name']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <div class="form-text">
            Select all organizations where this department exists.
        </div>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Department Code -->
    <div class="col-md-6">

        <label
            for="departmentCode"
            class="form-label">

            Department Code

        </label>

        <input
            type="text"
            id="departmentCode"
            name="department_code"
            class="form-control"
            maxlength="30"
            autocomplete="off"
            placeholder="e.g. HR">

        <div class="invalid-feedback"></div>

    </div>


    <!-- Department Name -->
    <div class="col-md-6">

        <label
            for="departmentName"
            class="form-label">

            Department Name

            <span class="text-danger">*</span>

        </label>

        <input
            type="text"
            id="departmentName"
            name="name"
            class="form-control"
            maxlength="120"
            autocomplete="organization-title"
            placeholder="Enter department name"
            required>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Description -->
    <div class="col-12">

        <label
            for="departmentDescription"
            class="form-label">

            Description

        </label>

        <textarea
            id="departmentDescription"
            name="description"
            class="form-control"
            rows="4"
            placeholder="Enter department description"></textarea>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Status -->
    <div class="col-md-6">

        <label
            for="departmentStatus"
            class="form-label">

            Status

        </label>

        <select
            id="departmentStatus"
            name="status"
            class="form-select">

            <option value="active">
                Active
            </option>

            <option value="inactive">
                Inactive
            </option>

        </select>

        <div class="invalid-feedback"></div>

    </div>

</div>