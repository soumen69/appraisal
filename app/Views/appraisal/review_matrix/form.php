<div class="modal fade" id="crudModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="crudForm" novalidate>
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Review Matrix</h5>
                        <div class="small text-muted">Configure who can review whom.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="organization_id" class="form-label">Organization <span class="text-danger">*</span></label>
                            <select class="form-select" id="organization_id" name="organization_id" required>
                                <option value="">Select Organization</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="reviewer_role_id" class="form-label">Reviewer Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="reviewer_role_id" name="reviewer_role_id" required>
                                <option value="">Select Reviewer Role</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="reviewee_role_id" class="form-label">Reviewee Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="reviewee_role_id" name="reviewee_role_id" required>
                                <option value="">Select Reviewee Role</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold">Allow Self Review</div>
                                        <div class="small text-muted">Allow employees with this role to review themselves.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="allow_self_review" name="allow_self_review" value="1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="border rounded-3 p-3">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div>
                                        <div class="fw-semibold">Active</div>
                                        <div class="small text-muted">Enable this reviewer and reviewee relationship.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="btn-submit-text">Save Review Matrix</span>
                        <span class="spinner-border spinner-border-sm d-none btn-submit-loader"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>