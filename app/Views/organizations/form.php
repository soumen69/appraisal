<div class="organization-form">

    <div class="row g-3">

        <!-- Organization Code -->
        <div class="col-md-4">

            <label
                for="organizationCode"
                class="form-label">

                Organization Code

            </label>

            <input
                type="text"
                id="organizationCode"
                name="organization_code"
                class="form-control"
                maxlength="30"
                autocomplete="off"
                placeholder="e.g. ACME">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Organization Name -->
        <div class="col-md-8">

            <label
                for="organizationName"
                class="form-label">

                Organization Name

                <span class="text-danger">*</span>

            </label>

            <input
                type="text"
                id="organizationName"
                name="name"
                class="form-control"
                maxlength="150"
                autocomplete="organization"
                placeholder="Enter organization name"
                required>

            <div class="invalid-feedback"></div>

        </div>


        <!-- Legal Name -->
        <div class="col-12">

            <label
                for="organizationLegalName"
                class="form-label">

                Legal Name

            </label>

            <input
                type="text"
                id="organizationLegalName"
                name="legal_name"
                class="form-control"
                maxlength="200"
                autocomplete="organization"
                placeholder="Registered/legal organization name">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Email -->
        <div class="col-md-6">

            <label
                for="organizationEmail"
                class="form-label">

                Email

            </label>

            <input
                type="email"
                id="organizationEmail"
                name="email"
                class="form-control"
                maxlength="150"
                autocomplete="email"
                placeholder="contact@example.com">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Phone -->
        <div class="col-md-6">

            <label
                for="organizationPhone"
                class="form-label">

                Phone

            </label>

            <input
                type="text"
                id="organizationPhone"
                name="phone"
                class="form-control"
                maxlength="30"
                autocomplete="tel"
                placeholder="Contact number">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Website -->
        <div class="col-md-6">

            <label
                for="organizationWebsite"
                class="form-label">

                Website

            </label>

            <input
                type="url"
                id="organizationWebsite"
                name="website"
                class="form-control"
                maxlength="255"
                autocomplete="url"
                placeholder="https://example.com">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Country -->
        <div class="col-md-6">

            <label
                for="organizationCountry"
                class="form-label">

                Country

            </label>

            <input
                type="text"
                id="organizationCountry"
                name="country"
                class="form-control"
                maxlength="100"
                autocomplete="country-name"
                value="India">

            <div class="invalid-feedback"></div>

        </div>


        <!-- City -->
        <div class="col-md-4">

            <label
                for="organizationCity"
                class="form-label">

                City

            </label>

            <input
                type="text"
                id="organizationCity"
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
                for="organizationState"
                class="form-label">

                State

            </label>

            <input
                type="text"
                id="organizationState"
                name="state"
                class="form-control"
                maxlength="100"
                autocomplete="address-level1"
                placeholder="State">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Postal Code -->
        <div class="col-md-4">

            <label
                for="organizationPostalCode"
                class="form-label">

                Postal Code

            </label>

            <input
                type="text"
                id="organizationPostalCode"
                name="postal_code"
                class="form-control"
                maxlength="20"
                autocomplete="postal-code"
                placeholder="Postal code">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Timezone -->
        <div class="col-md-6">

            <label
                for="organizationTimezone"
                class="form-label">

                Timezone

            </label>

            <input
                type="text"
                id="organizationTimezone"
                name="timezone"
                class="form-control"
                maxlength="100"
                value="Asia/Kolkata"
                placeholder="e.g. Asia/Kolkata">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Currency -->
        <div class="col-md-6">

            <label
                for="organizationCurrency"
                class="form-label">

                Currency

            </label>

            <input
                type="text"
                id="organizationCurrency"
                name="currency"
                class="form-control"
                maxlength="10"
                value="INR"
                placeholder="e.g. INR">

            <div class="invalid-feedback"></div>

        </div>


        <!-- Status -->
        <div class="col-md-6">

            <label
                for="organizationStatus"
                class="form-label">

                Status

            </label>

            <select
                id="organizationStatus"
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


        <!-- Address -->
        <div class="col-12">

            <label
                for="organizationAddress"
                class="form-label">

                Address

            </label>

            <textarea
                id="organizationAddress"
                name="address"
                class="form-control"
                rows="3"
                maxlength="500"
                autocomplete="street-address"
                placeholder="Full organization address"></textarea>

            <div class="invalid-feedback"></div>

        </div>

    </div>

</div>