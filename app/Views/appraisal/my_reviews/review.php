<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4" id="reviewPage">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url('my-reviews') ?>" class="btn btn-light border btn-sm px-3"><i class="bi bi-arrow-left"></i></a>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h2 class="mb-0 fw-semibold" id="reviewTitle">Appraisal Review</h2>
                    <span id="reviewStatusBadge"></span>
                </div>
                <p class="text-muted mb-0" id="reviewSubtitle">Loading your appraisal review...</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border" id="btnSaveDraft" disabled><i class="bi bi-cloud-arrow-down me-1"></i> Save Draft</button>
            <button type="button" class="btn btn-primary" id="btnSubmitReview" disabled><i class="bi bi-check2-circle me-1"></i> Submit Review</button>
        </div>
    </div>

    <div id="reviewLoading">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-5 text-center">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <div class="fw-semibold fs-5">Loading Review...</div>
                <div class="text-muted mt-1">Please wait while your appraisal review is prepared.</div>
            </div>
        </div>
    </div>

    <div id="reviewError" class="d-none">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-5 text-center">
                <div class="mb-3"><i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i></div>
                <h4 class="fw-semibold mb-2">Unable to Load Review</h4>
                <p class="text-muted mb-4" id="reviewErrorMessage">Unable to load appraisal review.</p>
                <a href="<?= base_url('my-reviews') ?>" class="btn btn-primary"><i class="bi bi-arrow-left me-1"></i> Back to My Reviews</a>
            </div>
        </div>
    </div>

    <div id="reviewContent" class="d-none">
        <div class="row g-4">
            <div class="col-xl-9">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="text-muted small mb-1">Appraisal Cycle</div>
                                <div class="fw-semibold" id="cycleName">-</div>
                                <small class="text-muted" id="cycleCode"></small>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="text-muted small mb-1">Review Template</div>
                                <div class="fw-semibold" id="templateName">-</div>
                            </div>

                            <div class="col-md-6 col-lg-4">
                                <div class="text-muted small mb-1">Appraisal Period</div>
                                <div class="fw-semibold" id="appraisalPeriod">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="reviewSections"></div>
            </div>

            <div class="col-xl-3">
                <div class="card border-0 shadow-sm position-sticky" style="top: 20px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-semibold mb-0">Review Progress</h6>
                            <span class="fw-semibold text-primary" id="progressPercentage">0%</span>
                        </div>

                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%;"></div>
                        </div>

                        <div class="small text-muted mb-4"><span id="answeredCount">0</span> of <span id="totalQuestions">0</span> questions answered</div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Required Questions</span>
                                <span class="fw-semibold small"><span id="requiredAnsweredCount">0</span>/<span id="requiredQuestionsCount">0</span></span>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Overall Score</span>
                                <span class="fw-semibold text-primary" id="overallScore">Not Rated</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <label class="form-label fw-semibold">Overall Comments <span class="text-muted fw-normal">(Optional)</span></label>
                <textarea class="form-control" id="overallComment" rows="4" placeholder="Add any overall comments about your performance, achievements, challenges or goals..."></textarea>
            </div>
        </div>
    </div>
</div>

<div class="review-action-bar d-none" id="reviewActionBar">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <div class="small text-muted"><span id="bottomAnsweredCount">0</span> of <span id="bottomTotalQuestions">0</span> questions completed</div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light border" id="btnSaveDraftBottom"><i class="bi bi-cloud-arrow-down me-1"></i> Save Draft</button>
                <button type="button" class="btn btn-primary" id="btnSubmitReviewBottom"><i class="bi bi-check2-circle me-1"></i> Submit Review</button>
            </div>
        </div>
    </div>
</div>

<style>
    .review-question-card {
        transition: box-shadow .2s ease, border-color .2s ease;
    }

    .review-question-card:hover {
        border-color: rgba(var(--bs-primary-rgb), .35) !important;
        box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05);
    }

    .review-question-number {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bs-primary-bg-subtle);
        color: var(--bs-primary);
        font-size: .8rem;
        font-weight: 700;
    }

    .rating-option {
        min-width: 64px;
        padding: .7rem .5rem;
        border: 1px solid var(--bs-border-color);
        border-radius: .5rem;
        background: #fff;
        cursor: pointer;
        transition: all .15s ease;
        text-align: center;
    }

    .rating-option:hover {
        border-color: var(--bs-primary);
    }

    .rating-option.active {
        background: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
    }

    .rating-option .rating-value {
        font-weight: 700;
        font-size: 1rem;
        display: block;
    }

    .rating-option .rating-label {
        font-size: .7rem;
        display: block;
        margin-top: .15rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .answer-yes-no .btn-check:checked+.btn {
        background: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
    }

    .review-section-header {
        border-left: 4px solid var(--bs-primary);
    }

    .review-action-bar {
        position: sticky;
        bottom: 0;
        z-index: 1000;
        background: rgba(255, 255, 255, .96);
        border-top: 1px solid var(--bs-border-color);
        padding: .85rem 0;
        box-shadow: 0 -.25rem .75rem rgba(0, 0, 0, .06);
        margin-top: 2rem;
    }

    .question-required::after {
        content: ' *';
        color: var(--bs-danger);
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>

<script>
    const REVIEW_ID = <?= (int) $reviewId ?>;
    let reviewData = null;
    let reviewAnswers = {};
    let reviewQuestions = [];

    $(function() {
        loadReviewData();
        $('#btnSaveDraft, #btnSaveDraftBottom').on('click', saveDraft);
        $('#btnSubmitReview, #btnSubmitReviewBottom').on('click', submitReview);
        $(document).on('input change', '.review-answer-input', handleAnswerChange);
        $(document).on('click', '.rating-option', handleRatingSelection);
    });

    function loadReviewData() {
        $.ajax({
            url: '<?= base_url('my-reviews/review') ?>/' + REVIEW_ID + '/data',
            type: 'GET',
            success(response) {
                if (!response.success) {
                    showReviewError(response.message || 'Unable to load appraisal review.');
                    return;
                }

                reviewData = response.data || {};
                renderReview();
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;
                showReviewError(xhr.responseJSON?.message || 'Unable to load appraisal review.');
            }
        });
    }

    function showReviewError(message) {
        $('#reviewLoading').addClass('d-none');
        $('#reviewErrorMessage').text(message);
        $('#reviewError').removeClass('d-none');
    }

    function renderReview() {
        $('#reviewLoading').addClass('d-none');
        $('#reviewContent').removeClass('d-none');
        $('#reviewActionBar').removeClass('d-none');

        $('#reviewTitle').text(reviewData.cycle_name || 'Appraisal Review');
        $('#reviewSubtitle').text(reviewData.template_name || 'Complete your appraisal review.');
        $('#cycleName').text(reviewData.cycle_name || '-');
        $('#cycleCode').text(reviewData.cycle_code || '');
        $('#templateName').text(reviewData.template_name || '-');
        $('#appraisalPeriod').text(formatReviewDate(reviewData.start_date) + ' to ' + formatReviewDate(reviewData.end_date));
        $('#overallComment').val(reviewData.overall_comment || '');
        $('#reviewStatusBadge').html(renderReviewStatus(reviewData.status));

        reviewQuestions = [];
        reviewAnswers = {};

        (reviewData.sections || []).forEach(section => {
            (section.questions || []).forEach(question => {
                reviewQuestions.push(question);
                reviewAnswers[question.id] = normalizeExistingAnswer(question);
            });
        });

        renderSections(reviewData.sections || []);
        updateReviewProgress();

        const locked = ['submitted', 'approved'].includes(reviewData.status);
        $('#btnSaveDraft, #btnSaveDraftBottom, #btnSubmitReview, #btnSubmitReviewBottom').prop('disabled', locked);

        if (locked) {
            $('#overallComment').prop('readonly', true);
            $('.review-answer-input').prop('disabled', true);
            $('.rating-option').css('pointer-events', 'none');
        }
    }

    function renderSections(sections) {
        if (!sections.length) {
            $('#reviewSections').html(`
            <div class="card border-0 shadow-sm">
                <div class="card-body py-5 text-center">
                    <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                    <h5 class="fw-semibold mt-3">No Questions Available</h5>
                    <p class="text-muted mb-0">No questions are configured for this appraisal template.</p>
                </div>
            </div>
        `);
            return;
        }

        let html = '';

        sections.forEach((section, sectionIndex) => {
            const questions = section.questions || [];

            html += `
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="p-4 border-bottom review-section-header">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="text-primary small fw-semibold text-uppercase mb-1">Section ${sectionIndex + 1}</div>
                                <h5 class="fw-semibold mb-1">${CrudUtils.escapeHtml(section.section_name || 'Untitled Section')}</h5>
                                ${section.description ? `<p class="text-muted small mb-0">${CrudUtils.escapeHtml(section.description)}</p>` : ''}
                            </div>
                            <span class="badge bg-light text-dark border">${questions.length} Question${questions.length !== 1 ? 's' : ''}</span>
                        </div>
                    </div>

                    <div class="p-4">
                        ${questions.map((question, questionIndex) => renderQuestion(question, questionIndex + 1)).join('<hr class="my-4">')}
                    </div>
                </div>
            </div>
        `;
        });

        $('#reviewSections').html(html);
    }

    function renderQuestion(question, questionNumber) {
        const answer = reviewAnswers[question.id] || {};
        const required = Number(question.is_required) === 1 || question.is_required === true;
        const questionText = CrudUtils.escapeHtml(question.question_text || question.question || 'Question');
        const questionDescription = question.description || question.help_text || '';
        const answerType = question.answer_type || question.question_type || 'text';

        return `
        <div class="review-question-card" data-question-id="${question.id}">
            <div class="d-flex gap-3">
                <div class="review-question-number">${questionNumber}</div>

                <div class="flex-grow-1">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                        <div class="fw-semibold ${required ? 'question-required' : ''}">${questionText}</div>
                        ${required ? '<span class="badge bg-danger-subtle text-danger border">Required</span>' : ''}
                    </div>

                    ${questionDescription ? `<div class="small text-muted mb-3">${CrudUtils.escapeHtml(questionDescription)}</div>` : ''}

                    <div class="question-answer-container">
                        ${renderQuestionInput(question, answer, answerType)}
                    </div>

                    ${question.allow_comment || question.enable_comment ? `
                        <div class="mt-3">
                            <label class="form-label small text-muted mb-1">Comment <span class="fw-normal">(Optional)</span></label>
                            <textarea class="form-control form-control-sm review-answer-input" data-question-id="${question.id}" data-answer-field="comment" rows="2" placeholder="Add supporting comments...">${CrudUtils.escapeHtml(answer.comment || '')}</textarea>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    }

    function renderQuestionInput(question, answer, answerType) {
        const questionId = question.id;

        switch (answerType) {
            case 'rating':
            case 'scale':
                return renderRatingInput(question, answer);

            case 'yes_no':
            case 'boolean':
                return `
                <div class="answer-yes-no d-flex gap-2">
                    <input type="radio" class="btn-check review-answer-input" name="question_${questionId}" id="question_${questionId}_yes" value="1" data-question-id="${questionId}" data-answer-field="answer_yes_no" ${Number(answer.answer_yes_no) === 1 ? 'checked' : ''}>
                    <label class="btn btn-outline-primary px-4" for="question_${questionId}_yes"><i class="bi bi-check-lg me-1"></i> Yes</label>

                    <input type="radio" class="btn-check review-answer-input" name="question_${questionId}" id="question_${questionId}_no" value="0" data-question-id="${questionId}" data-answer-field="answer_yes_no" ${Number(answer.answer_yes_no) === 0 && answer.answer_yes_no !== null ? 'checked' : ''}>
                    <label class="btn btn-outline-secondary px-4" for="question_${questionId}_no"><i class="bi bi-x-lg me-1"></i> No</label>
                </div>
            `;

            case 'number':
            case 'numeric':
                return `<input type="number" step="0.01" class="form-control review-answer-input" data-question-id="${questionId}" data-answer-field="answer_number" value="${answer.answer_number ?? ''}" placeholder="Enter your answer">`;

            case 'textarea':
            case 'long_text':
                return `<textarea class="form-control review-answer-input" data-question-id="${questionId}" data-answer-field="answer_text" rows="5" placeholder="Enter your response...">${CrudUtils.escapeHtml(answer.answer_text || '')}</textarea>`;

            case 'text':
            default:
                return `<textarea class="form-control review-answer-input" data-question-id="${questionId}" data-answer-field="answer_text" rows="4" placeholder="Enter your response...">${CrudUtils.escapeHtml(answer.answer_text || '')}</textarea>`;
        }
    }

    function renderRatingInput(question, answer) {
        const questionId = question.id;
        const ratingValues = question.rating_values || question.scale_values || reviewData.rating_values || [];

        if (!ratingValues.length) {
            const maxRating = Number(question.max_rating || reviewData.max_rating || 5);
            for (let value = 1; value <= maxRating; value++) ratingValues.push({
                value: value,
                label: value
            });
        }

        return `
        <div class="d-flex flex-wrap gap-2 rating-options" data-question-id="${questionId}">
            ${ratingValues.map(item => {
                const value = typeof item === 'object' ? (item.value ?? item.score ?? item.rating) : item;
                const label = typeof item === 'object' ? (item.label ?? item.name ?? value) : value;
                const active = Number(answer.rating) === Number(value);

                return `
                    <button type="button" class="rating-option ${active ? 'active' : ''}" data-question-id="${questionId}" data-rating="${value}">
                        <span class="rating-value">${CrudUtils.escapeHtml(String(value))}</span>
                        <span class="rating-label">${CrudUtils.escapeHtml(String(label))}</span>
                    </button>
                `;
            }).join('')}
        </div>
    `;
    }

    function handleRatingSelection() {
        const $button = $(this);
        const questionId = $button.data('question-id');
        const rating = $button.data('rating');

        reviewAnswers[questionId] = {
            ...(reviewAnswers[questionId] || {}),
            rating: rating
        };

        $button.closest('.rating-options').find('.rating-option').removeClass('active');
        $button.addClass('active');

        updateReviewProgress();
    }

    function handleAnswerChange() {
        const $input = $(this);
        const questionId = $input.data('question-id');
        const field = $input.data('answer-field');

        if (!questionId || !field) return;

        let value;

        if ($input.attr('type') === 'radio') {
            if (!$input.is(':checked')) return;
            value = $input.val();
        } else {
            value = $input.val();
        }

        reviewAnswers[questionId] = {
            ...(reviewAnswers[questionId] || {}),
            [field]: value
        };
        updateReviewProgress();
    }

    function normalizeExistingAnswer(question) {
        const answer = question.answer || question.existing_answer || {};

        return {
            rating: answer.rating ?? null,
            answer_text: answer.answer_text ?? null,
            answer_number: answer.answer_number ?? null,
            answer_yes_no: answer.answer_yes_no ?? null,
            comment: answer.comment ?? null
        };
    }

    function isQuestionAnswered(question) {
        const answer = reviewAnswers[question.id] || {};

        return (answer.rating !== null && answer.rating !== undefined && answer.rating !== '') ||
            (answer.answer_text !== null && answer.answer_text !== undefined && String(answer.answer_text).trim() !== '') ||
            (answer.answer_number !== null && answer.answer_number !== undefined && answer.answer_number !== '') ||
            (answer.answer_yes_no !== null && answer.answer_yes_no !== undefined && answer.answer_yes_no !== '');
    }

    function updateReviewProgress() {
        const total = reviewQuestions.length;
        const answered = reviewQuestions.filter(question => isQuestionAnswered(question)).length;
        const requiredQuestions = reviewQuestions.filter(question => Number(question.is_required) === 1 || question.is_required === true);
        const requiredAnswered = requiredQuestions.filter(question => isQuestionAnswered(question)).length;
        const percentage = total ? Math.round((answered / total) * 100) : 0;

        $('#progressPercentage').text(percentage + '%');
        $('#progressBar').css('width', percentage + '%');
        $('#answeredCount, #bottomAnsweredCount').text(answered);
        $('#totalQuestions, #bottomTotalQuestions').text(total);
        $('#requiredAnsweredCount').text(requiredAnswered);
        $('#requiredQuestionsCount').text(requiredQuestions.length);

        updateOverallScore();
    }

    function updateOverallScore() {
        const ratings = reviewQuestions
            .map(question => Number(reviewAnswers[question.id]?.rating))
            .filter(value => Number.isFinite(value) && value > 0);

        if (!ratings.length) {
            $('#overallScore').text('Not Rated');
            return;
        }

        const average = ratings.reduce((total, value) => total + value, 0) / ratings.length;
        $('#overallScore').text(average.toFixed(2));
    }

    function getReviewPayload() {
        return {
            overall_comment: $('#overallComment').val().trim(),
            answers: reviewQuestions.map(question => ({
                question_id: question.id,
                rating: reviewAnswers[question.id]?.rating ?? null,
                answer_text: reviewAnswers[question.id]?.answer_text ?? null,
                answer_number: reviewAnswers[question.id]?.answer_number ?? null,
                answer_yes_no: reviewAnswers[question.id]?.answer_yes_no ?? null,
                comment: reviewAnswers[question.id]?.comment ?? null
            }))
        };
    }

    function saveDraft() {
        const $buttons = $('#btnSaveDraft, #btnSaveDraftBottom');

        if ($buttons.prop('disabled')) return;

        $buttons.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: '<?= base_url('my-reviews/review') ?>/' + REVIEW_ID + '/save-draft',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(getReviewPayload()),
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to save draft.');
                    return;
                }

                APP.success(response.message || 'Draft saved successfully.');
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;
                APP.error(xhr.responseJSON?.message || 'Unable to save draft.');
            },
            complete() {
                $buttons.prop('disabled', false).html('<i class="bi bi-cloud-arrow-down me-1"></i> Save Draft');
            }
        });
    }

    function submitReview() {
        const requiredQuestions = reviewQuestions.filter(question => Number(question.is_required) === 1 || question.is_required === true);
        const unansweredRequired = requiredQuestions.filter(question => !isQuestionAnswered(question));

        if (unansweredRequired.length) {
            APP.error('Please complete all required questions before submitting the review.');
            return;
        }

        const $buttons = $('#btnSubmitReview, #btnSubmitReviewBottom');

        if ($buttons.prop('disabled')) return;

        $buttons.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Submitting...');

        $.ajax({
            url: '<?= base_url('my-reviews/review') ?>/' + REVIEW_ID + '/submit',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(getReviewPayload()),
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to submit review.');
                    return;
                }

                APP.success(response.message || 'Review submitted successfully.');

                setTimeout(() => {
                    window.location.href = '<?= base_url('my-reviews') ?>';
                }, 800);
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;
                APP.error(xhr.responseJSON?.message || 'Unable to submit review.');
            },
            complete() {
                $buttons.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Submit Review');
            }
        });
    }

    function renderReviewStatus(status) {
        const statuses = {
            pending: '<span class="badge bg-secondary-subtle text-secondary border">Pending</span>',
            in_progress: '<span class="badge bg-primary-subtle text-primary border">In Progress</span>',
            submitted: '<span class="badge bg-success-subtle text-success border">Submitted</span>',
            approved: '<span class="badge bg-success-subtle text-success border">Approved</span>',
            rejected: '<span class="badge bg-danger-subtle text-danger border">Rejected</span>'
        };

        return statuses[status] || '';
    }

    function formatReviewDate(value) {
        if (!value) return '-';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return value;
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }
</script>

<?= $this->endSection() ?>