$(function () {
    const templateId = window.templateBuilderConfig.templateId;
    const baseUrl = window.templateBuilderConfig.baseUrl;
    let builderData = null;
    loadBuilder();

    function loadBuilder() {
        $('#templateBuilder').html(renderLoading());
        $.ajax({
            url: `${baseUrl}/${templateId}/builder-data`,
            type: 'GET',
            success(response) {
                if (!response.success) {
                    $('#templateBuilder').html(renderError(response.message || 'Unable to load template.'));
                    return;
                }
                builderData = response.data;
                renderBuilder();
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) {
                    return;
                }
                $('#templateBuilder').html(renderError(xhr.responseJSON?.message || 'Unable to load template.'));
            }
        });
    }

    function renderBuilder() {
        const sections = builderData.sections || [];
        const questionCount = sections.reduce((total, section) => total + (section.questions || []).length, 0);
        $('#sectionCount').text(sections.length);
        $('#questionCount').text(questionCount);

        let html = '';

        if (!sections.length) {
            html = renderEmptyState();
        } else {
            sections.forEach(function (section, index) {
                html += renderSection(section, index);
            });
        }

        $('#templateBuilder').html(html);
        initializeSortable();
    }

    function renderSection(section, index) {
        const questions = section.questions || [];
        const requiredCount = questions.filter(question => Number(question.is_required) === 1).length;

        let questionsHtml = '';

        if (!questions.length) {
            questionsHtml = `
                <div class="text-center text-muted py-4 border-top">
                    <i class="bi bi-question-circle fs-5 d-block mb-1"></i>
                    <small>No questions added yet.</small>
                </div>
            `;
        } else {
            questions.forEach(function (question, questionIndex) {
                questionsHtml += renderQuestion(question, questionIndex);
            });
        }

        return `
            <div class="card border shadow-sm mb-2 builder-section" data-id="${section.id}">
                <div class="card-header bg-white border-0 p-0 builder-section-header">
                    <div class="d-flex align-items-center px-3 py-2 gap-3">
                        <div class="section-drag-handle text-muted flex-shrink-0" data-bs-toggle="tooltip" title="Drag to reorder">
                            <i class="bi bi-grip-vertical fs-5"></i>
                        </div>

                        <button type="button" class="btn btn-sm p-0 border-0 text-muted btn-toggle-section">
                            <i class="bi bi-chevron-down section-collapse-icon"></i>
                        </button>

                        <div class="flex-shrink-0">
                            <span class="badge bg-primary-subtle text-primary section-number">
                                ${String(index + 1).padStart(2, '0')}
                            </span>
                        </div>

                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate">
                                ${escapeHtml(section.section_name)}
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <span class="badge bg-light text-dark border d-none d-md-inline-flex">
                                ${questions.length} ${questions.length === 1 ? 'Question' : 'Questions'}
                            </span>

                            ${requiredCount ? `
                                <span class="badge bg-danger-subtle text-danger d-none d-lg-inline-flex">
                                    ${requiredCount} Required
                                </span>
                            ` : ''}

                            <button type="button" class="btn btn-sm btn-primary btn-add-question" data-section-id="${section.id}">
                                <i class="bi bi-plus-lg me-1"></i>
                                <span class="d-none d-sm-inline">Question</span>
                            </button>

                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown" onclick="event.stopPropagation()">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="#" class="dropdown-item btn-edit-section" data-id="${section.id}" data-name="${escapeAttribute(section.section_name)}">
                                            <i class="bi bi-pencil me-2"></i>
                                            Edit Section
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item btn-add-question" data-section-id="${section.id}">
                                            <i class="bi bi-plus-lg me-2"></i>
                                            Add Question
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a href="#" class="dropdown-item text-danger btn-delete-section" data-id="${section.id}">
                                            <i class="bi bi-trash me-2"></i>
                                            Delete Section
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-content border-top">
                    <div class="question-list" data-section-id="${section.id}">
                        ${questionsHtml}
                    </div>
                </div>
            </div>
        `;
    }

    function renderQuestion(question, index) {
        return `
            <div class="builder-question border-bottom px-3 py-2" data-id="${question.id}">
                <div class="d-flex align-items-center gap-3">
                    <div class="question-drag-handle text-muted flex-shrink-0">
                        <i class="bi bi-grip-vertical"></i>
                    </div>

                    <div class="text-muted small flex-shrink-0 question-number">
                        ${index + 1}.
                    </div>

                    <div class="question-text flex-grow-1">
                        <div class="fw-medium question-title" title="${escapeAttribute(question.question)}">
                            ${escapeHtml(question.question)}
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        ${renderAnswerType(question.answer_type)}

                        ${Number(question.is_required) === 1 ? `
                            <span class="badge bg-danger-subtle text-danger d-none d-sm-inline-flex">
                                Required
                            </span>
                        ` : ''}

                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border-0 py-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="#" class="dropdown-item btn-edit-question"
                                        data-id="${question.id}"
                                        data-question="${escapeAttribute(question.question)}"
                                        data-answer-type="${question.answer_type}"
                                        data-required="${question.is_required}">
                                        <i class="bi bi-pencil me-2"></i>
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item text-danger btn-delete-question" data-id="${question.id}">
                                        <i class="bi bi-trash me-2"></i>
                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderAnswerType(type) {
        const types = {
            rating: 'Rating',
            text: 'Text',
            number: 'Number',
            yes_no: 'Yes / No'
        };

        return `
            <span class="badge bg-light text-dark border">
                ${types[type] || escapeHtml(type)}
            </span>
        `;
    }

    $(document).on('click', '.builder-section-header', function (event) {
        if ($(event.target).closest('.section-drag-handle, .btn-add-question, .dropdown, .dropdown-menu, a').length) {
            return;
        }
        $(this).closest('.builder-section').toggleClass('collapsed');
    });

    $(document).on('click', '.btn-toggle-section', function (event) {
        event.stopPropagation();
        $(this).closest('.builder-section').toggleClass('collapsed');
    });

    $('#btnExpandAll').on('click', function () {
        $('.builder-section').removeClass('collapsed');
    });

    $('#btnCollapseAll').on('click', function () {
        $('.builder-section').addClass('collapsed');
    });

    $(document).on('click', '#btnAddSection', function () {
        $('#sectionModalTitle').text('Add Section');
        $('#sectionId').val('');
        $('#sectionName').val('');
        $('#sectionModal').modal('show');
    });

    $(document).on('submit', '#sectionForm', function (event) {
        event.preventDefault();

        const sectionId = $('#sectionId').val();
        const sectionName = $('#sectionName').val().trim();

        if (!sectionName) {
            APP.error('Section name is required.');
            return;
        }

        const url = sectionId
            ? `${baseUrl}/sections/${sectionId}/update`
            : `${baseUrl}/${templateId}/sections`;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                section_name: sectionName
            },
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to save section.');
                    return;
                }

                $('#sectionModal').modal('hide');
                APP.success(response.message || 'Section saved successfully.');
                loadBuilder();
            },
            error(xhr) {
                handleValidationError(xhr);
            }
        });
    });

    $(document).on('click', '.btn-edit-section', function (event) {
        event.preventDefault();

        $('#sectionModalTitle').text('Edit Section');
        $('#sectionId').val($(this).data('id'));
        $('#sectionName').val($(this).data('name'));
        $('#sectionModal').modal('show');
    });

    $(document).on('click', '.btn-delete-section', function (event) {
        event.preventDefault();

        const sectionId = $(this).data('id');

        if (!confirm('Are you sure you want to delete this section and all its questions?')) {
            return;
        }

        $.ajax({
            url: `${baseUrl}/sections/${sectionId}/delete`,
            type: 'POST',
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to delete section.');
                    return;
                }

                APP.success(response.message || 'Section deleted successfully.');
                loadBuilder();
            },
            error(xhr) {
                APP.error(xhr.responseJSON?.message || 'Unable to delete section.');
            }
        });
    });

    $(document).on('click', '.btn-add-question', function (event) {
        event.preventDefault();
        event.stopPropagation();

        const sectionId = $(this).data('section-id');

        $('#questionModalTitle').text('Add Question');
        $('#questionId').val('');
        $('#questionSectionId').val(sectionId);
        $('#questionText').val('');
        $('#answerType').val('rating');
        $('#isRequired').prop('checked', false);
        $('#questionModal').modal('show');
    });

    $(document).on('click', '.btn-edit-question', function (event) {
        event.preventDefault();

        const button = $(this);

        $('#questionModalTitle').text('Edit Question');
        $('#questionId').val(button.data('id'));
        $('#questionText').val(button.data('question'));
        $('#answerType').val(button.data('answer-type'));
        $('#isRequired').prop(
            'checked',
            Number(button.data('required')) === 1
        );

        $('#questionModal').modal('show');
    });

    $(document).on('submit', '#questionForm', function (event) {
        event.preventDefault();

        const questionId = $('#questionId').val();
        const sectionId = $('#questionSectionId').val();
        const question = $('#questionText').val().trim();

        if (!question) {
            APP.error('Question is required.');
            return;
        }

        const url = questionId
            ? `${baseUrl}/questions/${questionId}/update`
            : `${baseUrl}/sections/${sectionId}/questions`;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                question: question,
                answer_type: $('#answerType').val(),
                is_required: $('#isRequired').is(':checked') ? 1 : 0
            },
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to save question.');
                    return;
                }

                $('#questionModal').modal('hide');
                APP.success(response.message || 'Question saved successfully.');
                loadBuilder();
            },
            error(xhr) {
                handleValidationError(xhr);
            }
        });
    });

    $(document).on('click', '.btn-delete-question', function (event) {
        event.preventDefault();

        const questionId = $(this).data('id');

        if (!confirm('Are you sure you want to delete this question?')) {
            return;
        }

        $.ajax({
            url: `${baseUrl}/questions/${questionId}/delete`,
            type: 'POST',
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to delete question.');
                    return;
                }

                APP.success(response.message || 'Question deleted successfully.');
                loadBuilder();
            },
            error(xhr) {
                APP.error(xhr.responseJSON?.message || 'Unable to delete question.');
            }
        });
    });

    function initializeSortable() {
        if (typeof Sortable === 'undefined') {
            return;
        }

        const builder = document.getElementById('templateBuilder');

        if (builder) {
            new Sortable(builder, {
                animation: 150,
                handle: '.section-drag-handle',
                draggable: '.builder-section',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd() {
                    updateSectionNumbers();
                    const sectionIds = Array.from(builder.querySelectorAll('.builder-section')).map(element => element.dataset.id);
                    reorderSections(sectionIds);
                }
            });
        }

        document.querySelectorAll('.question-list').forEach(function (element) {
            new Sortable(element, {
                animation: 150,
                handle: '.question-drag-handle',
                draggable: '.builder-question',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd() {
                    updateQuestionNumbers(element);
                    const sectionId = element.dataset.sectionId;
                    const questionIds = Array.from(element.querySelectorAll('.builder-question')).map(question => question.dataset.id);
                    reorderQuestions(sectionId, questionIds);
                }
            });
        });
    }

    function updateSectionNumbers() {
        $('#templateBuilder')
            .find('.builder-section')
            .each(function (index) {
                $(this)
                    .find('.section-number')
                    .text(
                        String(index + 1).padStart(2, '0')
                    );
            });
    }

    function updateQuestionNumbers(questionList) {
        $(questionList)
            .find('.builder-question').each(
                function (index) {
                    $(this).find('.question-number').text(`${index + 1}.`);
                }
            );
    }

    function reorderSections(sectionIds) {
        $.ajax({
            url: `${baseUrl}/${templateId}/sections/reorder`,
            type: 'POST',
            data: {
                section_ids: sectionIds
            },
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to reorder sections.');
                    loadBuilder();
                }
            },
            error(xhr) {
                APP.error(xhr.responseJSON?.message || 'Unable to reorder sections.');
                loadBuilder();
            }
        });
    }

    function reorderQuestions(sectionId, questionIds) {
        $.ajax({
            url: `${baseUrl}/sections/${sectionId}/questions/reorder`,
            type: 'POST',
            data: {
                question_ids: questionIds
            },
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to reorder questions.');
                    loadBuilder();
                }
            },
            error(xhr) {
                APP.error(xhr.responseJSON?.message || 'Unable to reorder questions.');
                loadBuilder();
            }
        });
    }

    function handleValidationError(xhr) {
        if (APP.handleUnauthorized(xhr)) {
            return;
        }

        const response = xhr.responseJSON || {};

        if (response.errors) {
            APP.error(Object.values(response.errors).join('<br>'));
            return;
        }

        APP.error(response.message || 'Something went wrong.');
    }

    function renderLoading() {
        return `
            <div class="text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Loading template...
            </div>
        `;
    }

    function renderError(message) {
        return `
            <div class="alert alert-danger">
                ${escapeHtml(message)}
            </div>
        `;
    }

    function renderEmptyState() {
        return `
            <div class="card border shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-ui-checks-grid fs-1 d-block mb-3"></i>
                    <h6 class="mb-1">No sections yet</h6>
                    <p class="mb-3 small">
                        Start building your appraisal template by adding the first section.
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" id="btnAddSection">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add First Section
                    </button>
                </div>
            </div>
        `;
    }

    function escapeHtml(value) {
        return $('<div>')
            .text(value ?? '')
            .html();
    }

    function escapeAttribute(value) {
        return escapeHtml(value)
            .replace(/"/g, '&quot;');
    }
});