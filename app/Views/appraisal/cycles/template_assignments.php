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
                Assign appraisal templates based on department, designation or specific employees.
            </p>

        </div>

        <a href="<?= base_url('appraisal/cycles/' . $cycleId . '/participants') ?>" class="btn btn-outline-secondary">

            <i class="bi bi-people me-1"></i>

            Participants

        </a>

    </div>


    <div class="row g-4">


        <!-- Assignment Form -->

        <div class="col-xl-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">


                    <div class="d-flex align-items-start gap-3 mb-4">

                        <div class="bg-primary-subtle text-primary rounded-3 p-2">

                            <i class="bi bi-diagram-3 fs-5"></i>

                        </div>

                        <div>

                            <h5 class="mb-1" id="assignmentFormTitle">
                                Add Template Assignment
                            </h5>

                            <div class="small text-muted">
                                Configure which employees should receive an appraisal template.
                            </div>

                        </div>

                    </div>


                    <form id="assignmentForm">


                        <input
                            type="hidden"
                            id="assignment_id"
                            name="assignment_id">


                        <div class="mb-3">

                            <label
                                for="assignment_type"
                                class="form-label">
                                Assignment Type
                                <span class="text-danger">*</span>
                            </label>


                            <select
                                id="assignment_type"
                                name="assignment_type"
                                class="form-select">

                                <option value="">
                                    Select Assignment Type
                                </option>

                                <option value="department">
                                    Department
                                </option>

                                <option value="designation">
                                    Designation
                                </option>

                                <option value="employee">
                                    Specific Employee
                                </option>

                            </select>

                        </div>


                        <div
                            class="mb-3 d-none"
                            id="targetWrapper">

                            <label
                                for="assignment_target"
                                class="form-label"
                                id="targetLabel">
                                Target
                                <span class="text-danger">*</span>
                            </label>


                            <select
                                id="assignment_target"
                                class="form-select">

                                <option value="">
                                    Select Target
                                </option>

                            </select>

                        </div>


                        <div class="mb-4">

                            <label
                                for="template_id"
                                class="form-label">
                                Appraisal Template
                                <span class="text-danger">*</span>
                            </label>


                            <select
                                id="template_id"
                                name="template_id"
                                class="form-select">

                                <option value="">
                                    Select Template
                                </option>

                            </select>

                        </div>


                        <div class="d-flex gap-2">


                            <button
                                type="submit"
                                class="btn btn-primary flex-grow-1"
                                id="assignmentSubmitButton">

                                <i class="bi bi-plus-lg me-1"></i>

                                Add Assignment

                            </button>


                            <button
                                type="button"
                                class="btn btn-light d-none"
                                id="assignmentCancelButton">
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

                            <h6 class="mb-2">
                                Assignment Priority
                            </h6>

                            <div class="small text-muted">

                                <div class="mb-1">
                                    <strong>Employee</strong>
                                    overrides everything.
                                </div>

                                <div class="mb-1">
                                    <strong>Designation</strong>
                                    overrides department.
                                </div>

                                <div>
                                    <strong>Department</strong>
                                    is the default assignment.
                                </div>

                            </div>

                        </div>

                    </div>


                </div>

            </div>

        </div>


        <!-- Assignment Table -->

        <div class="col-xl-8">

            <div class="card border-0 shadow-sm">


                <div class="card-header bg-white border-bottom py-3 px-4">

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                        <div>

                            <h5 class="mb-1">
                                Template Assignments
                            </h5>

                            <div class="small text-muted">
                                Manage template assignment rules for this cycle.
                            </div>

                        </div>


                        <div id="crudToolbar"></div>


                    </div>

                </div>


                <div class="card-body p-0">

                    <?= view('layouts/components/crud_table') ?>

                </div>


            </div>

        </div>


    </div>

</div>

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
    $(function() {

        const cycleId =
            <?= (int) $cycleId ?>;

        const baseUrl =
            '<?= base_url('appraisal/cycles') ?>/' +
            cycleId +
            '/template-assignments';


        let assignmentOptions = {
            departments: [],
            designations: [],
            employees: [],
            templates: []
        };


        window.assignmentCrud =
            new Crud({

                endpoint: baseUrl,

                table: '#crudTable',

                form: '#assignmentForm',

                entity: 'Template Assignment',

                entityPlural: 'Template Assignments',

                permissionResource: 'appraisal-cycle',

                columns: [

                    {
                        key: 'assignment_type',

                        label: 'Assignment Type',

                        render: function(value) {

                            return renderAssignmentType(
                                value
                            );

                        }

                    },


                    {
                        key: 'target_name',

                        label: 'Target',

                        render: function(value, row) {

                            return renderAssignmentTarget(
                                value,
                                row
                            );

                        }

                    },


                    {
                        key: 'template_name',

                        label: 'Appraisal Template',

                        render: function(value) {

                            return `
                                <div class="fw-semibold">
                                    ${CrudUtils.escapeHtml(value || '-')}
                                </div>
                            `;

                        }

                    },


                    {
                        key: 'priority',

                        label: 'Priority',

                        render: function(value) {

                            return `
                                <span class="badge bg-light text-dark border">
                                    ${CrudUtils.escapeHtml(value || '-')}
                                </span>
                            `;

                        }

                    }

                ],


                actionRenderer: function(row, id, crud) {

                    const actions = [];


                    if (
                        crud.can('edit')
                    ) {

                        actions.push(`
                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item btn-edit-assignment"
                                    data-id="${id}"
                                >

                                    <i class="bi bi-pencil me-2"></i>

                                    Change Template

                                </a>

                            </li>
                        `);

                    }


                    if (
                        crud.can('delete')
                    ) {

                        if (
                            actions.length
                        ) {

                            actions.push(`
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                            `);

                        }


                        actions.push(`
                            <li>

                                <a
                                    href="#"
                                    class="dropdown-item text-danger btn-delete-assignment"
                                    data-id="${id}"
                                >

                                    <i class="bi bi-trash me-2"></i>

                                    Remove

                                </a>

                            </li>
                        `);

                    }


                    if (
                        !actions.length
                    ) {

                        actions.push(`
                            <li>

                                <span class="dropdown-item-text text-muted">
                                    No available actions
                                </span>

                            </li>
                        `);

                    }


                    return actions.join('');

                }

            });


        loadAssignmentOptions();


        $('#assignment_type').on(
            'change',
            function() {

                populateTargets(
                    $(this).val()
                );

            }
        );


        $('#assignmentForm').on(
            'submit',
            function(e) {

                e.preventDefault();

                const assignmentId =
                    $('#assignment_id').val();


                if (
                    assignmentId
                ) {

                    updateAssignment(
                        assignmentId
                    );

                    return;

                }


                createAssignment();

            }
        );


        $('#assignmentCancelButton').on(
            'click',
            function() {

                resetAssignmentForm();

            }
        );


        $(document).on(
            'click',
            '.btn-edit-assignment',
            function(e) {

                e.preventDefault();

                const assignmentId =
                    $(this).data('id');


                const row =
                    findAssignmentRow(
                        assignmentId
                    );


                if (
                    !row
                ) {

                    APP.error(
                        'Unable to load assignment details.'
                    );

                    return;

                }


                openAssignmentEdit(
                    row
                );

            }
        );


        $(document).on(
            'click',
            '.btn-delete-assignment',
            function(e) {

                e.preventDefault();

                deleteAssignment(
                    $(this).data('id')
                );

            }
        );

    });



    function loadAssignmentOptions() {

        const cycleId =
            <?= (int) $cycleId ?>;


        $.ajax({

            url: '<?= base_url('appraisal/cycles') ?>/' +
                cycleId +
                '/template-assignments/options',

            type: 'GET',


            success(response) {

                if (
                    !response.success
                ) {

                    APP.error(
                        response.message ||
                        'Unable to load assignment options.'
                    );

                    return;

                }


                assignmentOptions =
                    response.data ||
                    {
                        departments: [],
                        designations: [],
                        employees: [],
                        templates: []
                    };


                populateTemplates();

            },


            error(xhr) {

                if (
                    APP.handleUnauthorized(xhr)
                ) {
                    return;
                }


                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to load assignment options.'
                );

            }

        });

    }



    function populateTemplates() {

        const $select =
            $('#template_id');


        $select
            .empty()
            .append(
                '<option value="">Select Template</option>'
            );


        (
            assignmentOptions.templates ||
            []
        ).forEach(
            function(template) {

                $select.append(
                    $('<option>', {
                        value: template.id,
                        text: template.name
                    })
                );

            }
        );

    }



    function populateTargets(type) {

        const $wrapper =
            $('#targetWrapper');

        const $select =
            $('#assignment_target');

        const $label =
            $('#targetLabel');


        $select
            .empty()
            .append(
                '<option value="">Select Target</option>'
            );


        if (
            !type
        ) {

            $wrapper.addClass(
                'd-none'
            );

            return;

        }


        let items = [];

        let label =
            'Target';


        switch (
            type
        ) {

            case 'department':

                items =
                    assignmentOptions.departments ||
                    [];

                label =
                    'Department';

                break;


            case 'designation':

                items =
                    assignmentOptions.designations ||
                    [];

                label =
                    'Designation';

                break;


            case 'employee':

                items =
                    assignmentOptions.employees ||
                    [];

                label =
                    'Employee';

                break;

        }


        $label.html(
            label +
            ' <span class="text-danger">*</span>'
        );


        items.forEach(
            function(item) {

                let text =
                    item.name;


                if (
                    type === 'employee'
                ) {

                    const fullName = [
                            item.first_name,
                            item.last_name
                        ]
                        .filter(Boolean)
                        .join(' ');


                    text =
                        fullName +
                        (
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

            }
        );


        $wrapper.removeClass(
            'd-none'
        );

    }



    function createAssignment() {

        const assignmentType =
            $('#assignment_type').val();

        const targetId =
            $('#assignment_target').val();

        const templateId =
            $('#template_id').val();


        if (
            !assignmentType
        ) {

            APP.error(
                'Please select an assignment type.'
            );

            return;

        }


        if (
            !targetId
        ) {

            APP.error(
                'Please select an assignment target.'
            );

            return;

        }


        if (
            !templateId
        ) {

            APP.error(
                'Please select an appraisal template.'
            );

            return;

        }


        const data = {

            assignment_type: assignmentType,

            template_id: templateId

        };


        if (
            assignmentType === 'department'
        ) {

            data.department_id =
                targetId;

        }


        if (
            assignmentType === 'designation'
        ) {

            data.designation_id =
                targetId;

        }


        if (
            assignmentType === 'employee'
        ) {

            data.employee_id =
                targetId;

        }


        const $button =
            $('#assignmentSubmitButton');


        $button.prop(
            'disabled',
            true
        );


        const cycleId =
            <?= (int) $cycleId ?>;


        $.ajax({

            url: '<?= base_url('appraisal/cycles') ?>/' +
                cycleId +
                '/template-assignments',

            type: 'POST',

            data: data,


            success(response) {

                if (
                    !response.success
                ) {

                    APP.error(
                        response.message ||
                        'Unable to create template assignment.'
                    );

                    return;

                }


                APP.success(
                    response.message ||
                    'Template assignment created successfully.'
                );


                resetAssignmentForm();

                refreshAssignmentData();

            },


            error(xhr) {

                if (
                    APP.handleUnauthorized(xhr)
                ) {
                    return;
                }


                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to create template assignment.'
                );

            },


            complete() {

                $button.prop(
                    'disabled',
                    false
                );

            }

        });

    }



    function findAssignmentRow(
        assignmentId
    ) {

        let assignment =
            null;


        $('#crudTable tbody tr').each(
            function() {

                const rowData =
                    $(this).data('row');


                if (
                    rowData &&
                    String(
                        rowData.id
                    ) ===
                    String(
                        assignmentId
                    )
                ) {

                    assignment =
                        rowData;

                    return false;

                }

            }
        );


        return assignment;

    }



    function openAssignmentEdit(
        assignment
    ) {

        $('#assignment_id').val(
            assignment.id
        );


        $('#assignment_type')
            .val(
                assignment.assignment_type
            )
            .prop(
                'disabled',
                true
            );


        populateTargets(
            assignment.assignment_type
        );


        let targetId =
            null;


        switch (
            assignment.assignment_type
        ) {

            case 'department':

                targetId =
                    assignment.department_id;

                break;


            case 'designation':

                targetId =
                    assignment.designation_id;

                break;


            case 'employee':

                targetId =
                    assignment.employee_id;

                break;

        }


        $('#assignment_target')
            .val(
                targetId
            )
            .prop(
                'disabled',
                true
            );


        $('#template_id').val(
            assignment.template_id
        );


        $('#targetWrapper')
            .removeClass(
                'd-none'
            );


        $('#assignmentFormTitle').text(
            'Update Template Assignment'
        );


        $('#assignmentSubmitButton').html(
            '<i class="bi bi-check2 me-1"></i> Update Template'
        );


        $('#assignmentCancelButton')
            .removeClass(
                'd-none'
            );


        $('html, body').animate({

                scrollTop: $('#assignmentForm')
                    .offset()
                    .top -
                    100

            },
            250
        );

    }



    function updateAssignment(
        assignmentId
    ) {

        const templateId =
            $('#template_id').val();


        if (
            !templateId
        ) {

            APP.error(
                'Please select an appraisal template.'
            );

            return;

        }


        const $button =
            $('#assignmentSubmitButton');


        $button.prop(
            'disabled',
            true
        );


        $.ajax({

            url: '<?= base_url('appraisal/cycles/template-assignments') ?>/' +
                assignmentId +
                '/update',

            type: 'POST',

            data: {

                template_id: templateId

            },


            success(response) {

                if (
                    !response.success
                ) {

                    APP.error(
                        response.message ||
                        'Unable to update template assignment.'
                    );

                    return;

                }


                APP.success(
                    response.message ||
                    'Template assignment updated successfully.'
                );


                resetAssignmentForm();

                refreshAssignmentData();

            },


            error(xhr) {

                if (
                    APP.handleUnauthorized(xhr)
                ) {
                    return;
                }


                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to update template assignment.'
                );

            },


            complete() {

                $button.prop(
                    'disabled',
                    false
                );

            }

        });

    }



    function deleteAssignment(
        assignmentId
    ) {

        if (
            !confirm(
                'Are you sure you want to remove this template assignment?'
            )
        ) {
            return;
        }


        $.ajax({

            url: '<?= base_url('appraisal/cycles/template-assignments') ?>/' +
                assignmentId +
                '/delete',

            type: 'POST',


            success(response) {

                if (
                    !response.success
                ) {

                    APP.error(
                        response.message ||
                        'Unable to remove template assignment.'
                    );

                    return;

                }


                APP.success(
                    response.message ||
                    'Template assignment removed successfully.'
                );


                refreshAssignmentData();

            },


            error(xhr) {

                if (
                    APP.handleUnauthorized(xhr)
                ) {
                    return;
                }


                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to remove template assignment.'
                );

            }

        });

    }



    function resetAssignmentForm() {

        $('#assignmentForm')[0].reset();


        $('#assignment_id').val(
            ''
        );


        $('#assignment_type')
            .prop(
                'disabled',
                false
            );


        $('#assignment_target')
            .prop(
                'disabled',
                false
            );


        $('#targetWrapper')
            .addClass(
                'd-none'
            );


        $('#assignmentFormTitle').text(
            'Add Template Assignment'
        );


        $('#assignmentSubmitButton').html(
            '<i class="bi bi-plus-lg me-1"></i> Add Assignment'
        );


        $('#assignmentCancelButton')
            .addClass(
                'd-none'
            );

    }



    function refreshAssignmentData() {

        if (
            window.assignmentCrud &&
            typeof window.assignmentCrud.load === 'function'
        ) {

            window.assignmentCrud.load();

        } else {

            location.reload();

        }

    }



    function renderAssignmentType(
        type
    ) {

        const types = {

            department: '<span class="badge bg-primary-subtle text-primary">Department</span>',

            designation: '<span class="badge bg-info-subtle text-info">Designation</span>',

            employee: '<span class="badge bg-success-subtle text-success">Employee</span>'

        };


        return types[type] ||
            `
            <span class="badge bg-light text-dark border">
                ${CrudUtils.escapeHtml(type || '-')}
            </span>
        `;

    }



    function renderAssignmentTarget(
        value,
        row
    ) {

        if (
            value
        ) {

            return `
            <div class="fw-semibold">
                ${CrudUtils.escapeHtml(value)}
            </div>
        `;

        }


        return `
        <span class="text-muted">
            -
        </span>
    `;

    }
</script>


<?= $this->endSection() ?>