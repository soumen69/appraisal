<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 px-3">
            <ul class="nav nav-tabs card-header-tabs" id="myReviewsTabs">
                <li class="nav-item">
                    <button class="nav-link active" type="button" data-review-tab="assigned">
                        <i class="bi bi-clipboard-check me-1"></i>
                        Assigned to Me
                        <span class="badge bg-primary-subtle text-primary ms-1" id="assignedCount">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" type="button" data-review-tab="about_me">
                        <i class="bi bi-person-check me-1"></i>
                        About Me
                        <span class="badge bg-secondary-subtle text-secondary ms-1" id="aboutMeCount">0</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="myReviewsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Appraisal Cycle</th>
                            <th>Review</th>
                            <th>Template</th>
                            <th>Appraisal Period</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                <span class="text-muted">Loading your reviews...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>

<script>
    let myReviewsData = {
        assigned_to_me: [],
        about_me: [],
        available_self_reviews: []
    };

    let activeReviewTab = 'assigned';

    $(function() {
        loadMyReviews();

        $(document).on('click', '[data-review-tab]', function() {
            activeReviewTab = $(this).data('review-tab');

            $('#myReviewsTabs .nav-link').removeClass('active');
            $(this).addClass('active');

            renderActiveReviews();
        });
    });

    function loadMyReviews() {
        $.ajax({
            url: '<?= base_url('my-reviews/list') ?>',
            type: 'GET',

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to load reviews.');
                    renderMyReviewsEmpty(response.message || 'No reviews available.');
                    return;
                }

                myReviewsData = response.data || {
                    assigned_to_me: [],
                    about_me: [],
                    available_self_reviews: []
                };

                $('#assignedCount').text((myReviewsData.assigned_to_me || []).length);
                $('#aboutMeCount').text((myReviewsData.about_me || []).length);

                renderActiveReviews();
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                APP.error(xhr.responseJSON?.message || 'Unable to load reviews.');
                renderMyReviewsEmpty('Unable to load reviews.');
            }
        });
    }

    function renderActiveReviews() {
        const reviews = activeReviewTab === 'assigned' ?
            (myReviewsData.assigned_to_me || []) :
            (myReviewsData.about_me || []);

        renderMyReviews(reviews, activeReviewTab);

        if (activeReviewTab === 'assigned') {
            renderAvailableSelfReviews(myReviewsData.available_self_reviews || []);
        }
    }

    function renderMyReviews(reviews, viewType) {
        const $tbody = $('#myReviewsTable tbody');

        if (!reviews.length) {
            renderMyReviewsEmpty(
                viewType === 'assigned' ?
                'You currently have no reviews assigned to you.' :
                'No reviews have been submitted about you.'
            );
            return;
        }

        let html = '';

        reviews.forEach(function(review) {
            const cycleName = CrudUtils.escapeHtml(review.cycle_name || '-');
            const cycleCode = review.cycle_code ?
                `<small class="text-muted">${CrudUtils.escapeHtml(review.cycle_code)}</small>` :
                '';

            const templateName = CrudUtils.escapeHtml(review.template_name || '-');

            let reviewInfo = '-';

            if (viewType === 'assigned') {
                reviewInfo = `
                    <div class="d-flex flex-column">
                        <span class="fw-medium">${CrudUtils.escapeHtml(review.employee_name || '-')}</span>
                        <small class="text-muted">${renderReviewType(review.review_type)}</small>
                    </div>
                `;
            } else {
                reviewInfo = `
                    <div class="d-flex flex-column">
                        <span class="fw-medium">${CrudUtils.escapeHtml(review.reviewer_name || '-')}</span>
                        <small class="text-muted">${renderReviewType(review.review_type)}</small>
                    </div>
                `;
            }

            html += `
                <tr>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <div class="fw-semibold">${cycleName}</div>
                            ${cycleCode}
                        </div>
                    </td>

                    <td>${reviewInfo}</td>

                    <td>
                        <div class="fw-medium">${templateName}</div>
                    </td>

                    <td>
                        <div class="d-flex flex-column">
                            <span>${formatReviewDate(review.start_date)}</span>
                            <small class="text-muted">to ${formatReviewDate(review.end_date)}</small>
                        </div>
                    </td>

                    <td>${renderReviewStatus(review.status)}</td>

                    <td class="text-end pe-4">
                        ${renderReviewAction(review, viewType)}
                    </td>
                </tr>
            `;
        });

        $tbody.html(html);
    }

    function renderAvailableSelfReviews(reviews) {
        if (!reviews.length) return;

        const $tbody = $('#myReviewsTable tbody');

        if (activeReviewTab !== 'assigned') return;

        reviews.forEach(function(review) {
            const cycleName = CrudUtils.escapeHtml(review.cycle_name || '-');
            const cycleCode = review.cycle_code ?
                `<small class="text-muted">${CrudUtils.escapeHtml(review.cycle_code)}</small>` :
                '';

            const templateName = CrudUtils.escapeHtml(review.template_name || '-');

            $tbody.append(`
                <tr>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <div class="fw-semibold">${cycleName}</div>
                            ${cycleCode}
                        </div>
                    </td>

                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-medium">Self Review</span>
                            <small class="text-muted">Self</small>
                        </div>
                    </td>

                    <td>
                        <div class="fw-medium">${templateName}</div>
                    </td>

                    <td>
                        <div class="d-flex flex-column">
                            <span>${formatReviewDate(review.start_date)}</span>
                            <small class="text-muted">to ${formatReviewDate(review.end_date)}</small>
                        </div>
                    </td>

                    <td>${renderReviewStatus('pending')}</td>

                    <td class="text-end pe-4">
                        <button type="button" class="btn btn-sm btn-primary btn-start-review" data-cycle-id="${review.cycle_id}">
                            <i class="bi bi-play-fill me-1"></i> Start Review
                        </button>
                    </td>
                </tr>
            `);
        });
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

        return statuses[status] || '<span class="badge bg-secondary-subtle text-secondary border">Pending</span>';
    }

    // function renderReviewAction(review, viewType) {
    //     const reviewId = review.appraisal_id;

    //     if (!reviewId) return '-';

    //     if (viewType === 'about_me') {
    //         return `
    //             <a href="<?= base_url('my-reviews/review') ?>/${reviewId}" class="btn btn-sm btn-outline-primary">
    //                 <i class="bi bi-eye me-1"></i> View Review
    //             </a>
    //         `;
    //     }

    //     if (review.status === 'pending' || review.status === 'in_progress') {
    //         return `
    //             <a href="<?= base_url('my-reviews/review') ?>/${reviewId}" class="btn btn-sm btn-primary">
    //                 <i class="bi bi-pencil-square me-1"></i>
    //                 ${review.status === 'pending' ? 'Start Review' : 'Continue Review'}
    //             </a>
    //         `;
    //     }

    //     return `
    //         <a href="<?= base_url('my-reviews/review') ?>/${reviewId}" class="btn btn-sm btn-outline-primary">
    //             <i class="bi bi-eye me-1"></i> View Review
    //         </a>
    //     `;
    // }

    function renderReviewAction(review, viewType) {
        const reviewId = review.appraisal_id;

        if (!reviewId) return '-';

        if (viewType === 'about_me') {
            if (review.status === 'pending') {
                return `
                <span class="text-muted small">
                    <i class="bi bi-clock me-1"></i>Not Reviewed Yet
                </span>
            `;
            }

            if (review.status === 'in_progress') {
                return `
                <span class="text-muted small">
                    <i class="bi bi-hourglass-split me-1"></i>Review In Progress
                </span>
            `;
            }

            return `
            <a href="<?= base_url('my-reviews/review') ?>/${reviewId}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye me-1"></i> View Review
            </a>
        `;
        }

        if (review.status === 'pending' || review.status === 'in_progress') {
            return `
            <a href="<?= base_url('my-reviews/review') ?>/${reviewId}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil-square me-1"></i>
                ${review.status === 'pending' ? 'Start Review' : 'Continue Review'}
            </a>
        `;
        }

        return `
        <a href="<?= base_url('my-reviews/review') ?>/${reviewId}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-eye me-1"></i> View Review
        </a>`;
    }

    function renderMyReviewsEmpty(message) {
        $('#myReviewsTable tbody').html(`
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="mb-2">
                        <i class="bi bi-clipboard-check fs-2 text-muted"></i>
                    </div>
                    <div class="fw-semibold mb-1">No Reviews Available</div>
                    <small class="text-muted">${CrudUtils.escapeHtml(message)}</small>
                </td>
            </tr>
        `);
    }

    function formatReviewDate(value) {
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

    $(document).on('click', '.btn-start-review', function() {
        const $button = $(this);
        const cycleId = $button.data('cycle-id');

        if (!cycleId || $button.prop('disabled')) return;

        $button.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-1"></span> Starting...'
        );

        $.ajax({
            url: '<?= base_url('my-reviews') ?>/' + cycleId + '/start',
            type: 'POST',

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to start review.');

                    $button.prop('disabled', false).html(
                        '<i class="bi bi-play-fill me-1"></i> Start Review'
                    );

                    return;
                }

                window.location.href = response.data.redirect_url;
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                APP.error(xhr.responseJSON?.message || 'Unable to start review.');

                $button.prop('disabled', false).html(
                    '<i class="bi bi-play-fill me-1"></i> Start Review'
                );
            }
        });
    });
</script>

<?= $this->endSection() ?>