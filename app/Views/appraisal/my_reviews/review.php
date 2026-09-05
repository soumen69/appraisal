<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div id="reviewPage">
    <div class="rv-header">
        <div class="rv-header-row">
            <a href="<?= base_url('my-reviews') ?>" class="btn btn-light border btn-sm rv-back"><i class="bi bi-arrow-left"></i></a>
            <div class="rv-title-block">
                <div class="rv-title-line">
                    <h1 id="reviewTitle">Appraisal Review</h1>
                    <span id="reviewStatusBadge"></span>
                </div>
                <div class="rv-context" id="reviewContext">
                    <span id="cycleName">-</span><span class="rv-dot">•</span><span id="templateName">-</span><span class="rv-dot">•</span><span id="appraisalPeriod">-</span>
                </div>
            </div>
        </div>
    </div>

    <div id="reviewLockedNotice" class="rv-locked-banner d-none">
        <i class="bi bi-lock-fill"></i>
        <span id="reviewLockedText">This review has been finalized and can no longer be edited.</span>
    </div>

    <div id="reviewLoading" class="rv-skeleton">
        <div class="rv-skel rv-skel-line" style="width:40%;height:22px;"></div>
        <div class="rv-skel rv-skel-line" style="width:60%;height:14px;margin-top:8px;"></div>
        <div class="rv-skel-section">
            <div class="rv-skel rv-skel-line" style="width:30%;height:16px;"></div>
            <div class="rv-skel rv-skel-row"></div>
            <div class="rv-skel rv-skel-row"></div>
            <div class="rv-skel rv-skel-row"></div>
        </div>
        <div class="rv-skel-section">
            <div class="rv-skel rv-skel-line" style="width:35%;height:16px;"></div>
            <div class="rv-skel rv-skel-row"></div>
            <div class="rv-skel rv-skel-row"></div>
        </div>
    </div>

    <div id="reviewError" class="rv-state-panel d-none">
        <i class="bi bi-exclamation-circle"></i>
        <div class="rv-state-title">Unable to load review</div>
        <p id="reviewErrorMessage" class="rv-state-text">Unable to load appraisal review.</p>
        <a href="<?= base_url('my-reviews') ?>" class="btn btn-primary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to My Reviews</a>
    </div>

    <div id="reviewContent" class="d-none">
        <div id="reviewSections"></div>

        <div class="rv-overall" id="reviewOverall">
            <button type="button" class="rv-overall-toggle" id="overallToggle">
                <span><i class="bi bi-chat-square-text"></i> Overall assessment</span>
                <i class="bi bi-chevron-down" id="overallChevron"></i>
            </button>
            <div class="rv-overall-body" id="overallBody">
                <textarea class="form-control" id="overallComment" rows="3" placeholder="Add any overall comments about your performance, achievements, challenges or goals..."></textarea>
            </div>
        </div>
    </div>
</div>

<div class="rv-floatbar d-none" id="reviewFloatBar">
    <div class="rv-fb-progress">
        <span class="rv-fb-percent" id="progressPercentage">0%</span>
        <div class="rv-fb-track">
            <div class="rv-fb-fill" id="progressBar"></div>
        </div>
    </div>
    <div class="rv-fb-stats">
        <span title="Questions answered"><span id="answeredCount">0</span>/<span id="totalQuestions">0</span> done</span>
        <span class="rv-fb-sep"></span>
        <span title="Required questions answered">Req <span id="requiredAnsweredCount">0</span>/<span id="requiredQuestionsCount">0</span></span>
        <span class="rv-fb-sep"></span>
        <span title="Weighted average of rated questions">Score <strong id="overallScore">Not Rated</strong></span>
        <span class="rv-fb-save-state" id="saveStateIndicator"></span>
    </div>
    <div class="rv-fb-actions">
        <button type="button" class="btn btn-light border btn-sm" id="btnSaveDraft" disabled><i class="bi bi-cloud-arrow-down me-1"></i>Save Draft</button>
        <button type="button" class="btn btn-primary btn-sm" id="btnSubmitReview" disabled><i class="bi bi-check2-circle me-1"></i>Submit Review</button>
    </div>
</div>

<style>
    #reviewPage {
        max-width: 1180px;
        margin: 0 auto;
        padding: 1rem 1rem 6.5rem;
    }

    .rv-header {
        padding-bottom: .75rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--bs-border-color);
    }

    .rv-header-row {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
    }

    .rv-back {
        flex-shrink: 0;
        margin-top: .1rem;
    }

    .rv-title-block {
        min-width: 0;
        flex: 1;
    }

    .rv-title-line {
        display: flex;
        align-items: center;
        gap: .6rem;
        flex-wrap: wrap;
    }

    .rv-title-line h1 {
        font-size: 1.15rem;
        font-weight: 600;
        margin: 0;
    }

    .rv-context {
        font-size: .8rem;
        color: var(--bs-secondary-color);
        margin-top: .2rem;
    }

    .rv-dot {
        margin: 0 .45rem;
        opacity: .5;
    }

    .rv-locked-banner {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .8rem;
        padding: .5rem .75rem;
        background: var(--bs-warning-bg-subtle);
        border: 1px solid var(--bs-warning-border-subtle);
        border-radius: .4rem;
        margin-bottom: 1rem;
    }

    .rv-state-panel {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--bs-secondary-color);
    }

    .rv-state-panel i {
        font-size: 2rem;
        color: var(--bs-danger);
    }

    .rv-state-title {
        font-weight: 600;
        font-size: 1rem;
        margin-top: .75rem;
        color: var(--bs-body-color);
    }

    .rv-state-text {
        font-size: .85rem;
        margin: .35rem 0 1rem;
    }

    .rv-skel-line,
    .rv-skel-row {
        background: var(--bs-secondary-bg);
        border-radius: .3rem;
        animation: rv-pulse 1.3s ease-in-out infinite;
    }

    .rv-skel-row {
        height: 40px;
        margin-top: .6rem;
    }

    .rv-skel-section {
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid var(--bs-border-color);
    }

    @keyframes rv-pulse {

        0%,
        100% {
            opacity: .55;
        }

        50% {
            opacity: 1;
        }
    }

    .rv-section {
        border: 1px solid var(--bs-border-color);
        border-radius: .5rem;
        margin-bottom: .75rem;
        overflow: hidden;
    }

    /* .rv-section-head {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .55rem .85rem;
        background: var(--bs-tertiary-bg);
        border-bottom: 1px solid var(--bs-border-color);
    } */


    .rv-section-head {
        width: 100%;
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .65rem .85rem;
        background: var(--bs-tertiary-bg);
        border: none;
        cursor: pointer;
        text-align: left;
    }

    .rv-section-head:hover {
        background: var(--bs-secondary-bg);
    }

    .rv-section-head:focus {
        outline: none;
    }

    .rv-section-chevron {
        font-size: .8rem;
        color: var(--bs-secondary-color);
        transition: transform .2s ease;
        flex-shrink: 0;
    }

    .rv-section.rv-collapsed .rv-section-chevron {
        transform: rotate(-90deg);
    }

    .rv-section-index {
        font-size: .7rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
        width: 1.4rem;
        flex-shrink: 0;
    }

    .rv-section-name {
        font-weight: 600;
        font-size: .88rem;
        flex: 1;
        min-width: 0;
    }

    .rv-section-desc {
        font-size: .75rem;
        color: var(--bs-secondary-color);
        margin-top: .1rem;
    }

    .rv-section-count {
        font-size: .72rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
        white-space: nowrap;
    }

    .rv-section-count.rv-complete {
        color: var(--bs-success);
    }

    .rv-section-bar {
        height: 3px;
        background: var(--bs-border-color-translucent);
    }

    .rv-section-bar-fill {
        height: 100%;
        background: var(--bs-primary);
        transition: width .2s ease;
    }

    .rv-section-body {
        display: block;
    }

    .rv-section.rv-collapsed .rv-section-body {
        display: none;
    }

    .rv-section.rv-collapsed .rv-section-head {
        border-bottom: none;
    }

    .rv-question {
        display: flex;
        gap: .65rem;
        padding: .7rem .85rem;
        border-bottom: 1px solid var(--bs-border-color);
        position: relative;
    }

    .rv-question:last-child {
        border-bottom: none;
    }

    .rv-question.rv-answered {
        background: var(--bs-success-bg-subtle);
    }

    .rv-question.rv-flag-error {
        background: var(--bs-danger-bg-subtle);
    }

    .rv-q-number {
        font-size: .7rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
        width: 1.4rem;
        flex-shrink: 0;
        padding-top: .15rem;
    }

    .rv-q-body {
        flex: 1;
        min-width: 0;
    }

    .rv-q-text {
        font-size: .85rem;
        font-weight: 500;
        line-height: 1.35;
    }

    .rv-q-req {
        color: var(--bs-danger);
        margin-left: .15rem;
    }

    .rv-q-answer {
        margin-top: .5rem;
        max-width: 640px;
    }

    .rv-q-status {
        width: 1.1rem;
        flex-shrink: 0;
        text-align: center;
        padding-top: .2rem;
        color: var(--bs-success);
        font-size: .95rem;
    }

    .rv-answer-text {
        min-height: 2.4rem;
        resize: none;
        overflow: hidden;
        font-size: .85rem;
    }

    .rv-answer-number {
        max-width: 160px;
        font-size: .85rem;
    }

    .rv-rating {
        display: flex;
        gap: .35rem;
        flex-wrap: wrap;
    }

    .rv-rating-opt {
        min-width: 2.6rem;
        padding: .3rem .5rem;
        border: 1px solid var(--bs-border-color);
        border-radius: .35rem;
        background: var(--bs-body-bg);
        cursor: pointer;
        text-align: center;
        font-size: .8rem;
        font-weight: 600;
        line-height: 1.15;
        transition: background .12s ease, border-color .12s ease, color .12s ease;
    }

    .rv-rating-opt .rv-rating-label {
        display: block;
        font-size: .62rem;
        font-weight: 400;
        color: var(--bs-secondary-color);
        white-space: nowrap;
    }

    .rv-rating-opt:hover {
        border-color: var(--bs-primary);
    }

    .rv-rating-opt.active {
        background: var(--bs-primary);
        border-color: var(--bs-primary);
        color: #fff;
    }

    .rv-rating-opt.active .rv-rating-label {
        color: rgba(255, 255, 255, .85);
    }

    .rv-rating-opt:disabled,
    .rv-rating-opt.rv-disabled {
        pointer-events: none;
        opacity: .65;
    }

    .rv-yesno {
        display: inline-flex;
        border: 1px solid var(--bs-border-color);
        border-radius: .35rem;
        overflow: hidden;
    }

    .rv-yesno-opt {
        padding: .3rem .85rem;
        font-size: .8rem;
        font-weight: 600;
        background: var(--bs-body-bg);
        border: none;
        cursor: pointer;
        color: var(--bs-secondary-color);
    }

    .rv-yesno-opt+.rv-yesno-opt {
        border-left: 1px solid var(--bs-border-color);
    }

    .rv-yesno-opt.active-yes {
        background: var(--bs-success);
        color: #fff;
    }

    .rv-yesno-opt.active-no {
        background: var(--bs-danger);
        color: #fff;
    }

    .rv-comment-toggle {
        border: none;
        background: none;
        padding: 0;
        font-size: .74rem;
        color: var(--bs-primary);
        margin-top: .4rem;
        font-weight: 500;
    }

    .rv-comment-input {
        margin-top: .4rem;
        font-size: .8rem;
        max-width: 640px;
    }

    .rv-overall {
        border: 1px solid var(--bs-border-color);
        border-radius: .5rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .rv-overall-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .6rem .85rem;
        background: var(--bs-tertiary-bg);
        border: none;
        font-size: .85rem;
        font-weight: 600;
    }

    .rv-overall-body {
        padding: .75rem .85rem;
        display: none;
    }

    .rv-overall-body.rv-open {
        display: block;
    }

    .rv-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: var(--bs-secondary-color);
    }

    .rv-empty i {
        font-size: 1.8rem;
    }

    .rv-floatbar {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        padding: .55rem .9rem;
        margin: 0 auto;
        max-width: 1180px;
        background: rgba(var(--bs-body-bg-rgb), .92);
        backdrop-filter: blur(8px);
        border: 1px solid var(--bs-border-color);
        border-radius: .6rem;
        box-shadow: 0 .35rem 1rem rgba(0, 0, 0, .08);
    }

    .rv-fb-progress {
        display: flex;
        align-items: center;
        gap: .5rem;
        flex-shrink: 0;
    }

    .rv-fb-percent {
        font-size: .8rem;
        font-weight: 700;
        color: var(--bs-primary);
        width: 2.4rem;
    }

    .rv-fb-track {
        width: 70px;
        height: 5px;
        border-radius: 3px;
        background: var(--bs-border-color-translucent);
        overflow: hidden;
    }

    .rv-fb-fill {
        height: 100%;
        background: var(--bs-primary);
        transition: width .2s ease;
    }

    .rv-fb-stats {
        display: flex;
        align-items: center;
        gap: .55rem;
        font-size: .74rem;
        color: var(--bs-secondary-color);
        flex-wrap: wrap;
        flex: 1;
        min-width: 0;
    }

    .rv-fb-sep {
        width: 1px;
        height: 12px;
        background: var(--bs-border-color);
    }

    .rv-fb-save-state {
        font-size: .72rem;
        font-style: italic;
    }

    .rv-fb-actions {
        display: flex;
        gap: .5rem;
        margin-left: auto;
    }

    @media (max-width: 767px) {
        #reviewPage {
            padding: .75rem .75rem 8rem;
        }

        .rv-floatbar {
            flex-direction: column;
            align-items: stretch;
            gap: .5rem;
            border-radius: .5rem;
        }

        .rv-fb-actions {
            margin-left: 0;
        }

        .rv-fb-actions .btn {
            flex: 1;
        }

        .rv-fb-stats {
            order: 3;
        }
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>

<script>
    const REVIEW_ID = <?= (int) $reviewId ?>;
    const REVIEW_BASE_URL = '<?= base_url('my-reviews/review') ?>';
    let reviewData = null;
    let reviewAnswers = {};
    let reviewQuestions = [];
    let reviewLocked = false;
    let saveState = 'idle';

    $(function() {
        loadReviewData();
        $('#btnSaveDraft').on('click', saveDraft);
        $('#btnSubmitReview').on('click', submitReview);
        $('#overallToggle').on('click', () => {
            $('#overallBody').toggleClass('rv-open');
            $('#overallChevron').toggleClass('bi-chevron-down bi-chevron-up');
        });
        $(document).on('click', '.rv-section-head', function() {
            const $section = $(this).closest('.rv-section');
            $section.toggleClass('rv-collapsed');
        });
        $(document).on('input change', '.review-answer-input', handleAnswerChange);
        $(document).on('input', '.rv-answer-text', autoSizeTextarea);
        $(document).on('click', '.rv-rating-opt', handleRatingSelection);
        $(document).on('click', '.rv-yesno-opt', handleYesNoSelection);
        $(document).on('click', '.rv-comment-toggle', function() {
            $(this).next('.rv-comment-input').removeClass('d-none').trigger('focus');
            $(this).addClass('d-none');
        });
        $(document).on('input', '#overallComment', () => setSaveState('unsaved'));
    });

    function loadReviewData() {
        $.ajax({
            url: REVIEW_BASE_URL + '/' + REVIEW_ID + '/data',
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
        const review = reviewData.review || {};
        const cycle = reviewData.cycle || {};
        const template = reviewData.template || {};

        $('#reviewLoading').addClass('d-none');
        $('#reviewContent').removeClass('d-none');
        $('#reviewFloatBar').removeClass('d-none');

        $('#reviewTitle').text(cycle.name || 'Appraisal Review');
        $('#cycleName').text(cycle.name || '-');
        $('#templateName').text(template.name || '-');
        $('#appraisalPeriod').text(formatReviewDate(cycle.start_date) + ' – ' + formatReviewDate(cycle.end_date));
        $('#overallComment').val(review.overall_comment || '');
        $('#reviewStatusBadge').html(renderReviewStatus(review.status));

        reviewLocked = ['submitted', 'approved'].includes(review.status);
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
        applyLockedState(review.status);
    }

    function applyLockedState(status) {
        if (!reviewLocked) {
            $('#btnSaveDraft, #btnSubmitReview').prop('disabled', false);
            return;
        }

        const label = status === 'approved' ? 'This review has been approved and is now read-only.' : 'This review has been submitted and is now read-only.';
        $('#reviewLockedText').text(label);
        $('#reviewLockedNotice').removeClass('d-none');
        $('#overallComment').prop('readonly', true);
        $('.review-answer-input').prop('disabled', true);
        $('.rv-rating-opt, .rv-yesno-opt, .rv-comment-toggle').addClass('rv-disabled').prop('disabled', true);
        $('#btnSaveDraft, #btnSubmitReview').prop('disabled', true);
    }

    function renderSections(sections) {
        if (!sections.length) {
            $('#reviewSections').html(`
            <div class="rv-empty">
                <i class="bi bi-clipboard-x"></i>
                <div class="fw-semibold mt-2">No questions available</div>
                <p class="mb-0 small">No questions are configured for this appraisal template.</p>
            </div>
        `);
            return;
        }

        let html = '';

        sections.forEach((section, sectionIndex) => {
            const questions = section.questions || [];
            const answeredCount = questions.filter(isQuestionAnswered).length;
            const indexLabel = String(sectionIndex + 1).padStart(2, '0');

            html += `
            <div class="rv-section ${sectionIndex === 0 ? '' : 'rv-collapsed'}" data-section-id="${section.id}">
                <button type="button" class="rv-section-head">
                    <span class="rv-section-index">${indexLabel}</span>

                    <div class="rv-section-name">
                        ${CrudUtils.escapeHtml(section.section_name || 'Untitled section')}
                        ${section.description ? `<div class="rv-section-desc">${CrudUtils.escapeHtml(section.description)}</div>` : ''}
                    </div>

                    <span class="rv-section-count ${questions.length > 0 && answeredCount === questions.length ? 'rv-complete' : ''}" data-section-total="${questions.length}">
                        ${answeredCount} / ${questions.length}
                    </span>

                    <i class="bi bi-chevron-down rv-section-chevron"></i>
                </button>

                <div class="rv-section-body">
                    <div class="rv-section-bar">
                        <div class="rv-section-bar-fill" style="width:${questions.length ? (answeredCount / questions.length) * 100 : 0}%"></div>
                    </div>

                    <div class="rv-section-questions">
                        ${questions.map((question, questionIndex) => renderQuestion(question, questionIndex + 1)).join('')}
                    </div>
                </div>
            </div>
        `;
        });

        $('#reviewSections').html(html);

        $('.rv-answer-text').each(function() {
            autoSizeTextarea.call(this);
        });
    }

    function renderQuestion(question, questionNumber) {
        const answer = reviewAnswers[question.id] || {};
        const required = Number(question.is_required) === 1 || question.is_required === true;
        const questionText = CrudUtils.escapeHtml(question.question || 'Question');
        const answerType = question.answer_type || 'text';
        const answered = isQuestionAnswered(question);
        const hasComment = answer.comment !== null && answer.comment !== undefined && String(answer.comment).trim() !== '';
        const indexLabel = String(questionNumber).padStart(2, '0');

        return `
            <div class="rv-question ${answered ? 'rv-answered' : ''}" data-question-id="${question.id}" data-required="${required ? 1 : 0}">
                <div class="rv-q-number">${indexLabel}</div>
                <div class="rv-q-body">
                    <div class="rv-q-text">${questionText}${required ? '<span class="rv-q-req">*</span>' : ''}</div>
                    <div class="rv-q-answer">${renderQuestionInput(question, answer, answerType)}</div>
                    <button type="button" class="rv-comment-toggle ${hasComment ? 'd-none' : ''}"><i class="bi bi-plus-lg"></i> Add comment</button>
                    <textarea class="form-control form-control-sm rv-comment-input review-answer-input ${hasComment ? '' : 'd-none'}" data-question-id="${question.id}" data-answer-field="comment" rows="2" placeholder="Add supporting comments...">${CrudUtils.escapeHtml(answer.comment || '')}</textarea>
                </div>
                <div class="rv-q-status" data-status-icon>${answered ? '<i class="bi bi-check-circle-fill"></i>' : ''}</div>
            </div>
        `;
    }

    function renderQuestionInput(question, answer, answerType) {
        const questionId = question.id;

        switch (answerType) {
            case 'rating':
                return renderRatingInput(question, answer);

            case 'yes_no':
                return `
                    <div class="rv-yesno" data-question-id="${questionId}">
                        <button type="button" class="rv-yesno-opt review-answer-input ${Number(answer.answer_yes_no) === 1 ? 'active-yes' : ''}" data-question-id="${questionId}" data-answer-field="answer_yes_no" data-value="1" type="button"><i class="bi bi-check-lg"></i> Yes</button>
                        <button type="button" class="rv-yesno-opt review-answer-input ${answer.answer_yes_no !== null && Number(answer.answer_yes_no) === 0 ? 'active-no' : ''}" data-question-id="${questionId}" data-answer-field="answer_yes_no" data-value="0" type="button"><i class="bi bi-x-lg"></i> No</button>
                    </div>
                `;

            case 'number':
                return `<input type="number" step="0.01" class="form-control form-control-sm review-answer-input rv-answer-number" data-question-id="${questionId}" data-answer-field="answer_number" value="${answer.answer_number ?? ''}" placeholder="0.00">`;

            case 'text':
            default:
                return `<textarea class="form-control form-control-sm review-answer-input rv-answer-text" data-question-id="${questionId}" data-answer-field="answer_text" rows="1" placeholder="Enter your response...">${CrudUtils.escapeHtml(answer.answer_text || '')}</textarea>`;
        }
    }

    function renderRatingInput(question, answer) {
        const questionId = question.id;
        const maxRating = Number(question.max_rating || 5);
        const ratingValues = [];
        for (let value = 1; value <= maxRating; value++) ratingValues.push(value);

        return `
            <div class="rv-rating" data-question-id="${questionId}">
                ${ratingValues.map(value => `
                    <button type="button" class="rv-rating-opt review-answer-input ${Number(answer.rating) === value ? 'active' : ''}" data-question-id="${questionId}" data-answer-field="rating" data-value="${value}">${value}</button>
                `).join('')}
            </div>
        `;
    }

    function handleRatingSelection() {
        if (reviewLocked) return;
        const $button = $(this);
        const questionId = $button.data('question-id');
        const value = $button.data('value');

        reviewAnswers[questionId] = {
            ...(reviewAnswers[questionId] || {}),
            rating: value
        };
        $button.closest('.rv-rating').find('.rv-rating-opt').removeClass('active');
        $button.addClass('active');

        onAnswerUpdated(questionId);
    }

    function handleYesNoSelection() {
        if (reviewLocked) return;
        const $button = $(this);
        const questionId = $button.data('question-id');
        const value = $button.data('value');

        reviewAnswers[questionId] = {
            ...(reviewAnswers[questionId] || {}),
            answer_yes_no: value
        };
        const $group = $button.closest('.rv-yesno');
        $group.find('.rv-yesno-opt').removeClass('active-yes active-no');
        $button.addClass(Number(value) === 1 ? 'active-yes' : 'active-no');

        onAnswerUpdated(questionId);
    }

    function handleAnswerChange() {
        const $input = $(this);
        const questionId = $input.data('question-id');
        const field = $input.data('answer-field');
        if (!questionId || !field) return;

        reviewAnswers[questionId] = {
            ...(reviewAnswers[questionId] || {}),
            [field]: $input.val()
        };

        if (field === 'comment') {
            setSaveState('unsaved');
            return;
        }

        onAnswerUpdated(questionId);
    }

    function onAnswerUpdated(questionId) {
        const question = reviewQuestions.find(item => String(item.id) === String(questionId));
        if (!question) return;

        const answered = isQuestionAnswered(question);
        const $row = $(`.rv-question[data-question-id="${questionId}"]`);
        $row.toggleClass('rv-answered', answered).removeClass('rv-flag-error');
        $row.find('[data-status-icon]').html(answered ? '<i class="bi bi-check-circle-fill"></i>' : '');

        updateSectionCompletion(question.section_id);
        updateReviewProgress();
        setSaveState('unsaved');
    }

    function updateSectionCompletion(sectionId) {
        const $section = $(`.rv-section[data-section-id="${sectionId}"]`);
        if (!$section.length) return;

        const questionsInSection = reviewQuestions.filter(question => String(question.section_id) === String(sectionId));
        const total = questionsInSection.length;
        const answered = questionsInSection.filter(isQuestionAnswered).length;

        $section.find('.rv-section-count').text(`${answered} / ${total}`).toggleClass('rv-complete', total > 0 && answered === total);
        $section.find('.rv-section-bar-fill').css('width', total ? (answered / total) * 100 + '%' : '0%');
    }

    function autoSizeTextarea() {
        this.style.height = 'auto';
        this.style.height = Math.max(this.scrollHeight, 38) + 'px';
    }

    function normalizeExistingAnswer(question) {
        const answer = question.answer || {};
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
        const answered = reviewQuestions.filter(isQuestionAnswered).length;
        const requiredQuestions = reviewQuestions.filter(question => Number(question.is_required) === 1 || question.is_required === true);
        const requiredAnswered = requiredQuestions.filter(isQuestionAnswered).length;
        const percentage = total ? Math.round((answered / total) * 100) : 0;

        $('#progressPercentage').text(percentage + '%');
        $('#progressBar').css('width', percentage + '%');
        $('#answeredCount').text(answered);
        $('#totalQuestions').text(total);
        $('#requiredAnsweredCount').text(requiredAnswered);
        $('#requiredQuestionsCount').text(requiredQuestions.length);

        updateOverallScore();
    }

    function updateOverallScore() {
        let weightedSum = 0;
        let weightTotal = 0;

        reviewQuestions.forEach(question => {
            if ((question.answer_type || 'text') !== 'rating') return;
            const rating = Number(reviewAnswers[question.id]?.rating);
            if (!Number.isFinite(rating) || rating <= 0) return;
            const weight = Number(question.weight) > 0 ? Number(question.weight) : 1;
            weightedSum += rating * weight;
            weightTotal += weight;
        });

        $('#overallScore').text(weightTotal > 0 ? (weightedSum / weightTotal).toFixed(2) : 'Not Rated');
    }

    function setSaveState(state) {
        if (reviewLocked) return;
        saveState = state;
        const labels = {
            unsaved: 'Unsaved changes',
            saving: 'Saving…',
            saved: 'Saved'
        };
        $('#saveStateIndicator').text(labels[state] || '');
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
        if ($('#btnSaveDraft').prop('disabled')) return;

        setSaveState('saving');
        $('#btnSaveDraft').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

        $.ajax({
            url: REVIEW_BASE_URL + '/' + REVIEW_ID + '/save-draft',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(getReviewPayload()),
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to save draft.');
                    setSaveState('unsaved');
                    return;
                }
                APP.success(response.message || 'Draft saved successfully.');
                setSaveState('saved');
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;
                APP.error(xhr.responseJSON?.message || 'Unable to save draft.');
                setSaveState('unsaved');
            },
            complete() {
                $('#btnSaveDraft').prop('disabled', reviewLocked).html('<i class="bi bi-cloud-arrow-down me-1"></i>Save Draft');
            }
        });
    }

    function submitReview() {
        const requiredQuestions = reviewQuestions.filter(question => Number(question.is_required) === 1 || question.is_required === true);
        const unansweredRequired = requiredQuestions.filter(question => !isQuestionAnswered(question));

        $('.rv-question').removeClass('rv-flag-error');

        if (unansweredRequired.length) {
            unansweredRequired.forEach(question => $(`.rv-question[data-question-id="${question.id}"]`).addClass('rv-flag-error'));
            
            const $first = $(`.rv-question[data-question-id="${unansweredRequired[0].id}"]`);

            if ($first.length) {
                $first.closest('.rv-section').removeClass('rv-collapsed');

                setTimeout(() => {
                    $first[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 100);
            }
            APP.error('Please complete all required questions before submitting the review.');
            return;
        }

        if ($('#btnSubmitReview').prop('disabled')) return;

        $('#btnSubmitReview').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Submitting...');

        $.ajax({
            url: REVIEW_BASE_URL + '/' + REVIEW_ID + '/submit',
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
                $('#btnSubmitReview').prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i>Submit Review');
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