<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="mb-4">
        <h2 class="mb-1 fw-semibold">My Reviews</h2>
        <p class="text-muted mb-0">View and complete your assigned appraisal reviews.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="myReviewsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Appraisal Cycle</th>
                            <th>Review Template</th>
                            <th>Appraisal Period</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center py-5">
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
    $(function() {
        loadMyReviews();
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

                renderMyReviews(response.data || []);
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                APP.error(xhr.responseJSON?.message || 'Unable to load reviews.');
                renderMyReviewsEmpty('Unable to load reviews.');
            }
        });
    }

    function renderMyReviews(reviews) {
        const $tbody = $('#myReviewsTable tbody');

        if (!reviews.length) {
            renderMyReviewsEmpty('You currently have no appraisal reviews available.');
            return;
        }

        let html = '';

        reviews.forEach(function(review) {
            const cycleName = CrudUtils.escapeHtml(review.cycle_name || '-');
            const cycleCode = review.cycle_code ? `<small class="text-muted">${CrudUtils.escapeHtml(review.cycle_code)}</small>` : '';
            const templateName = CrudUtils.escapeHtml(review.template_name || '-');

            html += `
                <tr>
                    <td class="ps-4">
                        <div class="d-flex flex-column">
                            <div class="fw-semibold">${cycleName}</div>
                            ${cycleCode}
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

                    <td>${renderReviewStatus(review.status)}</td>

                    <td class="text-end pe-4">${renderReviewAction(review)}</td>
                </tr>
            `;
        });

        $tbody.html(html);
    }

    function renderMyReviewsEmpty(message) {
        $('#myReviewsTable tbody').html(`
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="mb-2">
                        <i class="bi bi-clipboard-check fs-2 text-muted"></i>
                    </div>
                    <div class="fw-semibold mb-1">No Reviews Available</div>
                    <small class="text-muted">${CrudUtils.escapeHtml(message)}</small>
                </td>
            </tr>
        `);
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

    function renderReviewAction(review) {
        const reviewId = review.appraisal_id;

        if (review.status === 'submitted' || review.status === 'approved' || review.status === 'rejected') {
            return `<a href="<?= base_url('my-reviews/review') ?>/${reviewId}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i> View Review</a>`;
        }

        if (review.status === 'in_progress' && reviewId) {
            return `<a href="<?= base_url('my-reviews/review') ?>/${reviewId}" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square me-1"></i> Continue Review</a>`;
        }

        return `<button type="button" class="btn btn-sm btn-primary btn-start-review" data-cycle-id="${review.cycle_id}"><i class="bi bi-play-fill me-1"></i> Start Review</button>`;
    }

    function formatReviewDate(value) {
        if (!value) return '-';

        const date = new Date(value);

        if (Number.isNaN(date.getTime())) return CrudUtils.escapeHtml(value);

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

        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Starting...');

        $.ajax({
            url: '<?= base_url('my-reviews') ?>/' + cycleId + '/start',
            type: 'POST',
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to start review.');
                    $button.prop('disabled', false).html('<i class="bi bi-play-fill me-1"></i> Start Review');
                    return;
                }

                window.location.href = response.data.redirect_url;
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                APP.error(xhr.responseJSON?.message || 'Unable to start review.');
                $button.prop('disabled', false).html('<i class="bi bi-play-fill me-1"></i> Start Review');
            }
        });
    });
</script>

<?= $this->endSection() ?>