<div class="modal fade" id="crudModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="crudModalTitle">
                        Create Appraisal Template
                    </h5>

                    <small class="text-muted">
                        Configure the basic details
                        of the appraisal template.
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            <form id="crudForm" novalidate>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <!-- Organization -->
                        <div class="col-md-12">
                            <label
                                for="organization_id"
                                class="form-label">
                                Organization
                                <span class="text-danger">
                                    *
                                </span>
                            </label>
                            <select
                                class="form-select"
                                id="organization_id"
                                name="organization_id"
                                required>
                                <option value="">
                                    Select Organization
                                </option>
                            </select>
                            <div
                                class="invalid-feedback">
                            </div>
                        </div>

                        <!-- Template Name -->
                        <div class="col-md-12">
                            <label
                                for="template_name"
                                class="form-label">
                                Template Name
                                <span class="text-danger">
                                    *
                                </span>
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                id="template_name"
                                name="template_name"
                                placeholder="e.g. Annual Performance Review"
                                maxlength="150"
                                required>
                            <div
                                class="invalid-feedback">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label
                                for="status"
                                class="form-label">
                                Status
                                <span class="text-danger">
                                    *
                                </span>
                            </label>

                            <select
                                class="form-select"
                                id="status"
                                name="status"
                                required>

                                <option value="active">
                                    Active
                                </option>
                                <option value="inactive">
                                    Inactive
                                </option>
                            </select>
                            <div
                                class="invalid-feedback">
                            </div>

                        </div>


                        <!-- Default Template -->

                        <div
                            class="col-md-6
                            d-flex
                            align-items-end">

                            <div
                                class="form-check
                                form-switch
                                mb-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="is_default"
                                    name="is_default"
                                    value="1">

                                <label
                                    class="form-check-label"
                                    for="is_default">
                                    Set as default template
                                </label>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label
                                for="description"
                                class="form-label">
                                Description
                            </label>
                            <textarea
                                class="form-control"
                                id="description"
                                name="description"
                                rows="4"
                                placeholder="Enter template description..."></textarea>
                            <div
                                class="invalid-feedback">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn app-btn-primary"
                        id="btnSave">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>