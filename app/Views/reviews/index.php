<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-0">
    <div class="card border-0 shadow-sm">
        <div class="card-body border-bottom">
            <div class="row g-3">
                <div class="col-lg-4">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" class="form-control" id="reviewSearch" placeholder="Employee, reviewer, cycle, template...">
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">Cycle</label>
                    <select class="form-select" id="reviewCycle">
                        <option value="">All Cycles</option>
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">Review Type</label>
                    <select class="form-select" id="reviewType">
                        <option value="">All Types</option>
                        <option value="self">Self</option>
                        <option value="matrix">Matrix</option>
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold">Status</label>
                    <select class="form-select" id="reviewStatus">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex align-items-end">
                    <button type="button" class="btn btn-light border w-100" id="resetReviewFilters">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="reviewsTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Appraisal Cycle</th>
                        <th>Employee</th>
                        <th>Reviewer</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Submitted</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <span class="text-muted">Loading appraisal reviews...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-0" id="reviewsPagination"></div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>

<script>
    let reviewPage = 1;
    let reviewPageSize = 10;
    let reviewSearchTimer = null;

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

        $('#reviewCycle, #reviewType, #reviewStatus').on('change', function() {
            reviewPage = 1;
            loadReviews();
        });

        $('#resetReviewFilters').on('click', function() {
            $('#reviewSearch').val('');
            $('#reviewCycle').val('');
            $('#reviewType').val('');
            $('#reviewStatus').val('');

            reviewPage = 1;
            loadReviews();
        });
    });

    function loadReviews() {
        const $tbody = $('#reviewsTable tbody');

        $tbody.html(`
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    <span class="text-muted">Loading appraisal reviews...</span>
                </td>
            </tr>
        `);

        $.ajax({
            url: '<?= base_url('reviews/list') ?>',
            type: 'GET',
            data: {
                page: reviewPage,
                pageSize: reviewPageSize,
                search: $('#reviewSearch').val().trim(),
                cycle_id: $('#reviewCycle').val(),
                review_type: $('#reviewType').val(),
                status: $('#reviewStatus').val()
            },

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to load appraisal reviews.');
                    renderReviewsEmpty(response.message || 'No reviews available.');
                    return;
                }

                const result = response.data || {};

                renderReviews(result.data || []);
                renderReviewsPagination(result);
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                APP.error(xhr.responseJSON?.message || 'Unable to load appraisal reviews.');
                renderReviewsEmpty('Unable to load appraisal reviews.');
            }
        });
    }

    function renderReviews(reviews) {
        const $tbody = $('#reviewsTable tbody');

        if (!reviews.length) {
            renderReviewsEmpty('No appraisal reviews match the selected criteria.');
            return;
        }

        let html = '';

        reviews.forEach(function(review) {
            html += `
                <tr>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${CrudUtils.escapeHtml(review.cycle_name || '-')}</span>
                            ${review.cycle_code ? `<small class="text-muted">${CrudUtils.escapeHtml(review.cycle_code)}</small>` : ''}
                        </div>
                    </td>

                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-medium">${CrudUtils.escapeHtml(review.employee_name || '-')}</span>
                            ${review.employee_code ? `<small class="text-muted">${CrudUtils.escapeHtml(review.employee_code)}</small>` : ''}
                        </div>
                    </td>

                    <td>
                        <span>${CrudUtils.escapeHtml(review.reviewer_name || '-')}</span>
                    </td>

                    <td>${renderReviewType(review.review_type)}</td>

                    <td>${renderReviewStatus(review.status)}</td>

                    <td>
                        ${review.overall_score !== null && review.overall_score !== undefined
                            ? `<strong>${CrudUtils.escapeHtml(Number(review.overall_score).toFixed(2))}</strong>`
                            : '<span class="text-muted">Not Rated</span>'}
                    </td>

                    <td>
                        ${review.submitted_at ? formatReviewDateTime(review.submitted_at) : '<span class="text-muted">—</span>'}
                    </td>

                    <td class="text-end pe-4">
                        <a href="<?= base_url('reviews/view') ?>/${review.appraisal_id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>View
                        </a>
                    </td>
                </tr>
            `;
        });

        $tbody.html(html);
    }

    function renderReviewsEmpty(message) {
        $('#reviewsTable tbody').html(`
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="mb-2">
                        <i class="bi bi-clipboard-x fs-2 text-muted"></i>
                    </div>
                    <div class="fw-semibold mb-1">No Reviews Available</div>
                    <small class="text-muted">${CrudUtils.escapeHtml(message)}</small>
                </td>
            </tr>
        `);

        $('#reviewsPagination').empty();
    }

    function renderReviewType(type) {
        if (type === 'matrix') {
            return '<span class="badge bg-info-subtle text-info border">Matrix</span>';
        }

        return '<span class="badge bg-secondary-subtle text-secondary border">Self</span>';
    }

    function renderReviewStatus(status) {
        const statuses = {
            pending: '<span class="badge bg-secondary-subtle text-secondary border">Pending</span>',
            in_progress: '<span class="badge bg-primary-subtle text-primary border">In Progress</span>',
            submitted: '<span class="badge bg-success-subtle text-success border">Submitted</span>',
            approved: '<span class="badge bg-success-subtle text-success border">Approved</span>',
            rejected: '<span class="badge bg-danger-subtle text-danger border">Rejected</span>'
        };

        return statuses[status] || '<span class="badge bg-light text-dark border">Unknown</span>';
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

        let html = `
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <small class="text-muted">
                    Showing ${((page - 1) * pageSize) + 1}–${Math.min(page * pageSize, total)} of ${total}
                </small>

                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-light border" ${page <= 1 ? 'disabled' : ''} onclick="changeReviewPage(${page - 1})">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <button type="button" class="btn btn-sm btn-light border" disabled>
                        Page ${page} of ${lastPage}
                    </button>

                    <button type="button" class="btn btn-sm btn-light border" ${page >= lastPage ? 'disabled' : ''} onclick="changeReviewPage(${page + 1})">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        `;

        $('#reviewsPagination').html(html);
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

                (response.data || []).forEach(function(cycle) {
                    $('#reviewCycle').append(
                        $('<option>', {
                            value: cycle.id,
                            text: cycle.cycle_code ? cycle.cycle_name + ' (' + cycle.cycle_code + ')' : cycle.cycle_name
                        })
                    );
                });
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;
            }
        });
    }

    function formatReviewDateTime(value) {
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
</script>

<?= $this->endSection() ?>