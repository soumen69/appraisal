<form
    id="branchForm"
    method="post"
    novalidate>

    <?= csrf_field() ?>


    <div class="branch-form-card">

        <div class="branch-form-card-header">

            <div class="branch-section-icon">
                <i class="bi bi-building"></i>
            </div>

            <div>
                <h6>Branch Information</h6>

                <p>
                    Basic branch identification and organization details.
                </p>
            </div>

        </div>


        <div class="branch-form-card-body">

            <div class="row g-4">

                <div class="col-md-6">

                    <label class="branch-form-label">
                        Organization
                        <span>*</span>
                    </label>

                    <select
                        name="organization_id"
                        id="organization_id"
                        class="form-select branch-form-control"
                        required>

                        <option value="">
                            Select organization
                        </option>

                        <?php foreach ($organizations as $organization): ?>

                            <option
                                value="<?= (int) $organization['id'] ?>"
                                <?= (
                                    isset($branch['organization_id']) &&
                                    $branch['organization_id'] == $organization['id']
                                ) ? 'selected' : '' ?>>
                                <?= esc($organization['name']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="organization_id"></div>

                </div>


                <div class="col-md-6">

                    <label class="branch-form-label">
                        Branch Name
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control branch-form-control"
                        value="<?= esc($branch['name'] ?? '') ?>"
                        maxlength="150"
                        required>

                    <div
                        class="invalid-feedback"
                        data-field-error="name"></div>

                </div>


                <div class="col-md-4">

                    <label class="branch-form-label">
                        Branch Code
                    </label>

                    <input
                        type="text"
                        name="branch_code"
                        class="form-control branch-form-control"
                        value="<?= esc($branch['branch_code'] ?? '') ?>"
                        maxlength="30"
                        placeholder="e.g. 17B">

                    <div
                        class="branch-form-help">
                        Unique within the organization.
                    </div>

                    <div
                        class="invalid-feedback"
                        data-field-error="branch_code"></div>

                </div>


                <div class="col-md-4">

                    <label class="branch-form-label">
                        Status
                        <span>*</span>
                    </label>

                    <select
                        name="status"
                        class="form-select branch-form-control"
                        required>

                        <option
                            value="active"
                            <?= (
                                ($branch['status'] ?? 'active')
                                === 'active'
                            ) ? 'selected' : '' ?>>
                            Active
                        </option>

                        <option
                            value="inactive"
                            <?= (
                                ($branch['status'] ?? '')
                                === 'inactive'
                            ) ? 'selected' : '' ?>>
                            Inactive
                        </option>

                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="status"></div>

                </div>


                <div class="col-md-4">

                    <label class="branch-form-label">
                        Postal Code
                    </label>

                    <input
                        type="text"
                        name="postal_code"
                        class="form-control branch-form-control"
                        value="<?= esc($branch['postal_code'] ?? '') ?>"
                        maxlength="20">

                    <div
                        class="invalid-feedback"
                        data-field-error="postal_code"></div>

                </div>

            </div>

        </div>

    </div>


    <div class="branch-form-card">

        <div class="branch-form-card-header">

            <div class="branch-section-icon">
                <i class="bi bi-telephone"></i>
            </div>

            <div>
                <h6>Contact Information</h6>

                <p>
                    Branch contact details.
                </p>
            </div>

        </div>


        <div class="branch-form-card-body">

            <div class="row g-4">

                <div class="col-md-6">

                    <label class="branch-form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control branch-form-control"
                        value="<?= esc($branch['email'] ?? '') ?>"
                        maxlength="150"
                        placeholder="branch@example.com">

                    <div
                        class="invalid-feedback"
                        data-field-error="email"></div>

                </div>


                <div class="col-md-6">

                    <label class="branch-form-label">
                        Phone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control branch-form-control"
                        value="<?= esc($branch['phone'] ?? '') ?>"
                        maxlength="30">

                    <div
                        class="invalid-feedback"
                        data-field-error="phone"></div>

                </div>

            </div>

        </div>

    </div>


    <div class="branch-form-card">

        <div class="branch-form-card-header">

            <div class="branch-section-icon">
                <i class="bi bi-geo-alt"></i>
            </div>

            <div>
                <h6>Location</h6>

                <p>
                    Physical branch location.
                </p>
            </div>

        </div>


        <div class="branch-form-card-body">

            <div class="row g-4">

                <div class="col-12">

                    <label class="branch-form-label">
                        Address
                    </label>

                    <textarea
                        name="address"
                        class="form-control branch-form-control"
                        rows="3"><?= esc($branch['address'] ?? '') ?></textarea>

                    <div
                        class="invalid-feedback"
                        data-field-error="address"></div>

                </div>


                <div class="col-md-4">

                    <label class="branch-form-label">
                        City
                    </label>

                    <input
                        type="text"
                        name="city"
                        class="form-control branch-form-control"
                        value="<?= esc($branch['city'] ?? '') ?>"
                        maxlength="100">

                </div>


                <div class="col-md-4">

                    <label class="branch-form-label">
                        State
                    </label>

                    <input
                        type="text"
                        name="state"
                        class="form-control branch-form-control"
                        value="<?= esc($branch['state'] ?? '') ?>"
                        maxlength="100">

                </div>


                <div class="col-md-4">

                    <label class="branch-form-label">
                        Country
                    </label>

                    <input
                        type="text"
                        name="country"
                        class="form-control branch-form-control"
                        value="<?= esc(
                                    $branch['country'] ??
                                        'India'
                                ) ?>"
                        maxlength="100">

                </div>


                <div class="col-md-4">

                    <label class="branch-form-label">
                        Postal Code
                    </label>

                    <input
                        type="text"
                        name="postal_code"
                        class="form-control branch-form-control"
                        value="<?= esc($branch['postal_code'] ?? '') ?>"
                        maxlength="20">

                </div>

            </div>

        </div>

    </div>


    <div class="branch-form-actions">

        <a
            href="<?= base_url('branches') ?>"
            class="btn branch-cancel-btn">
            Cancel
        </a>

        <button
            type="submit"
            class="btn app-btn-primary"
            id="branchSaveBtn">
            <i class="bi bi-check2 me-1"></i>

            <?= isset($branch)
                ? 'Save Changes'
                : 'Create Branch'
            ?>

        </button>

    </div>

</form>