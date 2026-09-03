$(function () {

    const templateId =
        window.templateBuilderConfig.templateId;

    const baseUrl =
        window.templateBuilderConfig.baseUrl;

    let builderData = null;


    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    loadBuilder();


    /*
    |--------------------------------------------------------------------------
    | Load Builder
    |--------------------------------------------------------------------------
    */

    function loadBuilder() {

        $('#templateBuilder')
            .html(renderLoading());


        $.ajax({

            url:
                `${baseUrl}/${templateId}/builder-data`,

            type: 'GET',

            success(response) {

                if (!response.success) {

                    $('#templateBuilder')
                        .html(renderError(
                            response.message ||
                            'Unable to load template.'
                        ));

                    return;
                }


                builderData =
                    response.data;


                renderBuilder();

            },

            error(xhr) {

                if (
                    APP.handleUnauthorized(xhr)
                ) {
                    return;
                }


                $('#templateBuilder')
                    .html(renderError(
                        xhr.responseJSON?.message ||
                        'Unable to load template.'
                    ));
            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Render Builder
    |--------------------------------------------------------------------------
    */

    function renderBuilder() {

        const template =
            builderData.template;


        const sections =
            builderData.sections || [];


        $('#builderTemplateName')
            .text(
                template.template_name ||
                'Untitled Template'
            );


        $('#builderOrganization')
            .text(
                template.organization_name ||
                '-'
            );


        $('#sectionCount')
            .text(
                sections.length
            );


        let html = '';


        if (!sections.length) {

            html = renderEmptyState();

        } else {

            sections.forEach(
                function (section, index) {

                    html +=
                        renderSection(
                            section,
                            index
                        );

                }
            );

        }


        $('#templateBuilder')
            .html(html);


        initializeSortable();

    }


    /*
    |--------------------------------------------------------------------------
    | Section Renderer
    |--------------------------------------------------------------------------
    */

    function renderSection(
        section,
        index
    ) {

        const questions =
            section.questions || [];


        let questionsHtml = '';


        if (!questions.length) {

            questionsHtml = `

                <div
                    class="text-center
                    text-muted
                    py-4">

                    <i
                        class="bi bi-question-circle
                        fs-3
                        d-block
                        mb-2">
                    </i>

                    <small>

                        No questions added yet.

                    </small>

                </div>

            `;

        } else {

            questions.forEach(
                function (question, questionIndex) {

                    questionsHtml +=
                        renderQuestion(
                            question,
                            questionIndex
                        );

                }
            );

        }


        return `

            <div
                class="card
                border
                shadow-sm
                mb-3
                builder-section"
                data-id="${section.id}">

                <div
                    class="card-header
                    bg-white
                    d-flex
                    align-items-center
                    justify-content-between">

                    <div
                        class="d-flex
                        align-items-center
                        gap-3">

                        <div
                            class="section-drag-handle
                            text-muted
                            cursor-move">

                            <i
                                class="bi bi-grip-vertical
                                fs-4">
                            </i>

                        </div>


                        <div>

                            <div
                                class="fw-semibold">

                                ${escapeHtml(
            section.section_name
        )}

                            </div>


                            <small
                                class="text-muted">

                                Section ${index + 1}

                            </small>

                        </div>

                    </div>


                    <div
                        class="dropdown">

                        <button
                            class="btn
                            btn-sm
                            btn-light"
                            type="button"
                            data-bs-toggle="dropdown">

                            <i
                                class="bi bi-three-dots-vertical">
                            </i>

                        </button>


                        <ul
                            class="dropdown-menu
                            dropdown-menu-end">

                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item
                                    btn-add-question"
                                    data-section-id="${section.id}">

                                    <i
                                        class="bi bi-plus-lg
                                        me-2">
                                    </i>

                                    Add Question

                                </a>

                            </li>


                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item
                                    btn-edit-section"
                                    data-id="${section.id}"
                                    data-name="${escapeAttribute(
            section.section_name
        )}">

                                    <i
                                        class="bi bi-pencil
                                        me-2">
                                    </i>

                                    Edit Section

                                </a>

                            </li>


                            <li>

                                <hr
                                    class="dropdown-divider">

                            </li>


                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item
                                    text-danger
                                    btn-delete-section"
                                    data-id="${section.id}">

                                    <i
                                        class="bi bi-trash
                                        me-2">
                                    </i>

                                    Delete Section

                                </a>

                            </li>

                        </ul>

                    </div>

                </div>


                <div
                    class="card-body">

                    <div
                        class="question-list"
                        data-section-id="${section.id}">

                        ${questionsHtml}

                    </div>


                    <button
                        type="button"
                        class="btn
                        btn-outline-primary
                        btn-sm
                        mt-3
                        btn-add-question"
                        data-section-id="${section.id}">

                        <i
                            class="bi bi-plus-lg
                            me-1">
                        </i>

                        Add Question

                    </button>

                </div>

            </div>

        `;

    }


    /*
    |--------------------------------------------------------------------------
    | Question Renderer
    |--------------------------------------------------------------------------
    */

    function renderQuestion(
        question,
        index
    ) {

        return `

            <div
                class="border
                rounded
                p-3
                mb-2
                builder-question"
                data-id="${question.id}">

                <div
                    class="d-flex
                    align-items-start
                    justify-content-between">

                    <div
                        class="d-flex
                        align-items-start
                        gap-3
                        flex-grow-1">

                        <div
                            class="question-drag-handle
                            text-muted
                            cursor-move">

                            <i
                                class="bi bi-grip-vertical">
                            </i>

                        </div>


                        <div
                            class="flex-grow-1">

                            <div
                                class="fw-medium">

                                ${index + 1}.
                                ${escapeHtml(
            question.question
        )}

                            </div>


                            <div
                                class="mt-2">

                                ${renderAnswerType(
            question.answer_type
        )}

                                ${Number(
            question.is_required
        ) === 1

                ? `

                                        <span
                                            class="badge
                                            bg-danger-subtle
                                            text-danger">

                                            Required

                                        </span>

                                    `

                : ''
            }

                            </div>

                        </div>

                    </div>


                    <div
                        class="dropdown">

                        <button
                            class="btn
                            btn-sm
                            btn-light"
                            type="button"
                            data-bs-toggle="dropdown">

                            <i
                                class="bi bi-three-dots">
                            </i>

                        </button>


                        <ul
                            class="dropdown-menu
                            dropdown-menu-end">

                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item
                                    btn-edit-question"
                                    data-id="${question.id}"
                                    data-question="${escapeAttribute(
                question.question
            )}"
                                    data-answer-type="${question.answer_type}"
                                    data-required="${question.is_required}">

                                    <i
                                        class="bi bi-pencil
                                        me-2">
                                    </i>

                                    Edit

                                </a>

                            </li>


                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item
                                    text-danger
                                    btn-delete-question"
                                    data-id="${question.id}">

                                    <i
                                        class="bi bi-trash
                                        me-2">
                                    </i>

                                    Delete

                                </a>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        `;

    }


    /*
    |--------------------------------------------------------------------------
    | Answer Type
    |--------------------------------------------------------------------------
    */

    function renderAnswerType(type) {

        const types = {

            rating:
                'Rating',

            text:
                'Text',

            number:
                'Number',

            yes_no:
                'Yes / No'

        };


        return `

            <span
                class="badge
                bg-light
                text-dark
                border">

                ${types[type] || type}

            </span>

        `;

    }


    /*
    |--------------------------------------------------------------------------
    | Add Section
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '#btnAddSection',
        function () {

            $('#sectionModalTitle')
                .text(
                    'Add Section'
                );


            $('#sectionId')
                .val('');


            $('#sectionName')
                .val('');


            $('#sectionModal')
                .modal('show');

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Save Section
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        '#sectionForm',
        function (event) {

            event.preventDefault();


            const sectionId =
                $('#sectionId').val();


            const sectionName =
                $('#sectionName').val()
                    .trim();


            if (!sectionName) {

                APP.error(
                    'Section name is required.'
                );

                return;
            }


            const url =
                sectionId

                    ? `${baseUrl}/sections/${sectionId}/update`

                    : `${baseUrl}/${templateId}/sections`;


            $.ajax({

                url: url,

                type: 'POST',

                data: {

                    section_name:
                        sectionName

                },

                success(response) {

                    if (!response.success) {

                        APP.error(
                            response.message ||
                            'Unable to save section.'
                        );

                        return;
                    }


                    $('#sectionModal')
                        .modal('hide');


                    APP.success(
                        response.message ||
                        'Section saved successfully.'
                    );


                    loadBuilder();

                },

                error(xhr) {

                    handleValidationError(xhr);

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Edit Section
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.btn-edit-section',
        function (event) {

            event.preventDefault();


            $('#sectionModalTitle')
                .text(
                    'Edit Section'
                );


            $('#sectionId')
                .val(
                    $(this).data('id')
                );


            $('#sectionName')
                .val(
                    $(this).data('name')
                );


            $('#sectionModal')
                .modal('show');

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Delete Section
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.btn-delete-section',
        function (event) {

            event.preventDefault();


            const sectionId =
                $(this).data('id');


            if (
                !confirm(
                    'Are you sure you want to delete this section and all its questions?'
                )
            ) {
                return;
            }


            $.ajax({

                url:
                    `${baseUrl}/sections/${sectionId}/delete`,

                type: 'POST',

                success(response) {

                    if (!response.success) {

                        APP.error(
                            response.message ||
                            'Unable to delete section.'
                        );

                        return;
                    }


                    APP.success(
                        response.message ||
                        'Section deleted successfully.'
                    );


                    loadBuilder();

                },

                error(xhr) {

                    APP.error(
                        xhr.responseJSON?.message ||
                        'Unable to delete section.'
                    );

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Add Question
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.btn-add-question',
        function (event) {

            event.preventDefault();


            const sectionId =
                $(this).data(
                    'section-id'
                );


            $('#questionModalTitle')
                .text(
                    'Add Question'
                );


            $('#questionId')
                .val('');


            $('#questionSectionId')
                .val(sectionId);


            $('#questionText')
                .val('');


            $('#answerType')
                .val('rating');


            $('#isRequired')
                .prop(
                    'checked',
                    false
                );


            $('#questionModal')
                .modal('show');

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Edit Question
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.btn-edit-question',
        function (event) {

            event.preventDefault();


            const button =
                $(this);


            $('#questionModalTitle')
                .text(
                    'Edit Question'
                );


            $('#questionId')
                .val(
                    button.data('id')
                );


            $('#questionText')
                .val(
                    button.data('question')
                );


            $('#answerType')
                .val(
                    button.data(
                        'answer-type'
                    )
                );


            $('#isRequired')
                .prop(
                    'checked',

                    Number(
                        button.data(
                            'required'
                        )
                    ) === 1
                );


            $('#questionModal')
                .modal('show');

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Save Question
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        '#questionForm',
        function (event) {

            event.preventDefault();


            const questionId =
                $('#questionId').val();


            const sectionId =
                $('#questionSectionId').val();


            const question =
                $('#questionText').val()
                    .trim();


            if (!question) {

                APP.error(
                    'Question is required.'
                );

                return;
            }


            const url =
                questionId

                    ? `${baseUrl}/questions/${questionId}/update`

                    : `${baseUrl}/sections/${sectionId}/questions`;


            $.ajax({

                url: url,

                type: 'POST',

                data: {

                    question: question,

                    answer_type:
                        $('#answerType').val(),

                    is_required:
                        $('#isRequired')
                            .is(':checked')
                            ? 1
                            : 0

                },

                success(response) {

                    if (!response.success) {

                        APP.error(
                            response.message ||
                            'Unable to save question.'
                        );

                        return;
                    }


                    $('#questionModal')
                        .modal('hide');


                    APP.success(
                        response.message ||
                        'Question saved successfully.'
                    );


                    loadBuilder();

                },

                error(xhr) {

                    handleValidationError(xhr);

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Delete Question
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.btn-delete-question',
        function (event) {

            event.preventDefault();


            const questionId =
                $(this).data('id');


            if (
                !confirm(
                    'Are you sure you want to delete this question?'
                )
            ) {
                return;
            }


            $.ajax({

                url:
                    `${baseUrl}/questions/${questionId}/delete`,

                type: 'POST',

                success(response) {

                    if (!response.success) {

                        APP.error(
                            response.message ||
                            'Unable to delete question.'
                        );

                        return;
                    }


                    APP.success(
                        response.message ||
                        'Question deleted successfully.'
                    );


                    loadBuilder();

                },

                error(xhr) {

                    APP.error(
                        xhr.responseJSON?.message ||
                        'Unable to delete question.'
                    );

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Sortable
    |--------------------------------------------------------------------------
    */

    function initializeSortable() {

        if (
            typeof Sortable === 'undefined'
        ) {
            return;
        }


        const builder =
            document.getElementById(
                'templateBuilder'
            );


        if (builder) {

            new Sortable(
                builder,
                {

                    animation: 150,

                    handle:
                        '.section-drag-handle',

                    draggable:
                        '.builder-section',

                    onEnd() {

                        const sectionIds =
                            Array
                                .from(
                                    builder.querySelectorAll(
                                        '.builder-section'
                                    )
                                )
                                .map(
                                    element =>
                                        element.dataset.id
                                );


                        reorderSections(
                            sectionIds
                        );

                    }

                }
            );

        }


        document
            .querySelectorAll(
                '.question-list'
            )
            .forEach(
                function (element) {

                    new Sortable(
                        element,
                        {

                            animation: 150,

                            handle:
                                '.question-drag-handle',

                            draggable:
                                '.builder-question',

                            onEnd() {

                                const sectionId =
                                    element.dataset
                                        .sectionId;


                                const questionIds =
                                    Array
                                        .from(
                                            element
                                                .querySelectorAll(
                                                    '.builder-question'
                                                )
                                        )
                                        .map(
                                            question =>
                                                question.dataset.id
                                        );


                                reorderQuestions(
                                    sectionId,
                                    questionIds
                                );

                            }

                        }
                    );

                }
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Reorder Sections
    |--------------------------------------------------------------------------
    */

    function reorderSections(
        sectionIds
    ) {

        $.ajax({

            url:
                `${baseUrl}/${templateId}/sections/reorder`,

            type: 'POST',

            data: {

                section_ids:
                    sectionIds

            },

            success(response) {

                if (!response.success) {

                    APP.error(
                        response.message ||
                        'Unable to reorder sections.'
                    );

                    loadBuilder();

                }

            },

            error(xhr) {

                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to reorder sections.'
                );


                loadBuilder();

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Reorder Questions
    |--------------------------------------------------------------------------
    */

    function reorderQuestions(
        sectionId,
        questionIds
    ) {

        $.ajax({

            url:
                `${baseUrl}/sections/${sectionId}/questions/reorder`,

            type: 'POST',

            data: {

                question_ids:
                    questionIds

            },

            success(response) {

                if (!response.success) {

                    APP.error(
                        response.message ||
                        'Unable to reorder questions.'
                    );

                    loadBuilder();

                }

            },

            error(xhr) {

                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to reorder questions.'
                );


                loadBuilder();

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    function handleValidationError(xhr) {

        if (
            APP.handleUnauthorized(xhr)
        ) {
            return;
        }


        const response =
            xhr.responseJSON || {};


        if (
            response.errors
        ) {

            const message =
                Object
                    .values(
                        response.errors
                    )
                    .join('<br>');


            APP.error(message);

            return;
        }


        APP.error(
            response.message ||
            'Something went wrong.'
        );

    }


    function renderLoading() {

        return `

            <div
                class="text-center
                py-5
                text-muted">

                <div
                    class="spinner-border
                    spinner-border-sm
                    me-2">
                </div>

                Loading template...

            </div>

        `;

    }


    function renderError(message) {

        return `

            <div
                class="alert
                alert-danger">

                ${escapeHtml(message)}

            </div>

        `;

    }


    function renderEmptyState() {

        return `

            <div
                class="text-center
                border
                rounded
                p-5
                text-muted">

                <i
                    class="bi bi-ui-checks-grid
                    fs-1
                    d-block
                    mb-3">
                </i>

                <h6>

                    No sections yet

                </h6>

                <p
                    class="mb-0">

                    Start building your appraisal template
                    by adding the first section.

                </p>

            </div>

        `;

    }


    function escapeHtml(value) {

        return $('<div>')
            .text(
                value ?? ''
            )
            .html();

    }


    function escapeAttribute(value) {

        return escapeHtml(value)
            .replace(/"/g, '&quot;');

    }

});