<div class="row g-3">

    <!-- Organizations -->
    <div class="col-12">

        <label
            for="designationOrganizations"
            class="form-label">

            Organizations

            <span class="text-danger">*</span>

        </label>

        <select
            name="organization_ids[]"
            id="designationOrganizations"
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
            Select all organizations where this designation exists.
        </div>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Designation Code -->
    <div class="col-md-5">

        <label
            for="designationCode"
            class="form-label">

            Designation Code

        </label>

        <input
            type="text"
            id="designationCode"
            name="designation_code"
            class="form-control"
            maxlength="30"
            autocomplete="off"
            placeholder="e.g. SSE">

        <div class="invalid-feedback"></div>

    </div>


    <!-- Designation Title -->
    <div class="col-md-7">

        <label
            for="designationTitle"
            class="form-label">

            Designation Title

            <span class="text-danger">*</span>

        </label>

        <input
            type="text"
            id="designationTitle"
            name="title"
            class="form-control"
            maxlength="120"
            autocomplete="organization-title"
            placeholder="e.g. Senior Software Engineer"
            required>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Level -->
    <div class="col-md-6">

        <label
            for="designationLevel"
            class="form-label">

            Level

            <span class="text-danger">*</span>

        </label>

        <input
            type="number"
            id="designationLevel"
            name="level"
            class="form-control"
            min="1"
            max="100"
            value="1"
            required>

        <div class="form-text">
            Higher number means higher seniority.
        </div>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Status -->
    <div class="col-md-6">

        <label
            for="designationStatus"
            class="form-label">

            Status

        </label>

        <select
            id="designationStatus"
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


    <!-- Description -->
    <div class="col-12">

        <label
            for="designationDescription"
            class="form-label">

            Description

        </label>

        <textarea
            id="designationDescription"
            name="description"
            class="form-control"
            rows="4"
            maxlength="1000"
            placeholder="Briefly describe the role and responsibilities."></textarea>

        <div class="invalid-feedback"></div>

    </div>

</div>