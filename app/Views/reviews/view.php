<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4" id="reviewPage">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= base_url('reviews') ?>" class="btn btn-sm btn-light border">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h2 class="mb-0 fw-semibold">Appraisal Review</h2>
            </div>
            <p class="text-muted mb-0 ms-5">Detailed appraisal review information and responses.</p>
        </div>

        <div id="reviewStatus"></div>
    </div>

    <div id="reviewLoading">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="placeholder-glow">
                    <span class="placeholder col-4"></span>
                    <span class="placeholder col-7"></span>
                    <span class="placeholder col-5"></span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body py-5 text-center">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                <span class="text-muted">Loading review details...</span>
            </div>
        </div>
    </div>

    <div id="reviewError" class="d-none">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-exclamation-circle fs-1 text-danger"></i>
                <h5 class="mt-3">Unable to Load Review</h5>
                <p class="text-muted mb-3" id="reviewErrorMessage">Something went wrong.</p>
                <a href="<?= base_url('reviews') ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Reviews
                </a>
            </div>
        </div>
    </div>

    <div id="reviewContent" class="d-none">
        <div id="reviewNotice" class="alert d-none border-0 shadow-sm mb-4"></div>

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-semibold">Review Information</h5>
                    </div>

                    <div class="card-body px-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Employee</small>
                                <div class="fw-semibold" id="employeeName">-</div>
                                <small class="text-muted" id="employeeCode"></small>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Reviewer</small>
                                <div class="fw-semibold" id="reviewerName">-</div>
                                <small class="text-muted" id="reviewerRole"></small>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Department</small>
                                <div id="employeeDepartment">-</div>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Designation</small>
                                <div id="employeeDesignation">-</div>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Review Type</small>
                                <div id="reviewType">-</div>
                            </div>

                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">Template</small>
                                <div class="fw-medium" id="templateName">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-semibold">Appraisal Cycle</h5>
                    </div>

                    <div class="card-body px-4">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Cycle</small>
                            <div class="fw-semibold" id="cycleName">-</div>
                            <small class="text-muted" id="cycleCode"></small>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Appraisal Period</small>
                            <div id="cyclePeriod">-</div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Cycle Status</small>
                            <div id="cycleStatus">-</div>
                        </div>

                        <div>
                            <small class="text-muted d-block mb-1">Overall Score</small>
                            <div class="fs-3 fw-bold" id="overallScore">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 fw-semibold">Review Responses</h5>
            </div>

            <div class="card-body px-4" id="reviewSections"></div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 fw-semibold">Overall Comment</h5>
            </div>

            <div class="card-body px-4">
                <div id="overallComment" class="text-body-secondary" style="white-space:pre-wrap;">No overall comment provided.</div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="mb-0 fw-semibold">Review Timeline</h5>
            </div>

            <div class="card-body px-4">
                <div class="row g-4">
                    <div class="col-md-3">
                        <small class="text-muted d-block mb-1">Created</small>
                        <div id="createdAt">-</div>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block mb-1">Last Updated</small>
                        <div id="updatedAt">-</div>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block mb-1">Submitted</small>
                        <div id="submittedAt">-</div>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block mb-1">Approved</small>
                        <div id="approvedAt">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>

<script>
    const reviewId = <?= (int) $reviewId ?>;

    $(function() {
        loadReview();
    });

    function loadReview() {
        $.ajax({
            url: '<?= base_url('reviews/view') ?>/' + reviewId + '/data',
            type: 'GET',

            success(response) {
                if (!response.success || !response.data?.review) {
                    showReviewError(response.message || 'Appraisal review not found.');
                    return;
                }

                renderReview(response.data);
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                showReviewError(xhr.responseJSON?.message || 'Unable to load appraisal review.');
            }
        });
    }

    function renderReview(data) {
        const review = data.review || {};

        $('#reviewLoading').addClass('d-none');
        $('#reviewError').addClass('d-none');
        $('#reviewContent').removeClass('d-none');

        $('#reviewStatus').html(renderReviewStatus(review.status));

        $('#employeeName').text(review.employee_name || '-');
        $('#employeeCode').text(review.employee_code || '');

        $('#reviewerName').text(review.reviewer_name || '-');
        $('#reviewerRole').text(review.reviewer_role_name || '');

        $('#employeeDepartment').text(review.employee_department || '-');
        $('#employeeDesignation').text(review.employee_designation || '-');

        $('#reviewType').html(renderReviewType(review.review_type));
        $('#templateName').text(review.template_name || '-');

        $('#cycleName').text(review.cycle_name || '-');
        $('#cycleCode').text(review.cycle_code || '');
        $('#cyclePeriod').text(formatDate(review.start_date) + ' – ' + formatDate(review.end_date));
        $('#cycleStatus').html(renderCycleStatus(review.cycle_status));

        const score = Number(review.overall_score);

        $('#overallScore').text(
            Number.isFinite(score) && score > 0 ?
            score.toFixed(2) :
            'Not Rated'
        );

        $('#overallComment').text(
            review.overall_comment || 'No overall comment provided.'
        );

        $('#createdAt').text(formatDateTime(review.created_at));
        $('#updatedAt').text(formatDateTime(review.updated_at));
        $('#submittedAt').text(formatDateTime(review.submitted_at));
        $('#approvedAt').text(formatDateTime(review.approved_at));

        renderReviewNotice(review.status);
        renderSections(data.sections || []);
    }

    function renderReviewNotice(status) {
        const messages = {
            pending: {
                className: 'alert-secondary',
                icon: 'bi-clock',
                text: 'This review is currently pending and has not been submitted by the reviewer.'
            },
            in_progress: {
                className: 'alert-primary',
                icon: 'bi-pencil-square',
                text: 'This review is currently in progress and may contain draft responses.'
            },
            submitted: {
                className: 'alert-success',
                icon: 'bi-check-circle',
                text: 'This review has been submitted by the reviewer.'
            },
            approved: {
                className: 'alert-success',
                icon: 'bi-patch-check',
                text: 'This review has been approved.'
            },
            rejected: {
                className: 'alert-danger',
                icon: 'bi-x-circle',
                text: 'This review has been rejected.'
            }
        };

        const notice = messages[status];

        if (!notice) {
            $('#reviewNotice').addClass('d-none');
            return;
        }

        $('#reviewNotice')
            .removeClass('d-none alert-secondary alert-primary alert-success alert-danger')
            .addClass(notice.className)
            .html(`<i class="bi ${notice.icon} me-2"></i>${CrudUtils.escapeHtml(notice.text)}`);
    }

    function renderSections(sections) {
        const $container = $('#reviewSections');

        if (!sections.length) {
            $container.html(`
                <div class="text-center py-5">
                    <i class="bi bi-question-circle fs-2 text-muted"></i>
                    <div class="fw-semibold mt-2">No Questions Found</div>
                    <small class="text-muted">No questions are configured for this review template.</small>
                </div>
            `);

            return;
        }

        let html = '';

        sections.forEach(function(section, sectionIndex) {
            html += `
                <div class="${sectionIndex > 0 ? 'border-top pt-4 mt-4' : ''}">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-semibold">
                            ${CrudUtils.escapeHtml(section.section_name || 'Section')}
                        </h5>
                        <span class="badge bg-light text-dark border">
                            ${(section.questions || []).length} ${(section.questions || []).length === 1 ? 'Question' : 'Questions'}
                        </span>
                    </div>
            `;

            if (!section.questions?.length) {
                html += `
                    <div class="text-muted small py-2">
                        No questions in this section.
                    </div>
                `;
            } else {
                section.questions.forEach(function(question, questionIndex) {
                    html += renderQuestion(question, questionIndex + 1);
                });
            }

            html += '</div>';
        });

        $container.html(html);
    }

    function renderQuestion(question, number) {
        const answer = question.answer || {};
        const answerHtml = renderAnswer(question, answer);

        return `
            <div class="border rounded-3 p-3 mb-3">
                <div class="d-flex gap-3">
                    <div class="text-muted fw-semibold">${number}.</div>

                    <div class="flex-grow-1">
                        <div class="fw-medium mb-2">
                            ${CrudUtils.escapeHtml(question.question || '')}
                            ${Number(question.is_required) === 1 ? '<span class="text-danger ms-1">*</span>' : ''}
                        </div>

                        <div class="mb-2">
                            ${answerHtml}
                        </div>

                        ${answer.comment ? `
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted d-block mb-1">Comment</small>
                                <div style="white-space:pre-wrap;">${CrudUtils.escapeHtml(answer.comment)}</div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    }

    function renderAnswer(question, answer) {
        if (!answer || Object.keys(answer).length === 0) {
            return '<span class="text-muted fst-italic">No answer provided.</span>';
        }

        switch (question.answer_type) {
            case 'rating':
                if (answer.rating === null || answer.rating === undefined || answer.rating === '') {
                    return '<span class="text-muted fst-italic">Not rated.</span>';
                }

                return `
                    <span class="badge bg-primary-subtle text-primary border fs-6 px-3 py-2">
                        ${CrudUtils.escapeHtml(Number(answer.rating).toFixed(2))}
                    </span>
                `;

            case 'number':
                if (answer.answer_number === null || answer.answer_number === undefined || answer.answer_number === '') {
                    return '<span class="text-muted fst-italic">No number provided.</span>';
                }

                return `
                    <span class="fw-semibold">
                        ${CrudUtils.escapeHtml(String(answer.answer_number))}
                    </span>
                `;

            case 'yes_no':
                if (answer.answer_yes_no === null || answer.answer_yes_no === undefined || answer.answer_yes_no === '') {
                    return '<span class="text-muted fst-italic">Not answered.</span>';
                }

                return Number(answer.answer_yes_no) === 1 ?
                    '<span class="badge bg-success-subtle text-success border">Yes</span>' :
                    '<span class="badge bg-danger-subtle text-danger border">No</span>';

            case 'text':
            default:
                if (!answer.answer_text) {
                    return '<span class="text-muted fst-italic">No answer provided.</span>';
                }

                return `
                    <div class="bg-light rounded-3 p-3" style="white-space:pre-wrap;">
                        ${CrudUtils.escapeHtml(answer.answer_text)}
                    </div>
                `;
        }
    }

    function renderReviewType(type) {
        if (type === 'matrix') {
            return '<span class="badge bg-info-subtle text-info border">Matrix Review</span>';
        }

        return '<span class="badge bg-secondary-subtle text-secondary border">Self Review</span>';
    }

    function renderReviewStatus(status) {
        const statuses = {
            pending: '<span class="badge bg-secondary-subtle text-secondary border px-3 py-2">Pending</span>',
            in_progress: '<span class="badge bg-primary-subtle text-primary border px-3 py-2">In Progress</span>',
            submitted: '<span class="badge bg-success-subtle text-success border px-3 py-2">Submitted</span>',
            approved: '<span class="badge bg-success-subtle text-success border px-3 py-2">Approved</span>',
            rejected: '<span class="badge bg-danger-subtle text-danger border px-3 py-2">Rejected</span>'
        };

        return statuses[status] || '<span class="badge bg-light text-dark border px-3 py-2">Unknown</span>';
    }

    function renderCycleStatus(status) {
        const statuses = {
            draft: '<span class="badge bg-secondary-subtle text-secondary border">Draft</span>',
            active: '<span class="badge bg-success-subtle text-success border">Active</span>',
            completed: '<span class="badge bg-primary-subtle text-primary border">Completed</span>',
            closed: '<span class="badge bg-dark-subtle text-dark border">Closed</span>'
        };

        return statuses[status] || '<span class="badge bg-light text-dark border">Unknown</span>';
    }

    function formatDate(value) {
        if (!value) return '-';

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return CrudUtils.escapeHtml(value);
        }

        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function formatDateTime(value) {
        if (!value) return '—';

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) {
            return CrudUtils.escapeHtml(value);
        }

        return date.toLocaleString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function showReviewError(message) {
        $('#reviewLoading').addClass('d-none');
        $('#reviewContent').addClass('d-none');
        $('#reviewError').removeClass('d-none');
        $('#reviewErrorMessage').text(message || 'Unable to load appraisal review.');
    }
</script>

<?= $this->endSection() ?>