<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= base_url('appraisal/cycles/' . $cycleId . '/participants') ?>" class="text-muted text-decoration-none">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <span class="text-muted small">Appraisal Cycle</span>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Template Assignments</span>
            </div>
            <h3 class="mb-1">Template Assignments</h3>
            <p class="text-muted mb-0">
                Configure appraisal templates for self and matrix reviews based on department, designation or specific employees.</p>
        </div>

        <a href="<?= base_url('appraisal/cycles/' . $cycleId . '/participants') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-people me-1"></i>
            Participants
        </a>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm" id="assignmentFormCard">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="bg-primary-subtle text-primary rounded-3 p-2">
                            <i class="bi bi-diagram-3 fs-5"></i>
                        </div>
                        <div>
                            <h5 class="mb-1" id="assignmentFormTitle">Add Template Assignment</h5>
                            <div class="small text-muted">Configure who will be reviewed, who performs the review and which template applies.</div>
                        </div>
                    </div>

                    <!-- <form id="assignmentForm">
                        <input type="hidden" id="assignment_id" name="assignment_id">

                        <div class="mb-3">
                            <label for="assignment_type" class="form-label">
                                Assignment Type
                                <span class="text-danger">*</span>
                            </label>

                            <select id="assignment_type" name="assignment_type" class="form-select">
                                <option value="">Select Assignment Type</option>
                                <option value="department">Department</option>
                                <option value="designation">Designation</option>
                                <option value="employee">Specific Employee</option>
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="targetWrapper">
                            <label for="assignment_target" class="form-label" id="targetLabel">
                                Target
                                <span class="text-danger">*</span>
                            </label>

                            <select id="assignment_target" class="form-select">
                                <option value="">Select Target</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="template_id" class="form-label">
                                Appraisal Template
                                <span class="text-danger">*</span>
                            </label>

                            <select id="template_id" name="template_id" class="form-select">
                                <option value="">Select Template</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" id="assignmentSubmitButton">
                                <i class="bi bi-plus-lg me-1"></i>
                                Add Assignment
                            </button>

                            <button type="button" class="btn btn-light d-none" id="assignmentCancelButton">
                                Cancel
                            </button>
                        </div>
                    </form> -->
                    <form id="assignmentForm">
                        <input type="hidden" id="assignment_id" name="assignment_id">

                        <div class="mb-3">
                            <label for="review_type" class="form-label">
                                Review Type
                                <span class="text-danger">*</span>
                            </label>

                            <select id="review_type" name="review_type" class="form-select">
                                <option value="">Select Review Type</option>
                                <option value="self">Self Review</option>
                                <option value="matrix">Matrix Review</option>
                            </select>

                            <div class="form-text">
                                Choose who will perform the appraisal.
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="reviewerRoleWrapper">
                            <label for="reviewer_role_id" class="form-label">
                                Reviewer Role
                                <span class="text-danger">*</span>
                            </label>

                            <select id="reviewer_role_id" name="reviewer_role_id" class="form-select">
                                <option value="">Select Reviewer Role</option>
                            </select>

                            <div class="form-text">
                                Select the role responsible for reviewing employees.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <label for="assignment_type" class="form-label">
                                Applies To
                                <span class="text-danger">*</span>
                            </label>

                            <select id="assignment_type" name="assignment_type" class="form-select">
                                <option value="">Select Assignment Type</option>
                                <option value="department">Department</option>
                                <option value="designation">Designation</option>
                                <option value="employee">Specific Employee</option>
                            </select>

                            <div class="form-text">
                                Choose which employees should receive this appraisal assignment.
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="targetWrapper">
                            <label for="assignment_target" class="form-label" id="targetLabel">
                                Target
                                <span class="text-danger">*</span>
                            </label>

                            <select id="assignment_target" class="form-select">
                                <option value="">Select Target</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="template_id" class="form-label">
                                Appraisal Template
                                <span class="text-danger">*</span>
                            </label>

                            <select id="template_id" name="template_id" class="form-select">
                                <option value="">Select Template</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" id="assignmentSubmitButton">
                                <i class="bi bi-plus-lg me-1"></i>
                                Add Assignment
                            </button>

                            <button type="button" class="btn btn-light d-none" id="assignmentCancelButton">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-flex gap-3">
                        <div class="text-primary">
                            <i class="bi bi-info-circle fs-5"></i>
                        </div>

                        <div>
                            <h6 class="mb-2">Assignment Resolution</h6>

                            <div class="small text-muted">
                                <div class="mb-2">
                                    Templates are resolved separately for each review type.
                                </div>

                                <div class="mb-1">
                                    <strong>Specific Employee</strong>
                                    overrides designation and department assignments.
                                </div>

                                <div class="mb-1">
                                    <strong>Designation</strong>
                                    overrides department assignments.
                                </div>

                                <div>
                                    <strong>Department</strong>
                                    acts as the default assignment.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <?= view('layouts/components/crud_toolbar', [
                'entity' => 'Template Assignment',
                'entityPlural' => 'Template Assignments',
                'showAddButton' => false
            ]) ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <?= view('layouts/components/crud_table') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #assignmentFormCard {
        transition: box-shadow .25s ease, transform .25s ease;
    }

    #assignmentFormCard.assignment-form-highlight {
        box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), .12), 0 .5rem 1.5rem rgba(0, 0, 0, .08) !important;
        transform: translateY(-2px);
    }

    #assignmentFormCard.assignment-form-highlight .form-select {
        border-color: rgba(var(--bs-primary-rgb), .45);
    }

    @media (prefers-reduced-motion: reduce) {
        #assignmentFormCard {
            transition: none;
        }
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.api.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.table.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.pagination.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.search.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.modal.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.form.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.delete.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.drawer.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.view.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.js') . '?v=' . time() ?>"></script>

<script>
    const cycleId = <?= (int) $cycleId ?>;

    $(function() {
        const baseUrl = '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/template-assignments';

        let assignmentOptions = {
            departments: [],
            designations: [],
            employees: [],
            templates: [],
            reviewer_roles: []
        };

        window.assignmentCrud = new Crud({
            endpoint: baseUrl,
            table: '#crudTable',
            entity: 'Template Assignment',
            entityPlural: 'Template Assignments',
            permissionResource: 'appraisal-cycle',

            columns: [{
                    key: 'review_type',
                    label: 'Review Type',
                    render: function(value) {
                        return renderReviewType(value);
                    }
                },
                {
                    key: 'reviewer_role_name',
                    label: 'Reviewer',
                    render: function(value, row) {
                        return renderReviewer(value, row);
                    }
                },
                {
                    key: 'assignment_type',
                    label: 'Applies To',
                    render: function(value, row) {
                        return `
                <div class="mb-1">
                    ${renderAssignmentType(value)}
                </div>
                ${renderAssignmentTarget(row.target_name, row)}
            `;
                    }
                },
                {
                    key: 'template_name',
                    label: 'Appraisal Template',
                    render: function(value) {
                        return `<div class="fw-semibold">${CrudUtils.escapeHtml(value || '-')}</div>`;
                    }
                },
                {
                    key: 'priority',
                    label: 'Priority',
                    render: function(value) {
                        return `<span class="badge bg-light text-dark border">${CrudUtils.escapeHtml(value || '-')}</span>`;
                    }
                }
            ],

            actionRenderer: function(row, id, crud) {
                const actions = [];
                const editable = canModifyAssignment(row);

                if (editable && crud.can('edit')) {
                    actions.push(`
            <li>
                <a href="#" class="dropdown-item btn-edit-assignment" data-id="${id}">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Assignment
                </a>
            </li>
        `);
                }

                if (editable && crud.can('delete')) {
                    if (actions.length) {
                        actions.push(`<li><hr class="dropdown-divider"></li>`);
                    }

                    actions.push(`
            <li>
                <a href="#" class="dropdown-item text-danger btn-delete-assignment" data-id="${id}">
                    <i class="bi bi-trash me-2"></i>
                    Remove
                </a>
            </li>
        `);
                }

                if (!editable) {
                    actions.push(`
            <li>
                <span class="dropdown-item-text text-muted">
                    <i class="bi bi-lock me-2"></i>
                    Locked
                </span>
            </li>`);
                }

                if (!actions.length) {
                    actions.push(`
            <li>
                <span class="dropdown-item-text text-muted">
                    No available actions
                </span>
            </li>`);
                }

                return actions.join('');
            }
        });

        $('#crudSearch').attr('placeholder', 'Search template assignments...');

        $(document)
            .off('click.assignmentCreate', '#btnAdd')
            .on('click.assignmentCreate', '#btnAdd', function(e) {
                e.preventDefault();
                openNewAssignmentForm();
            });

        loadAssignmentOptions();

        $('#assignment_type').on('change', function() {
            populateTargets($(this).val());
        });

        $('#review_type').on('change', function() {
            handleReviewTypeChange($(this).val());
        });

        $('#assignmentForm').on('submit', function(e) {
            e.preventDefault();

            const assignmentId = $('#assignment_id').val();

            if (assignmentId) {
                updateAssignment(assignmentId);
                return;
            }

            createAssignment();
        });

        $('#assignmentCancelButton').on('click', function() {
            resetAssignmentForm();
        });

        $(document).on('click', '.btn-edit-assignment', function(e) {
            e.preventDefault();

            const assignmentId = $(this).data('id');
            const row = findAssignmentRow(assignmentId);

            if (!row) {
                APP.error('Unable to load assignment details.');
                return;
            }

            if (!canModifyAssignment(row)) {
                APP.error(getAssignmentLockedMessage());
                return;
            }

            openAssignmentEdit(row);
        });

        $(document).on('click', '.btn-delete-assignment', function(e) {
            e.preventDefault();

            const assignmentId = $(this).data('id');
            const row = findAssignmentRow(assignmentId);

            if (!row) {
                APP.error('Unable to load assignment details.');
                return;
            }

            if (!canModifyAssignment(row)) {
                APP.error(getAssignmentLockedMessage());
                return;
            }

            deleteAssignment(assignmentId);
        });
    });

    function openNewAssignmentForm() {
        resetAssignmentForm();
        scrollToAssignmentForm('#assignment_type');
    }

    function loadAssignmentOptions() {
        const cycleId = <?= (int) $cycleId ?>;

        $.ajax({
            url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/template-assignments/options',
            type: 'GET',

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to load assignment options.');
                    return;
                }

                assignmentOptions = response.data || {
                    departments: [],
                    designations: [],
                    employees: [],
                    templates: [],
                    reviewer_roles: []
                };

                populateTemplates();
                populateReviewerRoles();
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) {
                    return;
                }

                APP.error(xhr.responseJSON?.message || 'Unable to load assignment options.');
            }
        });
    }

    function populateTemplates() {
        const $select = $('#template_id');

        $select
            .empty()
            .append('<option value="">Select Template</option>');

        (assignmentOptions.templates || []).forEach(function(template) {
            $select.append(
                $('<option>', {
                    value: template.id,
                    text: template.name
                })
            );
        });
    }

    function populateReviewerRoles() {
        const $select = $('#reviewer_role_id');

        $select
            .empty()
            .append('<option value="">Select Reviewer Role</option>');

        (assignmentOptions.reviewer_roles || []).forEach(function(role) {
            $select.append(
                $('<option>', {
                    value: role.id,
                    text: role.display_name || role.name
                })
            );
        });
    }

    function handleReviewTypeChange(reviewType) {
        const $wrapper = $('#reviewerRoleWrapper');
        const $select = $('#reviewer_role_id');

        if (reviewType === 'matrix') {
            $wrapper.removeClass('d-none');
            return;
        }

        $select.val('');
        $wrapper.addClass('d-none');
    }

    function populateTargets(type) {
        const $wrapper = $('#targetWrapper');
        const $select = $('#assignment_target');
        const $label = $('#targetLabel');

        $select
            .empty()
            .append('<option value="">Select Target</option>');

        if (!type) {
            $wrapper.addClass('d-none');
            return;
        }

        let items = [];
        let label = 'Target';

        switch (type) {
            case 'department':
                items = assignmentOptions.departments || [];
                label = 'Department';
                break;

            case 'designation':
                items = assignmentOptions.designations || [];
                label = 'Designation';
                break;

            case 'employee':
                items = assignmentOptions.employees || [];
                label = 'Employee';
                break;
        }

        $label.html(label + ' <span class="text-danger">*</span>');

        items.forEach(function(item) {
            let text = item.name;

            if (type === 'employee') {
                const fullName = [
                        item.first_name,
                        item.last_name
                    ]
                    .filter(Boolean)
                    .join(' ');

                text = fullName + (
                    item.employee_code ?
                    ' (' + item.employee_code + ')' :
                    ''
                );
            }

            $select.append(
                $('<option>', {
                    value: item.id,
                    text: text
                })
            );
        });

        $wrapper.removeClass('d-none');
    }

    // function createAssignment() {
    //     const cycleId = <?= (int) $cycleId ?>;
    //     const assignmentType = $('#assignment_type').val();
    //     const targetId = $('#assignment_target').val();
    //     const templateId = $('#template_id').val();

    //     if (!assignmentType) {
    //         APP.error('Please select an assignment type.');
    //         return;
    //     }

    //     if (!targetId) {
    //         APP.error('Please select an assignment target.');
    //         return;
    //     }

    //     if (!templateId) {
    //         APP.error('Please select an appraisal template.');
    //         return;
    //     }

    //     const data = {
    //         assignment_type: assignmentType,
    //         template_id: templateId
    //     };

    //     if (assignmentType === 'department') {
    //         data.department_id = targetId;
    //     }

    //     if (assignmentType === 'designation') {
    //         data.designation_id = targetId;
    //     }

    //     if (assignmentType === 'employee') {
    //         data.employee_id = targetId;
    //     }

    //     const $button = $('#assignmentSubmitButton');

    //     $button.prop('disabled', true);

    //     $.ajax({
    //         url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/template-assignments',
    //         type: 'POST',
    //         data: data,
    //         success(response) {
    //             if (!response.success) {
    //                 APP.error(response.message || 'Unable to create template assignment.');
    //                 return;
    //             }

    //             APP.success(response.message || 'Template assignment created successfully.');
    //             resetAssignmentForm();
    //             refreshAssignmentData();
    //         },
    //         error(xhr) {
    //             if (APP.handleUnauthorized(xhr)) {
    //                 return;
    //             }

    //             APP.error(xhr.responseJSON?.message || 'Unable to create template assignment.');
    //         },
    //         complete() {
    //             $button.prop('disabled', false);
    //         }
    //     });
    // }

    function createAssignment() {
        const reviewType = $('#review_type').val();
        const reviewerRoleId = $('#reviewer_role_id').val();
        const assignmentType = $('#assignment_type').val();
        const targetId = $('#assignment_target').val();
        const templateId = $('#template_id').val();

        if (!reviewType) {
            APP.error('Please select a review type.');
            return;
        }

        if (reviewType === 'matrix' && !reviewerRoleId) {
            APP.error('Please select a reviewer role.');
            return;
        }

        if (!assignmentType) {
            APP.error('Please select who this assignment applies to.');
            return;
        }

        if (!targetId) {
            APP.error('Please select an assignment target.');
            return;
        }

        if (!templateId) {
            APP.error('Please select an appraisal template.');
            return;
        }

        const data = {
            review_type: reviewType,
            reviewer_role_id: reviewType === 'matrix' ? reviewerRoleId : '',
            assignment_type: assignmentType,
            template_id: templateId
        };

        if (assignmentType === 'department') {
            data.department_id = targetId;
        }

        if (assignmentType === 'designation') {
            data.designation_id = targetId;
        }

        if (assignmentType === 'employee') {
            data.employee_id = targetId;
        }

        const $button = $('#assignmentSubmitButton');

        $button.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/template-assignments',
            type: 'POST',
            data: data,

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to create template assignment.');
                    return;
                }

                APP.success(response.message || 'Template assignment created successfully.');
                resetAssignmentForm();
                refreshAssignmentData();
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) {
                    return;
                }

                APP.error(xhr.responseJSON?.message || 'Unable to create template assignment.');
            },

            complete() {
                $button.prop('disabled', false);
            }
        });
    }

    function findAssignmentRow(assignmentId) {
        return (window.assignmentCrud.data || []).find(
            row => String(row.id) === String(assignmentId)
        ) || null;
    }

    function openAssignmentEdit(assignment) {
        if (!canModifyAssignment(assignment)) {
            APP.error(getAssignmentLockedMessage());
            return;
        }

        $('#assignment_id').val(assignment.id);

        $('#review_type')
            .val(assignment.review_type)
            .prop('disabled', false);

        handleReviewTypeChange(assignment.review_type);

        $('#reviewer_role_id')
            .val(assignment.reviewer_role_id || '')
            .prop('disabled', false);

        $('#assignment_type')
            .val(assignment.assignment_type)
            .prop('disabled', false);

        populateTargets(assignment.assignment_type);

        let targetId = null;

        switch (assignment.assignment_type) {
            case 'department':
                targetId = assignment.department_id;
                break;

            case 'designation':
                targetId = assignment.designation_id;
                break;

            case 'employee':
                targetId = assignment.employee_id;
                break;
        }

        $('#assignment_target')
            .val(targetId)
            .prop('disabled', false);

        $('#template_id')
            .val(assignment.template_id)
            .prop('disabled', false);

        $('#targetWrapper').removeClass('d-none');

        $('#assignmentFormTitle').text('Edit Template Assignment');

        $('#assignmentSubmitButton').html(
            '<i class="bi bi-check2 me-1"></i> Update Assignment'
        );

        $('#assignmentCancelButton').removeClass('d-none');

        scrollToAssignmentForm('#review_type');
    }

    function updateAssignment(assignmentId) {
        const assignment = findAssignmentRow(assignmentId);

        if (!assignment) {
            APP.error('Unable to load assignment details.');
            return;
        }

        if (!canModifyAssignment(assignment)) {
            APP.error(getAssignmentLockedMessage());
            resetAssignmentForm();
            return;
        }

        const reviewType = $('#review_type').val();
        const reviewerRoleId = $('#reviewer_role_id').val();
        const assignmentType = $('#assignment_type').val();
        const targetId = $('#assignment_target').val();
        const templateId = $('#template_id').val();

        if (!reviewType) {
            APP.error('Please select a review type.');
            return;
        }

        if (reviewType === 'matrix' && !reviewerRoleId) {
            APP.error('Please select a reviewer role.');
            return;
        }

        if (!assignmentType || !targetId) {
            APP.error('Invalid assignment target.');
            return;
        }

        if (!templateId) {
            APP.error('Please select an appraisal template.');
            return;
        }

        const data = {
            review_type: reviewType,
            reviewer_role_id: reviewType === 'matrix' ? reviewerRoleId : '',
            assignment_type: assignmentType,
            template_id: templateId
        };

        if (assignmentType === 'department') {
            data.department_id = targetId;
        }

        if (assignmentType === 'designation') {
            data.designation_id = targetId;
        }

        if (assignmentType === 'employee') {
            data.employee_id = targetId;
        }

        const $button = $('#assignmentSubmitButton');

        $button.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/template-assignments/' + assignmentId + '/update',
            type: 'POST',
            data: data,

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to update template assignment.');
                    return;
                }

                APP.success(response.message || 'Template assignment updated successfully.');
                resetAssignmentForm();
                refreshAssignmentData();
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) {
                    return;
                }

                APP.error(xhr.responseJSON?.message || 'Unable to update template assignment.');
            },

            complete() {
                $button.prop('disabled', false);
            }
        });
    }

    function deleteAssignment(assignmentId) {
        Swal.fire({
            title: 'Remove Template Assignment?',
            text: 'This assignment will be removed only if all related appraisals are still pending.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Remove',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545'
        }).then(result => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/template-assignments/' + assignmentId + '/delete',
                type: 'POST',
                data: {
                    [APP.csrfName]: APP.csrfHash
                },

                success(response) {
                    if (!response.success) {
                        APP.error(response.message || 'Unable to remove template assignment.');
                        return;
                    }

                    APP.success(response.message || 'Template assignment removed successfully.');
                    refreshAssignmentData();
                },

                error(xhr) {
                    if (APP.handleUnauthorized(xhr)) {
                        return;
                    }

                    APP.error(xhr.responseJSON?.message || 'Unable to remove template assignment.');
                }
            });
        });
    }

    function resetAssignmentForm() {
        $('#assignmentForm')[0].reset();

        $('#assignment_id').val('');

        $('#review_type').prop('disabled', false);
        $('#reviewer_role_id').prop('disabled', false);
        $('#assignment_type').prop('disabled', false);
        $('#assignment_target').prop('disabled', false);

        $('#reviewerRoleWrapper').addClass('d-none');
        $('#targetWrapper').addClass('d-none');

        $('#assignmentFormTitle').text('Add Template Assignment');

        $('#assignmentSubmitButton').html(
            '<i class="bi bi-plus-lg me-1"></i> Add Assignment'
        );

        $('#assignmentCancelButton').addClass('d-none');
    }

    function scrollToAssignmentForm(focusSelector) {
        const $formCard = $('#assignmentFormCard');

        $('html, body').animate({
            scrollTop: $formCard.offset().top - 100
        }, 250, function() {
            if (focusSelector) {
                $(focusSelector).trigger('focus');
            }

            $formCard.addClass('assignment-form-highlight');

            setTimeout(function() {
                $formCard.removeClass('assignment-form-highlight');
            }, 1800);
        });
    }

    function refreshAssignmentData() {
        if (
            window.assignmentCrud &&
            typeof window.assignmentCrud.reload === 'function'
        ) {
            window.assignmentCrud.reload();
            return;
        }

        location.reload();
    }

    function renderReviewType(type) {
        const types = {
            self: '<span class="badge bg-success-subtle text-success">Self Review</span>',
            matrix: '<span class="badge bg-primary-subtle text-primary">Matrix Review</span>'
        };

        return types[type] ||
            `<span class="badge bg-light text-dark border">${CrudUtils.escapeHtml(type || '-')}</span>`;
    }

    function renderReviewer(value, row) {
        if (row.review_type === 'self') {
            return `
            <div class="fw-semibold">Employee</div>
            <div class="small text-muted">Self</div>
        `;
        }

        const reviewerName = value || row.reviewer_role_display_name || row.reviewer_role_name;

        if (reviewerName) {
            return `<div class="fw-semibold">${CrudUtils.escapeHtml(reviewerName)}</div>`;
        }

        return '<span class="text-muted">-</span>';
    }

    function renderAssignmentType(type) {
        const types = {
            department: '<span class="badge bg-primary-subtle text-primary">Department</span>',
            designation: '<span class="badge bg-info-subtle text-info">Designation</span>',
            employee: '<span class="badge bg-success-subtle text-success">Employee</span>'
        };

        return types[type] ||
            `<span class="badge bg-light text-dark border">${CrudUtils.escapeHtml(type || '-')}</span>`;
    }

    function renderAssignmentTarget(value, row) {
        let targetName = value;

        if (!targetName) {
            switch (row.assignment_type) {
                case 'department':
                    targetName = row.department_name;
                    break;

                case 'designation':
                    targetName = row.designation_name;
                    break;

                case 'employee':
                    targetName = row.employee_name;

                    if (row.employee_code) {
                        targetName += ' (' + row.employee_code + ')';
                    }

                    break;
            }
        }

        if (targetName) {
            return `<div class="fw-semibold">${CrudUtils.escapeHtml(targetName)}</div>`;
        }

        return `<span class="text-muted">-</span>`;
    }

    function canModifyAssignment(row) {
        return row && (row.can_edit === true || row.can_edit === 1 || row.can_edit === '1');
    }

    function getAssignmentLockedMessage() {
        return 'This assignment cannot be modified because one or more related appraisals are already in progress or completed.';
    }
</script>

<?= $this->endSection() ?>