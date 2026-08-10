<?= csrf_field() ?>

<div class="row g-3">

    <!-- Organization -->
    <div class="col-md-6">

        <label
            for="branchOrganization"
            class="form-label">

            Organization

            <span class="text-danger">*</span>

        </label>

        <select
            id="branchOrganization"
            name="organization_id"
            class="form-select"
            required>

            <option value="">
                Select organization
            </option>

            <?php foreach ($organizations as $organization): ?>

                <option
                    value="<?= (int) $organization['id'] ?>">

                    <?= esc($organization['name']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Branch Name -->
    <div class="col-md-6">

        <label
            for="branchName"
            class="form-label">

            Branch Name

            <span class="text-danger">*</span>

        </label>

        <input
            type="text"
            id="branchName"
            name="name"
            class="form-control"
            maxlength="150"
            autocomplete="organization"
            placeholder="Enter branch name"
            required>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Branch Code -->
    <div class="col-md-6">

        <label
            for="branchCode"
            class="form-label">

            Branch Code

        </label>

        <input
            type="text"
            id="branchCode"
            name="branch_code"
            class="form-control"
            maxlength="30"
            autocomplete="off"
            placeholder="e.g. 17B">

        <div class="form-text">
            Unique within the organization.
        </div>

        <div class="invalid-feedback"></div>

    </div>


    <!-- Status -->
    <div class="col-md-6">

        <label
            for="branchStatus"
            class="form-label">

            Status

        </label>

        <select
            id="branchStatus"
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


    <!-- Email -->
    <div class="col-md-6">

        <label
            for="branchEmail"
            class="form-label">

            Email

        </label>

        <input
            type="email"
            id="branchEmail"
            name="email"
            class="form-control"
            maxlength="150"
            autocomplete="email"
            placeholder="branch@example.com">

        <div class="invalid-feedback"></div>

    </div>


    <!-- Phone -->
    <div class="col-md-6">

        <label
            for="branchPhone"
            class="form-label">

            Phone

        </label>

        <input
            type="text"
            id="branchPhone"
            name="phone"
            class="form-control"
            maxlength="30"
            autocomplete="tel"
            placeholder="Contact number">

        <div class="invalid-feedback"></div>

    </div>


    <!-- Address -->
    <div class="col-12">

        <label
            for="branchAddress"
            class="form-label">

            Address

        </label>

        <textarea
            id="branchAddress"
            name="address"
            class="form-control"
            rows="3"
            maxlength="500"
            autocomplete="street-address"
            placeholder="Full branch address"></textarea>

        <div class="invalid-feedback"></div>

    </div>


    <!-- City -->
    <div class="col-md-4">

        <label
            for="branchCity"
            class="form-label">

            City

        </label>

        <input
            type="text"
            id="branchCity"
            name="city"
            class="form-control"
            maxlength="100"
            autocomplete="address-level2"
            placeholder="City">

        <div class="invalid-feedback"></div>

    </div>


    <!-- State -->
    <div class="col-md-4">

        <label
            for="branchState"
            class="form-label">

            State

        </label>

        <input
            type="text"
            id="branchState"
            name="state"
            class="form-control"
            maxlength="100"
            autocomplete="address-level1"
            placeholder="State">

        <div class="invalid-feedback"></div>

    </div>


    <!-- Country -->
    <div class="col-md-4">

        <label
            for="branchCountry"
            class="form-label">

            Country

        </label>

        <input
            type="text"
            id="branchCountry"
            name="country"
            class="form-control"
            maxlength="100"
            autocomplete="country-name"
            value="India"
            placeholder="Country">

        <div class="invalid-feedback"></div>

    </div>


    <!-- Postal Code -->
    <div class="col-md-6">

        <label
            for="branchPostalCode"
            class="form-label">

            Postal Code

        </label>

        <input
            type="text"
            id="branchPostalCode"
            name="postal_code"
            class="form-control"
            maxlength="20"
            autocomplete="postal-code"
            placeholder="Postal code">

        <div class="invalid-feedback"></div>

    </div>

</div>