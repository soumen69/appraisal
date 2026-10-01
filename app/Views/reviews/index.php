<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-0">
    <div class="reviews-page">
        <div class="reviews-toolbar">
            <div class="reviews-toolbar-top">
                <div>
                    <div class="reviews-eyebrow">
                        <i class="bi bi-clipboard2-check"></i>
                        APPRAISAL REVIEWS
                    </div>
                    <h4 class="reviews-title">Employee appraisals</h4>
                    <p class="reviews-subtitle">Review submitted scores, calculate final results, and complete verification.</p>
                </div>
                <div class="reviews-live-status">
                    <span class="reviews-live-dot"></span>
                    Live appraisal data
                </div>
            </div>

            <div class="reviews-filters">
                <div class="review-filter search-filter">
                    <label for="reviewSearch">Search</label>
                    <div class="review-search">
                        <i class="bi bi-search"></i>
                        <input
                            type="text"
                            id="reviewSearch"
                            placeholder="Employee, reviewer, cycle...">
                    </div>
                </div>

                <div class="review-filter">
                    <label for="reviewCycle">Appraisal Cycle</label>
                    <select class="form-select" id="reviewCycle">
                        <option value="">All Cycles</option>
                    </select>
                </div>

                <div class="review-filter">
                    <label for="reviewStatus">Status</label>
                    <select class="form-select" id="reviewStatus">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="submitted">Awaiting Approval</option>
                        <option value="approved">Completed</option>
                        <option value="rejected">Revision Required</option>
                    </select>
                </div>

                <button type="button" class="btn btn-light border review-reset-btn" id="resetReviewFilters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset
                </button>
            </div>
        </div>

        <div id="reviewsList" class="reviews-list">
            <div class="reviews-loading">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <div>
                    <div class="reviews-loading-title">Loading appraisals</div>
                    <div class="reviews-loading-text">Fetching the latest employee review data...</div>
                </div>
            </div>
        </div>

        <div id="reviewsPagination" class="reviews-pagination"></div>
    </div>
</div>

<div class="review-action-overlay d-none" id="reviewActionOverlay">
    <div class="review-action-loader">
        <div class="review-action-loader-icon">
            <span class="spinner-border" role="status" aria-hidden="true"></span>
        </div>
        <div class="review-action-loader-title" id="reviewActionLoaderTitle">Processing...</div>
        <div class="review-action-loader-text" id="reviewActionLoaderText">
            Please wait while the appraisal is being processed.
        </div>
    </div>
</div>

<div class="modal fade" id="finalScoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered calculation-dialog">
        <div class="modal-content calculation-modal">
            <form id="finalScoreForm">
                <div class="calculation-header">
                    <div class="calculation-header-main">
                        <div class="calculation-icon">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <div>
                            <div class="calculation-eyebrow">FINAL RESULT</div>
                            <h5 class="calculation-title">Calculate appraisal</h5>
                            <div class="calculation-employee" id="finalScoreEmployeeLabel"></div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="calculation-body">
                    <input type="hidden" id="finalScoreEmployeeId" name="employee_id">
                    <input type="hidden" id="finalScoreEmployeeCode" name="employee_code">
                    <input type="hidden" id="finalScoreCycleId" name="cycle_id">

                    <div class="calculation-label">Calculation method</div>

                    <div class="calculation-options">
                        <label class="calculation-option">
                            <input
                                type="radio"
                                name="calculation_rule"
                                value="global_cap"
                                checked>
                            <span class="calculation-option-icon">
                                <i class="bi bi-sliders"></i>
                            </span>
                            <span class="calculation-option-content">
                                <strong>Global maximum</strong>
                                <small>Apply the application-wide maximum increment percentage.</small>
                            </span>
                            <span class="calculation-option-check">
                                <i class="bi bi-check2"></i>
                            </span>
                        </label>

                        <label class="calculation-option">
                            <input
                                type="radio"
                                name="calculation_rule"
                                value="ctc_slab">
                            <span class="calculation-option-icon">
                                <i class="bi bi-bar-chart-steps"></i>
                            </span>
                            <span class="calculation-option-content">
                                <strong>CTC range-based</strong>
                                <small>Apply the increment percentage configured for the employee's CTC range.</small>
                            </span>
                            <span class="calculation-option-check">
                                <i class="bi bi-check2"></i>
                            </span>
                        </label>
                    </div>

                    <div class="calculation-note">
                        <i class="bi bi-info-circle"></i>
                        <span>Completed reviews will use configured reviewer weightage when enabled, otherwise the eligible reviewers will receive equal weight.</span>
                    </div>
                </div>

                <div class="calculation-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary calculation-submit" id="calculateFinalScoreBtn">
                        <span class="calculate-final-text">
                            <i class="bi bi-calculator me-1"></i>
                            Calculate
                        </span>
                        <span class="spinner-border spinner-border-sm d-none calculate-final-loader" role="status"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="finalResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered result-dialog">
        <div class="modal-content result-modal">
            <div class="result-header">
                <div class="result-header-main">
                    <div class="result-status-icon" id="finalResultStatusIcon">
                        <i class="bi bi-check2"></i>
                    </div>
                    <div>
                        <div class="result-eyebrow">APPRAISAL RESULT</div>
                        <h5 class="result-title">Final result</h5>
                        <div class="result-employee" id="finalResultEmployeeLabel"></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="result-body">
                <div class="result-hero">
                    <div>
                        <span class="result-hero-label">Final rating</span>
                        <div class="result-hero-score">
                            <span id="finalResultScore">—</span>
                            <small>/ 5</small>
                        </div>
                    </div>

                    <div class="result-hero-side">
                        <span class="result-status-badge" id="finalResultStatus">Awaiting approval</span>
                        <span class="result-method" id="finalResultRule">—</span>
                    </div>
                </div>

                <div class="result-compensation">
                    <div class="result-compensation-item">
                        <span>Current CTC</span>
                        <strong id="finalResultCurrentCtc">—</strong>
                    </div>
                    <div class="result-compensation-item">
                        <span>Increment</span>
                        <strong id="finalResultIncrementPercentage">—</strong>
                    </div>
                    <div class="result-compensation-item result-compensation-highlight">
                        <span>Revised CTC</span>
                        <strong id="finalResultRevisedCtc">—</strong>
                    </div>
                </div>

                <div class="result-secondary">
                    <div class="result-secondary-item">
                        <span>Increase amount</span>
                        <strong id="finalResultIncrementAmount">—</strong>
                    </div>
                    <div class="result-secondary-item">
                        <span>Self score</span>
                        <strong id="finalResultSelfScore">—</strong>
                    </div>
                    <div class="result-secondary-item">
                        <span>Matrix score</span>
                        <strong id="finalResultMatrixScore">—</strong>
                    </div>
                    <div class="result-secondary-item">
                        <span>Weightage</span>
                        <strong id="finalResultWeightageMode">—</strong>
                    </div>
                </div>

                <div class="result-context" id="finalResultContext">
                    <div class="result-context-item" id="finalResultSlabRow">
                        <span>CTC slab</span>
                        <strong id="finalResultSlab">—</strong>
                    </div>
                    <div class="result-context-item">
                        <span>Reviewers</span>
                        <strong id="finalResultReviewCount">—</strong>
                    </div>
                </div>

                <div class="result-review-section">
                    <div class="result-section-heading">
                        <div>
                            <strong>Score breakdown</strong>
                            <span>How each completed review contributed</span>
                        </div>
                        <span class="result-section-total" id="finalResultContributionTotal">—</span>
                    </div>

                    <div id="finalResultReviewBreakdown" class="result-review-list"></div>
                </div>
            </div>

            <div class="result-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>

                <div class="result-footer-actions">
                    <button
                        type="button"
                        class="btn btn-outline-danger"
                        id="finalResultReject">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Return
                    </button>

                    <button
                        type="button"
                        class="btn btn-success"
                        id="finalResultApprove">
                        <i class="bi bi-check2 me-1"></i>
                        Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered reject-dialog">
        <div class="modal-content reject-modal">
            <form id="rejectReviewForm">
                <div class="reject-header">
                    <div class="reject-icon">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <div>
                        <h5 class="reject-title">Return appraisal</h5>
                        <div class="reject-subtitle">Tell the reviewer what needs to be corrected.</div>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="reject-body">
                    <input type="hidden" id="rejectAppraisalId" name="appraisal_id">

                    <label for="rejectionReason" class="reject-label">
                        Reason <span class="text-danger">*</span>
                    </label>

                    <textarea
                        class="form-control reject-textarea"
                        id="rejectionReason"
                        name="reason"
                        rows="4"
                        maxlength="2000"
                        required
                        placeholder="Explain what needs to be corrected..."></textarea>

                    <div class="reject-meta">
                        <span>This reason will be recorded with the appraisal.</span>
                        <span><span id="rejectionReasonCount">0</span>/2000</span>
                    </div>
                </div>

                <div class="reject-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="confirmRejectReview">
                        <span class="reject-button-text">Return for revision</span>
                        <span class="spinner-border spinner-border-sm d-none reject-button-loader"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>

<script>
    let reviewPage = 1;
    let reviewPageSize = 10;
    let reviewSearchTimer = null;
    let activeResultReview = null;
    const reviewResultCache = {};

    $(function() {
        loadReviewCycles();
        loadReviews();

        $('#reviewSearch').on('input', function() {
            clearTimeout(reviewSearchTimer);
            reviewSearchTimer = setTimeout(function() {
                reviewPage = 1;
                loadReviews();
            }, 350);
        });

        $('#reviewCycle, #reviewStatus').on('change', function() {
            reviewPage = 1;
            loadReviews();
        });

        $('#resetReviewFilters').on('click', function() {
            $('#reviewSearch').val('');
            $('#reviewCycle').val('');
            $('#reviewStatus').val('');
            reviewPage = 1;
            loadReviews();
        });

        $('#rejectionReason').on('input', function() {
            $('#rejectionReasonCount').text($(this).val().length);
        });

        $(document).on('click', '.btn-get-final-score', function() {
            const $button = $(this);
            const employeeId = String($button.data('employee-id') || '');
            const employeeCode = String($button.data('employee-code') || '');
            const cycleId = String($button.data('cycle-id') || '');
            const employeeName = String($button.data('employee-name') || '');
            const cycleName = String($button.data('cycle-name') || '');

            $('#finalScoreForm')[0].reset();
            $('#finalScoreEmployeeId').val(employeeId);
            $('#finalScoreEmployeeCode').val(employeeCode);
            $('#finalScoreCycleId').val(cycleId);
            $('#finalScoreEmployeeLabel').text(
                `${employeeName}${employeeCode ? ` · ${employeeCode}` : ''}${cycleName ? ` · ${cycleName}` : ''}`
            );
            $('input[name="calculation_rule"][value="global_cap"]').prop('checked', true);

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('finalScoreModal')
            ).show();
        });

        $(document).on('click', '.btn-view-final-result', function() {
            const resultId = String($(this).data('result-key') || '');
            const review = resultId ? reviewResultCache[resultId] : null;

            if (!review) {
                APP.error('Final result data is no longer available. Please refresh the page.');
                return;
            }

            openFinalResultModal(review);
        });

        $(document).on('click', '.btn-view-review', function() {
            const employeeId = String($(this).data('employee-id') || '');
            const cycleId = String($(this).data('cycle-id') || '');

            if (!employeeId) return;

            window.location.href =
                '<?= base_url('reviews/view') ?>/' +
                encodeURIComponent(employeeId) +
                (cycleId ? '?cycle_id=' + encodeURIComponent(cycleId) : '');
        });

        $('#finalScoreForm').on('submit', function(event) {
            event.preventDefault();

            const employeeId = String($('#finalScoreEmployeeId').val() || '');
            const employeeCode = String($('#finalScoreEmployeeCode').val() || '');
            const cycleId = String($('#finalScoreCycleId').val() || '');
            const rule = String($('input[name="calculation_rule"]:checked').val() || '');

            if (!employeeId || !employeeCode || !cycleId || !rule) {
                APP.error('Employee information or calculation rule is missing.');
                return;
            }

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('finalScoreModal')
            ).hide();

            setReviewActionLoading(
                true,
                'Calculating final result',
                'Combining completed review scores and applying the selected increment rule.'
            );

            $.ajax({
                url: '<?= base_url('reviews/calculate-final') ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    employee_id: employeeId,
                    employee_code: employeeCode,
                    cycle_id: cycleId,
                    calculation_rule: rule
                },
                success(response) {
                    if (!response.success) {
                        APP.error(
                            response.message ||
                            'Unable to calculate the final appraisal score.'
                        );
                        return;
                    }

                    activeResultReview = {
                        ...response,
                        employee_code: response.employee_code || employeeCode,
                        employee_id: response.employee_id || employeeId,
                        cycle_id: response.cycle_id || cycleId,
                        calculation_rule: response.calculation_rule || rule
                    };

                    loadReviews();

                    setTimeout(function() {
                        openFinalResultModal(activeResultReview);
                    }, 150);
                },
                error(xhr) {
                    if (APP.handleUnauthorized(xhr)) return;

                    APP.error(
                        xhr.responseJSON?.message ||
                        'Unable to calculate the final appraisal score.'
                    );
                },
                complete() {
                    setReviewActionLoading(false);
                }
            });
        });

        $(document).on('click', '.btn-approve-appraisal', function() {
            const appraisalId = String($(this).data('id') || '');

            if (!appraisalId) {
                APP.error('Approval record could not be identified.');
                return;
            }

            confirmApproveAppraisal(appraisalId);
        });

        $(document).on('click', '.btn-reject-appraisal', function() {
            const appraisalId = String($(this).data('id') || '');

            if (!appraisalId) {
                APP.error('Approval record could not be identified.');
                return;
            }

            openRejectModal(appraisalId);
        });

        $('#finalResultApprove').on('click', function() {
            const appraisalId = String(
                activeResultReview?.approval_appraisal_id ||
                activeResultReview?.appraisal_id ||
                ''
            );

            if (!appraisalId) {
                APP.error('Approval record could not be identified.');
                return;
            }

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('finalResultModal')
            ).hide();

            confirmApproveAppraisal(appraisalId);
        });

        $('#finalResultReject').on('click', function() {
            const appraisalId = String(
                activeResultReview?.approval_appraisal_id ||
                activeResultReview?.appraisal_id ||
                ''
            );

            if (!appraisalId) {
                APP.error('Approval record could not be identified.');
                return;
            }

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('finalResultModal')
            ).hide();

            openRejectModal(appraisalId);
        });

        $('#rejectReviewForm').on('submit', function(event) {
            event.preventDefault();

            const appraisalId = String($('#rejectAppraisalId').val() || '');
            const reason = $('#rejectionReason').val().trim();

            if (!appraisalId || !reason) {
                APP.error('Please provide a rejection reason.');
                return;
            }

            rejectAppraisal(appraisalId, reason);
        });

        function confirmApproveAppraisal(appraisalId) {
            if (typeof Swal === 'undefined') {
                if (window.confirm('Approve this appraisal? This will mark it as completed.')) {
                    approveAppraisal(appraisalId);
                }
                return;
            }

            Swal.fire({
                title: 'Approve appraisal?',
                text: 'The final appraisal result will be marked as completed.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Approve appraisal',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#198754'
            }).then(function(result) {
                if (result.isConfirmed) {
                    approveAppraisal(appraisalId);
                }
            });
        }

        function openRejectModal(appraisalId) {
            $('#rejectReviewForm')[0].reset();
            $('#rejectAppraisalId').val(appraisalId);
            $('#rejectionReasonCount').text('0');

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById('rejectReviewModal')
            ).show();
        }
    });

    function loadReviews() {
        const $list = $('#reviewsList');

        $list.html(`
        <div class="reviews-loading">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <div>
                <div class="reviews-loading-title">Loading appraisals</div>
                <div class="reviews-loading-text">Fetching the latest employee review data...</div>
            </div>
        </div>
    `);

        $.ajax({
            url: '<?= base_url('reviews/list') ?>',
            type: 'GET',
            data: {
                page: reviewPage,
                pageSize: reviewPageSize,
                search: $('#reviewSearch').val().trim(),
                cycle_id: $('#reviewCycle').val(),
                status: $('#reviewStatus').val()
            },
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to load appraisals.');
                    renderReviewsEmpty(response.message || 'No appraisals available.');
                    return;
                }

                const result = response.data || {};
                renderReviews(result.data || []);
                renderReviewsPagination(result);
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to load appraisals.'
                );

                renderReviewsEmpty('Unable to load appraisals.');
            }
        });
    }

    function renderReviews(reviews) {
        const $list = $('#reviewsList');

        if (!reviews.length) {
            renderReviewsEmpty('No appraisals match the selected criteria.');
            return;
        }

        let html = '';

        reviews.forEach(function(review, index) {
            const employeeId = String(review.employee_id || '');
            const cycleId = String(review.cycle_id || '');
            const employeeName = review.employee_name || '-';
            const employeeCode = review.employee_code || '';
            const cycleName = review.cycle_name || '-';
            const cycleCode = review.cycle_code || '';

            const statuses = String(
                    review.review_statuses ||
                    review.status ||
                    ''
                )
                .split(',')
                .map(s => s.trim().toLowerCase())
                .filter(Boolean);

            const status = statuses.length ?
                (
                    statuses.every(s => s === 'submitted') ? 'submitted' :
                    statuses.every(s => s === 'approved') ? 'approved' :
                    statuses.every(s => s === 'rejected') ? 'rejected' :
                    statuses.every(s => s === 'pending') ? 'pending' :
                    statuses.every(s => s === 'in_progress') ? 'in_progress' :
                    'mixed'
                ) :
                '';

            const finalScore = review.final_score ?? null;

            const scoreFinalized =
                review.score_finalized === true ||
                Number(review.score_finalized) === 1 ||
                (finalScore !== null && finalScore !== '');

            const submittedAt =
                review.latest_submitted_at ||
                review.submitted_at ||
                '';

            const reviewers =
                review.reviewers ??
                review.reviewer_names ??
                review.reviewer_scores ?? [];

            const approvalAppraisalId =
                review.approval_appraisal_id ||
                review.appraisal_id ||
                '';

            const hasFinalResult =
                finalScore !== null &&
                finalScore !== '' &&
                Number.isFinite(Number(finalScore));

            const resultKey = `review-result-${employeeId}-${cycleId}-${index}`;

            if (hasFinalResult) {
                reviewResultCache[resultKey] = {
                    ...review,
                    employee_id: employeeId,
                    cycle_id: cycleId
                };
            }

            const initials = getInitials(employeeName);

            html += `
            <article class="employee-appraisal-card">
                <div class="employee-card-main">
                    <div class="employee-identity">
                        <div class="employee-avatar">
                            ${CrudUtils.escapeHtml(initials)}
                        </div>

                        <div class="employee-identity-content">
                            <div class="employee-name-row">
                                <h5>${CrudUtils.escapeHtml(employeeName)}</h5>
                                ${employeeCode
                                    ? `<span class="employee-code">${CrudUtils.escapeHtml(employeeCode)}</span>`
                                    : ''}
                            </div>

                            <div class="employee-cycle">
                                <i class="bi bi-calendar3"></i>
                                <span>${CrudUtils.escapeHtml(cycleName)}</span>
                                ${cycleCode
                                    ? `<span class="cycle-code">${CrudUtils.escapeHtml(cycleCode)}</span>`
                                    : ''}
                            </div>
                        </div>
                    </div>

                    <div class="employee-status-area">
                        ${renderReviewStatus(status)}
                        ${submittedAt
                            ? `<span class="employee-submitted">
                                <i class="bi bi-clock"></i>
                                ${formatReviewDateTime(submittedAt)}
                            </span>`
                            : ''}
                    </div>
                </div>

                <div class="employee-review-area">
                    <div class="review-area-heading">
                        <div>
                            <strong>Review scores</strong>
                            <span>${getReviewerSummary(reviewers)}</span>
                        </div>
                        ${renderReviewScoreSummary(reviewers)}
                    </div>

                    <div class="reviewer-grid">
                        ${renderReviewerCards(reviewers)}
                    </div>
                </div>

                <div class="employee-result-area">
                    <div class="result-summary">
                        <span class="result-summary-label">Final rating</span>
                        ${
                            hasFinalResult
                                ? `
                                    <div class="result-summary-score">
                                        ${CrudUtils.escapeHtml(Number(finalScore).toFixed(2))}
                                        <small>/ 5</small>
                                    </div>
                                    <span class="result-summary-state ${status === 'approved' ? 'completed' : 'calculated'}">
                                        ${status === 'approved' ? 'Approved' : 'Calculated'}
                                    </span>
                                `
                                : `
                                    <div class="result-summary-pending">
                                        <i class="bi bi-hourglass-split"></i>
                                        Awaiting final calculation
                                    </div>
                                `
                        }
                    </div>

                    <div class="employee-card-actions">
                        <button
                            type="button"
                            class="btn btn-light border btn-view-review"
                            data-employee-id="${CrudUtils.escapeHtml(employeeId)}"
                            data-cycle-id="${CrudUtils.escapeHtml(cycleId)}">
                            <i class="bi bi-eye"></i>
                            Review
                        </button>

                        ${
                            status === 'submitted' && !scoreFinalized
                                ? `
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-get-final-score"
                                        data-employee-id="${CrudUtils.escapeHtml(employeeId)}"
                                        data-employee-code="${CrudUtils.escapeHtml(employeeCode)}"
                                        data-cycle-id="${CrudUtils.escapeHtml(cycleId)}"
                                        data-employee-name="${CrudUtils.escapeHtml(employeeName)}"
                                        data-cycle-name="${CrudUtils.escapeHtml(cycleName)}">
                                        <i class="bi bi-calculator"></i>
                                        Get final score
                                    </button>
                                `
                                : ''
                        }

                        ${
                            hasFinalResult
                                ? `
                                    <button
                                        type="button"
                                        class="btn btn-light border btn-view-final-result"
                                        data-result-key="${CrudUtils.escapeHtml(resultKey)}">
                                        <i class="bi bi-bar-chart-line"></i>
                                        Result
                                    </button>
                                `
                                : ''
                        }

                        ${
                            status === 'submitted' && hasFinalResult
                                ? `
                                    <button
                                        type="button"
                                        class="btn btn-success btn-approve-appraisal"
                                        data-id="${CrudUtils.escapeHtml(approvalAppraisalId)}">
                                        <i class="bi bi-check2"></i>
                                        Approve
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-reject-appraisal"
                                        data-id="${CrudUtils.escapeHtml(approvalAppraisalId)}">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        Return
                                    </button>
                                `
                                : ''
                        }
                    </div>
                </div>
            </article>
        `;
        });

        $list.html(html);
    }

    function renderReviewerCards(reviewers) {
        if (typeof reviewers === 'string') {
            reviewers = reviewers.trim() ?
                reviewers.split(/\s*,\s*/) :
                [];
        }

        if (!Array.isArray(reviewers) || !reviewers.length) {
            return `
            <div class="reviewer-empty">
                <i class="bi bi-people"></i>
                <span>No reviewer score data available.</span>
            </div>
        `;
        }

        let html = '';

        reviewers.forEach(function(reviewer, index) {
            let name = 'Reviewer';
            let score = null;
            let type = '';
            let role = '';
            let status = '';

            if (typeof reviewer === 'string') {
                const match = reviewer.match(/^(.+?)\s*\(([^)]+)\)$/);

                if (match) {
                    name = match[1].trim();
                    score = match[2].trim();
                } else {
                    name = reviewer.trim();
                }
            } else {
                name =
                    reviewer.reviewer_name ||
                    reviewer.name ||
                    reviewer.employee_name ||
                    'Reviewer';

                score =
                    reviewer.overall_score ??
                    reviewer.score ??
                    null;

                type = String(reviewer.review_type || '').toLowerCase();

                role =
                    reviewer.reviewer_role_name ||
                    reviewer.role_name ||
                    reviewer.reviewer_role ||
                    '';

                status =
                    reviewer.status ||
                    reviewer.review_status ||
                    '';
            }

            const scoreValue =
                score !== null &&
                score !== undefined &&
                score !== '' &&
                Number.isFinite(Number(score)) ?
                Number(score).toFixed(2) :
                null;

            const isSelf = type === 'self';

            html += `
            <div class="reviewer-card ${isSelf ? 'reviewer-card-self' : ''}">
                <div class="reviewer-card-top">
                    <div class="reviewer-avatar">
                        ${CrudUtils.escapeHtml(getInitials(name))}
                    </div>

                    <div class="reviewer-card-identity">
                        <div class="reviewer-name">
                            ${CrudUtils.escapeHtml(name)}
                            ${isSelf
                                ? '<span class="reviewer-type self">Self</span>'
                                : '<span class="reviewer-type">Reviewer</span>'}
                        </div>

                        ${
                            role
                                ? `<div class="reviewer-role">${CrudUtils.escapeHtml(role)}</div>`
                                : ''
                        }
                    </div>

                    <div class="reviewer-score">
                        ${
                            scoreValue
                                ? `
                                    <strong>${CrudUtils.escapeHtml(scoreValue)}</strong>
                                    <small>/ 5</small>
                                `
                                : `
                                    <span class="reviewer-pending">
                                        ${status ? CrudUtils.escapeHtml(formatReviewStatusLabel(status)) : 'Pending'}
                                    </span>
                                `
                        }
                    </div>
                </div>

                ${
                    scoreValue
                        ? `
                            <div class="reviewer-score-bar">
                                <span style="width:${Math.min(100, Math.max(0, (Number(scoreValue) / 5) * 100))}%"></span>
                            </div>
                        `
                        : ''
                }
            </div>
        `;
        });

        return html;
    }

    function renderReviewScoreSummary(reviewers) {
        if (typeof reviewers === 'string') {
            reviewers = reviewers.trim() ?
                reviewers.split(/\s*,\s*/) :
                [];
        }

        if (!Array.isArray(reviewers) || !reviewers.length) {
            return '';
        }

        const scores = reviewers
            .map(function(reviewer) {
                if (typeof reviewer === 'string') {
                    const match = reviewer.match(/\(([^)]+)\)$/);
                    return match && Number.isFinite(Number(match[1])) ?
                        Number(match[1]) :
                        null;
                }

                const value =
                    reviewer.overall_score ??
                    reviewer.score ??
                    null;

                return value !== null &&
                    value !== '' &&
                    Number.isFinite(Number(value)) ?
                    Number(value) :
                    null;
            })
            .filter(value => value !== null);

        if (!scores.length) return '';

        const average = scores.reduce((sum, value) => sum + value, 0) / scores.length;

        return `
        <div class="review-score-summary">
            <span>Average</span>
            <strong>${average.toFixed(2)}</strong>
            <small>/ 5</small>
        </div>
    `;
    }

    function getReviewerSummary(reviewers) {
        if (typeof reviewers === 'string') {
            reviewers = reviewers.trim() ?
                reviewers.split(/\s*,\s*/) :
                [];
        }

        if (!Array.isArray(reviewers) || !reviewers.length) {
            return 'No reviewers';
        }

        const count = reviewers.length;

        return `${count} completed review${count === 1 ? '' : 's'}`;
    }

    function renderReviewStatus(status) {
        const statuses = {
            pending: {
                label: 'Pending',
                className: 'review-status-pending',
                icon: 'bi-hourglass'
            },
            in_progress: {
                label: 'In progress',
                className: 'review-status-progress',
                icon: 'bi-arrow-repeat'
            },
            submitted: {
                label: 'Awaiting approval',
                className: 'review-status-submitted',
                icon: 'bi-clock-history'
            },
            approved: {
                label: 'Completed',
                className: 'review-status-approved',
                icon: 'bi-check2-circle'
            },
            rejected: {
                label: 'Revision required',
                className: 'review-status-rejected',
                icon: 'bi-arrow-counterclockwise'
            },
            mixed: {
                label: 'Mixed status',
                className: 'review-status-mixed',
                icon: 'bi-layers'
            }
        };

        const item = statuses[status] || {
            label: 'Unknown',
            className: 'review-status-unknown',
            icon: 'bi-question-circle'
        };

        return `
        <span class="review-status ${item.className}">
            <i class="bi ${item.icon}"></i>
            ${item.label}
        </span>
    `;
    }

    function renderReviewsEmpty(message) {
        $('#reviewsList').html(`
        <div class="reviews-empty">
            <div class="reviews-empty-icon">
                <i class="bi bi-clipboard2-x"></i>
            </div>
            <div class="reviews-empty-title">No appraisals found</div>
            <div class="reviews-empty-text">${CrudUtils.escapeHtml(message)}</div>
        </div>
    `);

        $('#reviewsPagination').empty();
    }

    function renderReviewsPagination(result) {
        const total = Number(result.total || 0);
        const page = Number(result.page || 1);
        const pageSize = Number(result.pageSize || 10);
        const lastPage = Number(result.lastPage || 1);

        if (total <= pageSize) {
            $('#reviewsPagination').empty();
            return;
        }

        const firstItem = ((page - 1) * pageSize) + 1;
        const lastItem = Math.min(page * pageSize, total);

        $('#reviewsPagination').html(`
        <div class="pagination-inner">
            <span class="pagination-summary">
                Showing <strong>${firstItem}–${lastItem}</strong> of <strong>${total}</strong>
            </span>

            <div class="pagination-controls">
                <button
                    type="button"
                    class="pagination-btn"
                    ${page <= 1 ? 'disabled' : ''}
                    onclick="changeReviewPage(${page - 1})"
                    aria-label="Previous page">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <span class="pagination-page">
                    ${page} / ${lastPage}
                </span>

                <button
                    type="button"
                    class="pagination-btn"
                    ${page >= lastPage ? 'disabled' : ''}
                    onclick="changeReviewPage(${page + 1})"
                    aria-label="Next page">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    `);
    }

    function changeReviewPage(page) {
        if (page < 1) return;

        reviewPage = page;
        loadReviews();
    }

    function loadReviewCycles() {
        $.ajax({
            url: '<?= base_url('reviews/cycles') ?>',
            type: 'GET',
            success(response) {
                if (!response.success) return;

                const $cycle = $('#reviewCycle');

                (response.data || []).forEach(function(cycle) {
                    $cycle.append(
                        $('<option>', {
                            value: cycle.id,
                            text: cycle.cycle_code ?
                                `${cycle.cycle_name} (${cycle.cycle_code})` :
                                cycle.cycle_name
                        })
                    );
                });
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;
            }
        });
    }

    function openFinalResultModal(review) {
        activeResultReview = review;

        const employeeName =
            review.employee_name ||
            'Employee';

        const employeeCode =
            review.employee_code ||
            '';

        const cycleName =
            review.cycle_name ||
            '';

        $('#finalResultEmployeeLabel').text(
            `${employeeName}${employeeCode ? ` · ${employeeCode}` : ''}${cycleName ? ` · ${cycleName}` : ''}`
        );

        const finalScore = Number(review.final_score);

        $('#finalResultScore').text(
            Number.isFinite(finalScore) ?
            finalScore.toFixed(2) :
            '—'
        );

        $('#finalResultCurrentCtc').text(
            formatCurrency(review.current_ctc)
        );

        $('#finalResultIncrementPercentage').text(
            formatPercentage(
                review.actual_increment_percentage ??
                review.effective_increment_percentage
            )
        );

        $('#finalResultIncrementAmount').text(
            formatCurrency(review.increment_amount)
        );

        $('#finalResultRevisedCtc').text(
            formatCurrency(review.revised_ctc)
        );

        $('#finalResultSelfScore').text(
            formatScore(review.self_score)
        );

        $('#finalResultMatrixScore').text(
            formatScore(review.matrix_score)
        );

        const weighted =
            review.use_reviewer_weightage === true ||
            Number(review.use_reviewer_weightage) === 1;

        $('#finalResultWeightageMode').text(
            weighted ?
            'Configured' :
            'Equal'
        );

        $('#finalResultRule').text(
            getCalculationRuleLabel(review.calculation_rule)
        );

        const slabFrom = review.ctc_slab_from;
        const slabTo = review.ctc_slab_to;

        if (
            review.calculation_rule === 'ctc_slab' &&
            slabFrom !== undefined &&
            slabTo !== undefined
        ) {
            $('#finalResultSlabRow').removeClass('d-none');
            $('#finalResultSlab').text(
                `${formatCurrency(slabFrom)} – ${formatCurrency(slabTo)}`
            );
        } else {
            $('#finalResultSlabRow').addClass('d-none');
        }

        const reviewCount = Number(
            review.review_count ??
            (Array.isArray(review.review_details) ?
                review.review_details.length :
                0)
        );

        $('#finalResultReviewCount').text(
            reviewCount ?
            `${reviewCount} review${reviewCount === 1 ? '' : 's'}` :
            '—'
        );

        const approved =
            String(review.status || '').toLowerCase() === 'approved';

        $('#finalResultStatus')
            .text(approved ? 'Approved' : 'Awaiting approval')
            .toggleClass('approved', approved);

        $('#finalResultStatusIcon')
            .toggleClass('approved', approved)
            .html(
                approved ?
                '<i class="bi bi-check2"></i>' :
                '<i class="bi bi-hourglass-split"></i>'
            );

        const approvalAppraisalId =
            review.approval_appraisal_id ||
            review.appraisal_id ||
            '';

        const canVerify = !approved &&
            String(review.status || '').toLowerCase() === 'submitted' &&
            !!approvalAppraisalId;

        $('#finalResultApprove, #finalResultReject')
            .toggle(!!canVerify);

        renderFinalResultReviewBreakdown(
            review.review_details ||
            review.reviewer_details ||
            review.reviewers || []
        );

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('finalResultModal')
        ).show();
    }

    function renderFinalResultReviewBreakdown(reviews) {
        const $container = $('#finalResultReviewBreakdown');

        if (typeof reviews === 'string') {
            reviews = reviews.trim() ?
                reviews.split(/\s*,\s*/) :
                [];
        }

        if (!Array.isArray(reviews) || !reviews.length) {
            $('#finalResultContributionTotal').text('—');

            $container.html(`
            <div class="result-review-empty">
                Review contribution details are not available.
            </div>
        `);

            return;
        }

        let totalContribution = 0;
        let html = '';

        reviews.forEach(function(review) {
            if (typeof review === 'string') {
                html += `
                <div class="result-review-row">
                    <div class="result-review-name">
                        ${CrudUtils.escapeHtml(review)}
                    </div>
                </div>
            `;
                return;
            }

            const type =
                review.review_type === 'self' ?
                'Self review' :
                'Reviewer';

            const name =
                review.reviewer_name ||
                review.name ||
                (type === 'Self review' ? 'Self' : 'Reviewer');

            const score =
                review.review_score ??
                review.overall_score ??
                review.score ??
                null;

            const weight =
                review.weightage ??
                review.configured_weightage ??
                null;

            const contribution =
                review.weighted_contribution ??
                null;

            if (
                contribution !== null &&
                contribution !== '' &&
                Number.isFinite(Number(contribution))
            ) {
                totalContribution += Number(contribution);
            }

            html += `
            <div class="result-review-row">
                <div class="result-review-person">
                    <div class="result-review-avatar">
                        ${CrudUtils.escapeHtml(getInitials(name))}
                    </div>
                    <div>
                        <strong>${CrudUtils.escapeHtml(name)}</strong>
                        <span>${CrudUtils.escapeHtml(type)}</span>
                    </div>
                </div>

                <div class="result-review-metrics">
                    <div>
                        <span>Score</span>
                        <strong>${formatScore(score)}</strong>
                    </div>

                    <div>
                        <span>Weight</span>
                        <strong>${formatPercentage(weight)}</strong>
                    </div>

                    <div>
                        <span>Contribution</span>
                        <strong>${formatScore(contribution)}</strong>
                    </div>
                </div>
            </div>
        `;
        });

        $('#finalResultContributionTotal').text(
            Number.isFinite(totalContribution) ?
            `Total ${totalContribution.toFixed(2)}` :
            '—'
        );

        $container.html(html);
    }

    function getCalculationRuleLabel(rule) {
        switch (String(rule || '').toLowerCase()) {
            case 'global_cap':
                return 'Global maximum';

            case 'ctc_slab':
                return 'CTC range-based';

            default:
                return '—';
        }
    }

    function formatReviewStatusLabel(status) {
        const labels = {
            pending: 'Pending',
            in_progress: 'In progress',
            submitted: 'Awaiting approval',
            approved: 'Completed',
            rejected: 'Revision required'
        };

        return labels[String(status || '').toLowerCase()] || 'Pending';
    }

    function formatScore(value) {
        if (
            value === null ||
            value === undefined ||
            value === '' ||
            !Number.isFinite(Number(value))
        ) {
            return '—';
        }

        return Number(value).toFixed(2);
    }

    function formatPercentage(value) {
        if (
            value === null ||
            value === undefined ||
            value === '' ||
            !Number.isFinite(Number(value))
        ) {
            return '—';
        }

        return `${Number(value).toFixed(2)}%`;
    }

    function formatCurrency(value) {
        if (
            value === null ||
            value === undefined ||
            value === '' ||
            !Number.isFinite(Number(value))
        ) {
            return '—';
        }

        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 2
        }).format(Number(value));
    }

    function approveAppraisal(appraisalId) {
        setReviewActionLoading(
            true,
            'Approving appraisal',
            'Saving the approval and completing the appraisal.'
        );

        $.ajax({
            url: '<?= base_url('reviews/approve') ?>/' + encodeURIComponent(appraisalId),
            type: 'POST',
            dataType: 'json',
            success(response) {
                if (!response.success) {
                    APP.error(
                        response.message ||
                        'Unable to approve appraisal.'
                    );
                    return;
                }

                APP.success(
                    response.message ||
                    'Appraisal approved and completed.'
                );

                activeResultReview = null;
                loadReviews();
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to approve appraisal.'
                );
            },
            complete() {
                setReviewActionLoading(false);
            }
        });
    }

    function rejectAppraisal(appraisalId, reason) {
        const $button = $('#confirmRejectReview');

        $button.prop('disabled', true);
        $button.find('.reject-button-text').text('Returning...');
        $button.find('.reject-button-loader').removeClass('d-none');

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('rejectReviewModal')
        ).hide();

        setReviewActionLoading(
            true,
            'Returning appraisal',
            'Recording the revision request and updating the appraisal.'
        );

        $.ajax({
            url: '<?= base_url('reviews/reject') ?>/' + encodeURIComponent(appraisalId),
            type: 'POST',
            dataType: 'json',
            data: {
                reason: reason
            },
            success(response) {
                if (!response.success) {
                    APP.error(
                        response.message ||
                        'Unable to return appraisal for revision.'
                    );
                    return;
                }

                APP.success(
                    response.message ||
                    'Appraisal returned for revision.'
                );

                activeResultReview = null;
                loadReviews();
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to return appraisal for revision.'
                );
            },
            complete() {
                $button.prop('disabled', false);
                $button.find('.reject-button-text').text('Return for revision');
                $button.find('.reject-button-loader').addClass('d-none');
                setReviewActionLoading(false);
            }
        });
    }

    function setReviewActionLoading(
        loading,
        title = 'Processing...',
        text = 'Please wait while the appraisal is being processed.'
    ) {
        $('#reviewActionLoaderTitle').text(title);
        $('#reviewActionLoaderText').text(text);
        $('#reviewActionOverlay').toggleClass('d-none', !loading);
        $('body').toggleClass('review-processing', loading);
    }

    function getInitials(name) {
        const parts = String(name || '')
            .trim()
            .split(/\s+/)
            .filter(Boolean);

        if (!parts.length) return '?';

        if (parts.length === 1) {
            return parts[0].substring(0, 2).toUpperCase();
        }

        return (
            parts[0][0] +
            parts[parts.length - 1][0]
        ).toUpperCase();
    }

    function formatReviewDateTime(value) {
        if (!value) return '—';

        const normalized = String(value).replace(' ', 'T');
        const date = new Date(normalized);

        if (Number.isNaN(date.getTime())) {
            return CrudUtils.escapeHtml(value);
        }

        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }
</script>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>

<style>
    .reviews-page {
        max-width: 1320px;
        margin: 0 auto;
        padding-bottom: 24px;
    }

    .reviews-toolbar {
        margin-bottom: 14px;
        padding: 20px;
        border: 1px solid var(--bs-border-color);
        border-radius: 14px;
        background: var(--bs-body-bg);
    }

    .reviews-toolbar-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .reviews-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
        color: var(--bs-primary);
        font-size: .62rem;
        font-weight: 750;
        letter-spacing: .08em;
    }

    .reviews-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 750;
        letter-spacing: -.015em;
    }

    .reviews-subtitle {
        margin: 4px 0 0;
        color: var(--bs-secondary-color);
        font-size: .74rem;
    }

    .reviews-live-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 9px;
        border: 1px solid var(--bs-border-color);
        border-radius: 7px;
        color: var(--bs-secondary-color);
        background: var(--bs-light-bg-subtle);
        font-size: .66rem;
        white-space: nowrap;
    }

    .reviews-live-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--bs-success);
        box-shadow: 0 0 0 3px var(--bs-success-bg-subtle);
    }

    .reviews-filters {
        display: grid;
        grid-template-columns: minmax(260px, 1.7fr) minmax(180px, 1fr) minmax(160px, .8fr) auto;
        gap: 10px;
        align-items: end;
    }

    .review-filter label {
        display: block;
        margin-bottom: 5px;
        color: var(--bs-secondary-color);
        font-size: .67rem;
        font-weight: 700;
    }

    .review-filter .form-select {
        height: 38px;
        font-size: .76rem;
    }

    .review-search {
        position: relative;
    }

    .review-search i {
        position: absolute;
        top: 50%;
        left: 12px;
        z-index: 2;
        transform: translateY(-50%);
        color: var(--bs-secondary-color);
        font-size: .76rem;
    }

    .review-search input {
        width: 100%;
        height: 38px;
        padding: 0 12px 0 34px;
        border: 1px solid var(--bs-border-color);
        border-radius: 6px;
        outline: 0;
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        font-size: .76rem;
    }

    .review-search input:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 .18rem var(--bs-primary-bg-subtle);
    }

    .review-reset-btn {
        height: 38px;
        padding: 0 13px;
        font-size: .73rem;
    }

    .review-reset-btn i {
        margin-right: 4px;
    }

    .reviews-list {
        display: grid;
        gap: 12px;
    }

    .employee-appraisal-card {
        overflow: hidden;
        border: 1px solid var(--bs-border-color);
        border-radius: 14px;
        background: var(--bs-body-bg);
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .employee-appraisal-card:hover {
        border-color: var(--bs-primary-border-subtle);
        box-shadow: 0 7px 22px rgba(0, 0, 0, .055);
        transform: translateY(-1px);
    }

    .employee-card-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 15px 17px;
        border-bottom: 1px solid var(--bs-border-color);
    }

    .employee-identity {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
    }

    .employee-avatar {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: var(--bs-primary-bg-subtle);
        color: var(--bs-primary-text-emphasis);
        font-size: .75rem;
        font-weight: 750;
    }

    .employee-identity-content {
        min-width: 0;
    }

    .employee-name-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .employee-name-row h5 {
        margin: 0;
        color: var(--bs-body-color);
        font-size: .87rem;
        font-weight: 700;
    }

    .employee-code {
        padding: 3px 6px;
        border-radius: 5px;
        background: var(--bs-secondary-bg);
        color: var(--bs-secondary-color);
        font-size: .61rem;
        font-weight: 650;
    }

    .employee-cycle {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 4px;
        color: var(--bs-secondary-color);
        font-size: .67rem;
    }

    .employee-cycle i {
        font-size: .63rem;
    }

    .cycle-code {
        padding: 2px 5px;
        border-radius: 4px;
        background: var(--bs-light-bg-subtle);
        font-size: .58rem;
    }

    .employee-status-area {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 8px;
    }

    .employee-submitted {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--bs-secondary-color);
        font-size: .62rem;
        white-space: nowrap;
    }

    .employee-submitted i {
        font-size: .6rem;
    }

    .review-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 8px;
        border: 1px solid transparent;
        border-radius: 6px;
        font-size: .63rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .review-status i {
        font-size: .63rem;
    }

    .review-status-pending {
        color: var(--bs-secondary-color);
        background: var(--bs-secondary-bg);
        border-color: var(--bs-border-color);
    }

    .review-status-progress {
        color: var(--bs-primary);
        background: var(--bs-primary-bg-subtle);
        border-color: var(--bs-primary-border-subtle);
    }

    .review-status-submitted {
        color: #8a5a00;
        background: #fff4d6;
        border-color: #f3df9e;
    }

    .review-status-approved {
        color: var(--bs-success);
        background: var(--bs-success-bg-subtle);
        border-color: var(--bs-success-border-subtle);
    }

    .review-status-rejected {
        color: var(--bs-danger);
        background: var(--bs-danger-bg-subtle);
        border-color: var(--bs-danger-border-subtle);
    }

    .review-status-mixed,
    .review-status-unknown {
        color: var(--bs-secondary-color);
        background: var(--bs-light-bg-subtle);
        border-color: var(--bs-border-color);
    }

    .employee-review-area {
        padding: 14px 17px 15px;
    }

    .review-area-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 10px;
    }

    .review-area-heading>div:first-child {
        min-width: 0;
    }

    .review-area-heading strong {
        display: block;
        color: var(--bs-body-color);
        font-size: .72rem;
    }

    .review-area-heading span {
        display: block;
        margin-top: 2px;
        color: var(--bs-secondary-color);
        font-size: .61rem;
    }

    .review-score-summary {
        display: inline-flex;
        align-items: baseline;
        gap: 4px;
        padding: 5px 8px;
        border: 1px solid var(--bs-border-color);
        border-radius: 6px;
        background: var(--bs-light-bg-subtle);
        white-space: nowrap;
    }

    .review-score-summary span {
        margin: 0;
        color: var(--bs-secondary-color);
        font-size: .59rem;
    }

    .review-score-summary strong {
        display: inline;
        margin: 0;
        color: var(--bs-body-color);
        font-size: .73rem;
    }

    .review-score-summary small {
        color: var(--bs-secondary-color);
        font-size: .56rem;
    }

    .reviewer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(205px, 1fr));
        gap: 7px;
    }

    .reviewer-card {
        min-width: 0;
        padding: 9px;
        border: 1px solid var(--bs-border-color);
        border-radius: 9px;
        background: var(--bs-body-bg);
    }

    .reviewer-card-self {
        border-color: var(--bs-primary-border-subtle);
        background: var(--bs-primary-bg-subtle);
    }

    .reviewer-card-top {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .reviewer-avatar {
        width: 29px;
        height: 29px;
        flex: 0 0 29px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        background: var(--bs-secondary-bg);
        color: var(--bs-secondary-color);
        font-size: .59rem;
        font-weight: 750;
    }

    .reviewer-card-self .reviewer-avatar {
        background: var(--bs-primary);
        color: #fff;
    }

    .reviewer-card-identity {
        min-width: 0;
        flex: 1 1 auto;
    }

    .reviewer-name {
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
        overflow: hidden;
        color: var(--bs-body-color);
        font-size: .68rem;
        font-weight: 650;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .reviewer-type {
        flex: 0 0 auto;
        padding: 2px 4px;
        border-radius: 3px;
        background: var(--bs-secondary-bg);
        color: var(--bs-secondary-color);
        font-size: .5rem;
        font-weight: 700;
    }

    .reviewer-type.self {
        background: var(--bs-primary-bg-subtle);
        color: var(--bs-primary-text-emphasis);
    }

    .reviewer-role {
        margin-top: 2px;
        overflow: hidden;
        color: var(--bs-secondary-color);
        font-size: .57rem;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .reviewer-score {
        display: flex;
        align-items: baseline;
        flex: 0 0 auto;
        gap: 2px;
    }

    .reviewer-score strong {
        color: var(--bs-body-color);
        font-size: .85rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }

    .reviewer-score small {
        color: var(--bs-secondary-color);
        font-size: .54rem;
    }

    .reviewer-pending {
        color: var(--bs-secondary-color);
        font-size: .58rem;
        font-weight: 600;
    }

    .reviewer-score-bar {
        height: 3px;
        overflow: hidden;
        margin-top: 8px;
        border-radius: 5px;
        background: var(--bs-secondary-bg);
    }

    .reviewer-score-bar span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: var(--bs-primary);
    }

    .reviewer-empty {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 11px;
        border: 1px dashed var(--bs-border-color);
        border-radius: 8px;
        color: var(--bs-secondary-color);
        font-size: .65rem;
    }

    .employee-result-area {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 11px 17px;
        border-top: 1px solid var(--bs-border-color);
        background: var(--bs-light-bg-subtle);
    }

    .result-summary {
        min-width: 0;
    }

    .result-summary-label {
        display: block;
        color: var(--bs-secondary-color);
        font-size: .58rem;
        font-weight: 650;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .result-summary-score {
        display: inline-flex;
        align-items: baseline;
        gap: 3px;
        margin-top: 1px;
        color: var(--bs-body-color);
        font-size: 1.1rem;
        font-weight: 800;
        line-height: 1;
    }

    .result-summary-score small {
        color: var(--bs-secondary-color);
        font-size: .59rem;
        font-weight: 600;
    }

    .result-summary-state {
        display: inline-block;
        margin-left: 6px;
        padding: 3px 5px;
        border-radius: 4px;
        font-size: .54rem;
        font-weight: 700;
    }

    .result-summary-state.calculated {
        color: var(--bs-primary-text-emphasis);
        background: var(--bs-primary-bg-subtle);
    }

    .result-summary-state.completed {
        color: var(--bs-success-text-emphasis);
        background: var(--bs-success-bg-subtle);
    }

    .result-summary-pending {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 3px;
        color: var(--bs-secondary-color);
        font-size: .65rem;
    }

    .employee-card-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 5px;
    }

    .employee-card-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        min-height: 31px;
        padding: 5px 9px;
        border-radius: 6px;
        font-size: .64rem;
        font-weight: 650;
    }

    .employee-card-actions .btn i {
        font-size: .65rem;
    }

    .reviews-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 11px;
        min-height: 220px;
        border: 1px solid var(--bs-border-color);
        border-radius: 14px;
        background: var(--bs-body-bg);
    }

    .reviews-loading-title {
        color: var(--bs-body-color);
        font-size: .75rem;
        font-weight: 700;
    }

    .reviews-loading-text {
        margin-top: 2px;
        color: var(--bs-secondary-color);
        font-size: .63rem;
    }

    .reviews-empty {
        padding: 65px 20px;
        border: 1px solid var(--bs-border-color);
        border-radius: 14px;
        background: var(--bs-body-bg);
        text-align: center;
    }

    .reviews-empty-icon {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        border-radius: 12px;
        background: var(--bs-light-bg-subtle);
        color: var(--bs-secondary-color);
        font-size: 1.15rem;
    }

    .reviews-empty-title {
        color: var(--bs-body-color);
        font-size: .8rem;
        font-weight: 700;
    }

    .reviews-empty-text {
        margin-top: 3px;
        color: var(--bs-secondary-color);
        font-size: .66rem;
    }

    .reviews-pagination {
        margin-top: 13px;
    }

    .pagination-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 0 3px;
    }

    .pagination-summary {
        color: var(--bs-secondary-color);
        font-size: .63rem;
    }

    .pagination-summary strong {
        color: var(--bs-body-color);
        font-weight: 650;
    }

    .pagination-controls {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .pagination-btn,
    .pagination-page {
        min-width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--bs-border-color);
        border-radius: 6px;
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        font-size: .63rem;
    }

    .pagination-btn {
        cursor: pointer;
    }

    .pagination-btn:hover:not(:disabled) {
        border-color: var(--bs-primary-border-subtle);
        color: var(--bs-primary);
        background: var(--bs-primary-bg-subtle);
    }

    .pagination-btn:disabled {
        opacity: .45;
        cursor: default;
    }

    .pagination-page {
        min-width: 55px;
        color: var(--bs-secondary-color);
        font-weight: 650;
    }

    .review-action-overlay {
        position: fixed;
        inset: 0;
        z-index: 2050;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .72);
        backdrop-filter: blur(5px);
    }

    .review-action-loader {
        width: min(330px, calc(100vw - 32px));
        padding: 22px;
        border: 1px solid var(--bs-border-color);
        border-radius: 13px;
        background: var(--bs-body-bg);
        box-shadow: 0 18px 55px rgba(0, 0, 0, .12);
        text-align: center;
    }

    .review-action-loader-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 11px;
        border-radius: 11px;
        background: var(--bs-primary-bg-subtle);
    }

    .review-action-loader-icon .spinner-border {
        width: 1.1rem;
        height: 1.1rem;
    }

    .review-action-loader-title {
        color: var(--bs-body-color);
        font-size: .82rem;
        font-weight: 750;
    }

    .review-action-loader-text {
        margin-top: 4px;
        color: var(--bs-secondary-color);
        font-size: .65rem;
        line-height: 1.45;
    }

    body.review-processing {
        overflow: hidden;
    }

    .calculation-dialog {
        max-width: 475px;
    }

    .calculation-modal,
    .result-modal,
    .reject-modal {
        overflow: hidden;
        border: 0;
        border-radius: 14px;
        box-shadow: 0 20px 70px rgba(0, 0, 0, .15);
    }

    .calculation-header,
    .result-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--bs-border-color);
    }

    .calculation-header-main,
    .result-header-main {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .calculation-icon,
    .result-status-icon {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: var(--bs-primary-bg-subtle);
        color: var(--bs-primary);
        font-size: .82rem;
    }

    .result-status-icon {
        background: var(--bs-warning-bg-subtle);
        color: var(--bs-warning-text-emphasis);
    }

    .result-status-icon.approved {
        background: var(--bs-success-bg-subtle);
        color: var(--bs-success-text-emphasis);
    }

    .calculation-eyebrow,
    .result-eyebrow {
        margin-bottom: 2px;
        color: var(--bs-primary);
        font-size: .58rem;
        font-weight: 750;
        letter-spacing: .08em;
    }

    .result-eyebrow {
        color: var(--bs-success);
    }

    .calculation-title,
    .result-title {
        margin: 0;
        font-size: .9rem;
        font-weight: 750;
    }

    .calculation-employee,
    .result-employee {
        margin-top: 2px;
        color: var(--bs-secondary-color);
        font-size: .62rem;
    }

    .calculation-body {
        padding: 16px 18px;
    }

    .calculation-label {
        margin-bottom: 7px;
        color: var(--bs-secondary-color);
        font-size: .62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .calculation-options {
        display: grid;
        gap: 7px;
    }

    .calculation-option {
        position: relative;
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px;
        border: 1px solid var(--bs-border-color);
        border-radius: 9px;
        cursor: pointer;
        transition: .15s ease;
    }

    .calculation-option:hover {
        border-color: var(--bs-primary-border-subtle);
        background: var(--bs-light-bg-subtle);
    }

    .calculation-option:has(input:checked) {
        border-color: var(--bs-primary);
        background: var(--bs-primary-bg-subtle);
    }

    .calculation-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .calculation-option-icon {
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        background: var(--bs-secondary-bg);
        color: var(--bs-primary);
        font-size: .72rem;
    }

    .calculation-option-content {
        min-width: 0;
        flex: 1;
    }

    .calculation-option-content strong {
        display: block;
        color: var(--bs-body-color);
        font-size: .7rem;
    }

    .calculation-option-content small {
        display: block;
        margin-top: 2px;
        color: var(--bs-secondary-color);
        font-size: .59rem;
        line-height: 1.4;
    }

    .calculation-option-check {
        width: 19px;
        height: 19px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--bs-border-color);
        border-radius: 50%;
        color: transparent;
        font-size: .6rem;
    }

    .calculation-option:has(input:checked) .calculation-option-check {
        border-color: var(--bs-primary);
        background: var(--bs-primary);
        color: #fff;
    }

    .calculation-note {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        margin-top: 11px;
        padding: 8px 9px;
        border: 1px solid var(--bs-border-color);
        border-radius: 7px;
        background: var(--bs-light-bg-subtle);
        color: var(--bs-secondary-color);
        font-size: .6rem;
        line-height: 1.45;
    }

    .calculation-note i {
        flex: 0 0 auto;
        color: var(--bs-primary);
    }

    .calculation-footer,
    .reject-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;
        padding: 11px 18px;
        border-top: 1px solid var(--bs-border-color);
    }

    .calculation-footer .btn,
    .reject-footer .btn {
        min-height: 32px;
        padding: 5px 11px;
        font-size: .66rem;
    }

    .calculation-submit {
        min-width: 92px;
    }

    .result-dialog {
        max-width: 650px;
    }

    .result-body {
        padding: 15px 18px;
    }

    .result-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 13px;
        border: 1px solid var(--bs-primary-border-subtle);
        border-radius: 10px;
        background: var(--bs-primary-bg-subtle);
    }

    .result-hero-label {
        display: block;
        color: var(--bs-primary-text-emphasis);
        font-size: .58rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .result-hero-score {
        display: flex;
        align-items: baseline;
        gap: 3px;
        margin-top: 3px;
        color: var(--bs-primary-text-emphasis);
        font-size: 2rem;
        font-weight: 850;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }

    .result-hero-score small {
        color: var(--bs-primary-text-emphasis);
        font-size: .62rem;
        font-weight: 650;
    }

    .result-hero-side {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
        text-align: right;
    }

    .result-status-badge {
        padding: 4px 7px;
        border-radius: 5px;
        background: var(--bs-warning-bg-subtle);
        color: var(--bs-warning-text-emphasis);
        font-size: .58rem;
        font-weight: 700;
    }

    .result-status-badge.approved {
        background: var(--bs-success-bg-subtle);
        color: var(--bs-success-text-emphasis);
    }

    .result-method {
        color: var(--bs-secondary-color);
        font-size: .58rem;
    }

    .result-compensation {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 7px;
        margin-top: 9px;
    }

    .result-compensation-item {
        padding: 10px;
        border: 1px solid var(--bs-border-color);
        border-radius: 8px;
    }

    .result-compensation-item span,
    .result-secondary-item span,
    .result-context-item span {
        display: block;
        color: var(--bs-secondary-color);
        font-size: .57rem;
    }

    .result-compensation-item strong {
        display: block;
        margin-top: 3px;
        color: var(--bs-body-color);
        font-size: .78rem;
        font-weight: 750;
        font-variant-numeric: tabular-nums;
    }

    .result-compensation-highlight {
        border-color: var(--bs-success-border-subtle);
        background: var(--bs-success-bg-subtle);
    }

    .result-compensation-highlight strong {
        color: var(--bs-success-text-emphasis);
    }

    .result-secondary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 7px;
        margin-top: 7px;
    }

    .result-secondary-item {
        padding: 8px 9px;
        border: 1px solid var(--bs-border-color);
        border-radius: 7px;
    }

    .result-secondary-item strong {
        display: block;
        margin-top: 2px;
        color: var(--bs-body-color);
        font-size: .67rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .result-context {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 7px;
    }

    .result-context-item {
        min-width: 120px;
        flex: 1 1 auto;
        padding: 7px 9px;
        border: 1px solid var(--bs-border-color);
        border-radius: 7px;
    }

    .result-context-item strong {
        display: block;
        margin-top: 2px;
        color: var(--bs-body-color);
        font-size: .64rem;
    }

    .result-review-section {
        margin-top: 12px;
    }

    .result-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 6px;
    }

    .result-section-heading strong {
        display: block;
        font-size: .68rem;
    }

    .result-section-heading span {
        display: block;
        margin-top: 1px;
        color: var(--bs-secondary-color);
        font-size: .57rem;
    }

    .result-section-total {
        color: var(--bs-primary);
        font-size: .61rem !important;
        font-weight: 700;
        white-space: nowrap;
    }

    .result-review-list {
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid var(--bs-border-color);
        border-radius: 8px;
    }

    .result-review-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 9px;
    }

    .result-review-row+.result-review-row {
        border-top: 1px solid var(--bs-border-color);
    }

    .result-review-person {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
    }

    .result-review-avatar {
        width: 27px;
        height: 27px;
        flex: 0 0 27px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: var(--bs-light-bg-subtle);
        color: var(--bs-primary);
        font-size: .55rem;
        font-weight: 750;
    }

    .result-review-person strong {
        display: block;
        overflow: hidden;
        color: var(--bs-body-color);
        font-size: .64rem;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .result-review-person span {
        display: block;
        margin-top: 1px;
        color: var(--bs-secondary-color);
        font-size: .55rem;
    }

    .result-review-metrics {
        display: flex;
        align-items: center;
        gap: 13px;
        flex: 0 0 auto;
    }

    .result-review-metrics div {
        min-width: 42px;
        text-align: right;
    }

    .result-review-metrics span {
        display: block;
        color: var(--bs-secondary-color);
        font-size: .5rem;
    }

    .result-review-metrics strong {
        display: block;
        margin-top: 1px;
        color: var(--bs-body-color);
        font-size: .61rem;
        font-variant-numeric: tabular-nums;
    }

    .result-review-empty {
        padding: 11px;
        color: var(--bs-secondary-color);
        font-size: .6rem;
    }

    .result-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 11px 18px;
        border-top: 1px solid var(--bs-border-color);
    }

    .result-footer .btn {
        min-height: 32px;
        padding: 5px 10px;
        font-size: .64rem;
    }

    .result-footer-actions {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .reject-dialog {
        max-width: 455px;
    }

    .reject-header {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 15px 17px;
        border-bottom: 1px solid var(--bs-border-color);
    }

    .reject-icon {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: var(--bs-danger-bg-subtle);
        color: var(--bs-danger);
        font-size: .78rem;
    }

    .reject-title {
        margin: 0;
        font-size: .86rem;
        font-weight: 750;
    }

    .reject-subtitle {
        margin-top: 2px;
        color: var(--bs-secondary-color);
        font-size: .59rem;
    }

    .reject-body {
        padding: 15px 17px;
    }

    .reject-label {
        display: block;
        margin-bottom: 5px;
        color: var(--bs-body-color);
        font-size: .65rem;
        font-weight: 700;
    }

    .reject-textarea {
        resize: vertical;
        min-height: 105px;
        font-size: .7rem;
    }

    .reject-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 5px;
        color: var(--bs-secondary-color);
        font-size: .56rem;
    }

    .reject-footer {
        padding: 10px 17px;
    }

    @media (max-width: 991.98px) {
        .reviews-filters {
            grid-template-columns: 1fr 1fr;
        }

        .search-filter {
            grid-column: span 2;
        }

        .review-reset-btn {
            width: 100%;
        }

        .employee-card-main {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
        }

        .employee-status-area {
            justify-content: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .reviews-page {
            padding-bottom: 15px;
        }

        .reviews-toolbar {
            padding: 14px;
            border-radius: 11px;
        }

        .reviews-toolbar-top {
            margin-bottom: 13px;
        }

        .reviews-title {
            font-size: 1rem;
        }

        .reviews-live-status {
            display: none;
        }

        .reviews-filters {
            grid-template-columns: 1fr;
        }

        .search-filter {
            grid-column: auto;
        }

        .employee-card-main,
        .employee-review-area,
        .employee-result-area {
            padding-left: 13px;
            padding-right: 13px;
        }

        .review-area-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .review-score-summary {
            align-self: flex-start;
        }

        .reviewer-grid {
            grid-template-columns: 1fr;
        }

        .employee-result-area {
            align-items: flex-start;
            flex-direction: column;
        }

        .employee-card-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .employee-card-actions .btn {
            flex: 1 1 auto;
            justify-content: center;
        }

        .result-compensation {
            grid-template-columns: 1fr;
        }

        .result-secondary {
            grid-template-columns: repeat(2, 1fr);
        }

        .result-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .result-hero-side {
            align-items: flex-start;
            text-align: left;
        }

        .result-review-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .result-review-metrics {
            width: 100%;
            justify-content: space-between;
        }

        .result-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .result-footer-actions {
            width: 100%;
        }

        .result-footer-actions .btn {
            flex: 1;
        }

        .pagination-inner {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 420px) {
        .employee-card-actions .btn {
            flex-basis: calc(50% - 4px);
        }

        .result-secondary {
            grid-template-columns: 1fr 1fr;
        }

        .result-review-metrics {
            gap: 7px;
        }
    }
</style>

<?= $this->endSection() ?>