<div class="modal fade" id="crudModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content shadow-sm">
            <form id="crudForm" novalidate>
                <div class="modal-header px-3 py-2">
                    <div>
                        <h5 class="modal-title mb-0">Review Matrix</h5>
                        <div class="small text-muted">Set who reviews each role and their contribution.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="organization_id" class="form-label rm-label">Organization <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="organization_id" name="organization_id" required>
                                <option value="">Select organization</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-6">
                            <label for="reviewee_role_id" class="form-label rm-label">Who is being reviewed? <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="reviewee_role_id" name="reviewee_role_id" required>
                                <option value="">Select role</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="rm-self-review mt-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="allow_self_review" name="allow_self_review" value="1" checked>
                            </div>
                            <div>
                                <div class="fw-semibold small">Self Review</div>
                                <div class="text-muted rm-hint">Employee reviews themselves.</div>
                            </div>
                        </div>
                        <div id="selfReviewWeightWrap" class="input-group input-group-sm rm-self-weight">
                            <input type="number" class="form-control" id="self_review_weightage" name="self_review_weightage" min="0" max="100" step="1" value="" placeholder="0">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="rm-reviewers mt-2">
                        <div class="rm-section-head">
                            <div>
                                <div class="fw-semibold small">Who will review them?</div>
                                <div class="text-muted rm-hint">Select the reviewer roles.</div>
                            </div>
                            <div class="rm-total-wrap">
                                <span class="text-muted">Total</span>
                                <strong id="reviewWeightTotal" class="text-danger">0%</strong>
                            </div>
                        </div>

                        <div id="reviewerRolePicker" class="rm-role-picker"></div>

                        <div id="selectedReviewers" class="mt-2"></div>

                        <div id="reviewWeightValidation" class="rm-validation"></div>
                    </div>
                </div>

                <div class="modal-footer px-3 py-2">
                    <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="saveReviewMatrix">
                        <span class="btn-submit-text">Save Configuration</span>
                        <span class="spinner-border spinner-border-sm d-none btn-submit-loader"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>