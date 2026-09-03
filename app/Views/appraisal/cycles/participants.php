<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= base_url('appraisal/cycles') ?>" class="text-muted text-decoration-none">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <span class="text-muted small">Appraisal Cycle</span>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Participants</span>
            </div>

            <h3 class="mb-1">Cycle Participants</h3>
            <p class="text-muted mb-0">Manage employees participating in this appraisal cycle.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('appraisal/cycles/' . $cycleId . '/template-assignments') ?>" class="btn btn-outline-primary">
                <i class="bi bi-diagram-3 me-1"></i>
                Template Assignments
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="bg-primary-subtle text-primary rounded-3 p-2">
                            <i class="bi bi-person-plus fs-5"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Add Participant</h5>
                            <div class="small text-muted">Add an individual employee to this appraisal cycle.</div>
                        </div>
                    </div>

                    <form id="crudForm">
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                            <select id="employee_id" name="employee_id" class="form-select">
                                <option value="">Select Employee</option>
                            </select>
                        </div>

                        <input type="hidden" id="participant_id" name="participant_id">

                        <div id="participantStatusWrapper" class="mb-3 d-none">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="excluded">Excluded</option>
                            </select>
                            <div class="form-text">Excluded employees remain in the cycle but are not considered active participants.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" id="participantSubmitButton">
                                <i class="bi bi-person-plus me-1"></i>
                                Add Participant
                            </button>

                            <button type="button" class="btn btn-light d-none" id="participantCancelButton">
                                Cancel
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="bg-success-subtle text-success rounded-3 p-2">
                            <i class="bi bi-people fs-5"></i>
                        </div>

                        <div>
                            <h5 class="mb-1">Bulk Add Participants</h5>
                            <div class="small text-muted">Select multiple employees and add them to the cycle at once.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">

                            <label class="form-label mb-0">Available Employees</label>

                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="selectAllEmployees">
                                <label class="form-check-label small fw-semibold" for="selectAllEmployees">
                                    Select All
                                </label>
                            </div>

                        </div>

                        <div class="small text-muted mb-2">
                            <span id="selectedEmployeeCount">0 selected</span>
                        </div>

                        <div id="bulkEmployeeList" class="participant-employee-list">

                            <div class="text-center py-4 text-muted">
                                <div class="spinner-border spinner-border-sm me-2"></div>
                                Loading employees...
                            </div>

                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary w-100" id="bulkParticipantSubmit">
                        <i class="bi bi-people me-1"></i>
                        Add Selected Participants
                    </button>

                </div>
            </div>

        </div>

        <div class="col-xl-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-bottom py-3 px-4">

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                        <div>
                            <h5 class="mb-1">Participants</h5>
                            <div class="small text-muted">Employees currently included in this appraisal cycle.</div>
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
<style>
    .participant-employee-list {
        max-height: 420px;
        overflow-y: auto;
        border: 1px solid var(--bs-border-color);
        border-radius: .5rem;
    }

    .employee-selection-row {
        cursor: pointer;
        transition: background-color .15s ease;
    }

    .employee-selection-row:last-child {
        border-bottom: 0 !important;
    }

    .employee-selection-row:hover {
        background-color: var(--bs-light);
    }

    .participant-selected-row {
        background-color: var(--bs-primary-bg-subtle);
    }

    @media (max-width: 1199.98px) {
        .participant-employee-list {
            max-height: 300px;
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
    $(function() {

        const cycleId = <?= (int) $cycleId ?>;
        const participantBaseUrl = '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/participants';

        window.participantCrud = new Crud({
            endpoint: participantBaseUrl,
            table: '#crudTable',
            form: '#crudForm',
            entity: 'Participant',
            entityPlural: 'Cycle Participants',
            permissionResource: 'appraisal-cycle',
            columns: [{
                    key: 'first_name',
                    label: 'Employee',
                    render: function(value, row) {

                        const fullName = [row.first_name, row.last_name].filter(Boolean).join(' ');

                        return `
                        <div>
                            <div class="fw-semibold">${CrudUtils.escapeHtml(fullName || '-')}</div>
                            <small class="text-muted">${CrudUtils.escapeHtml(row.email || '-')}</small>
                        </div>
                    `;
                    }
                },
                {
                    key: 'employee_code',
                    label: 'Code',
                    render: function(value) {
                        return CrudUtils.escapeHtml(value || '-');
                    }
                },
                {
                    key: 'department_name',
                    label: 'Department',
                    render: function(value) {
                        return CrudUtils.escapeHtml(value || '-');
                    }
                },
                {
                    key: 'designation_name',
                    label: 'Designation',
                    render: function(value) {
                        return CrudUtils.escapeHtml(value || '-');
                    }
                },
                {
                    key: 'resolved_template_name',
                    label: 'Assigned Template',
                    render: function(value, row) {
                        if (!value) {
                            return `
                                <span class="text-muted small">
                                    Not Assigned
                                </span>
                            `;
                        }

                        const sourceLabels = {
                            employee: 'Employee Override',
                            designation: 'Designation Rule',
                            department: 'Department Rule'
                        };

                        const source =
                            sourceLabels[row.template_source] ||
                            'Assignment Rule';
                        return `
                        <div>
                            <div class="fw-semibold">
                                ${CrudUtils.escapeHtml(value)}
                            </div>
                            <small class="text-muted">
                                ${CrudUtils.escapeHtml(source)}
                            </small>

                        </div>
                    `;
                    }
                },
                {
                    key: 'status',
                    label: 'Status',
                    render: function(value) {
                        return renderParticipantStatus(value);
                    }
                }
            ],

            actionRenderer: function(row, id, crud) {
                const actions = [];

                if (crud.can('edit')) {
                    actions.push(`
                    <li>
                        <a class="dropdown-item btn-edit" href="#" data-id="${id}">
                            <i class="bi bi-pencil me-2"></i>
                            Manage Status
                        </a>
                    </li>
                `);
                }

                if (crud.can('delete')) {
                    if (actions.length) {
                        actions.push(`
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    `);
                    }

                    actions.push(`
                    <li>
                        <a class="dropdown-item text-danger btn-delete" href="#" data-id="${id}">
                            <i class="bi bi-trash me-2"></i>
                            Remove
                        </a>
                    </li>
                `);
                }

                if (!actions.length) {
                    actions.push(`
                    <li>
                        <span class="dropdown-item-text text-muted">
                            No available actions
                        </span>
                    </li>
                `);
                }
                return actions.join('');
            },

            drawerRenderer: function(data) {
                const fullName = [data.first_name, data.last_name].filter(Boolean).join(' ');

                return `
                <div class="drawer-row mb-4">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Employee</div>
                    <div class="fw-semibold fs-5">${CrudUtils.escapeHtml(fullName || '-')}</div>
                    <div class="text-muted small mt-1">${CrudUtils.escapeHtml(data.email || '-')}</div>
                </div>
                <div class="drawer-row mb-3">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Employee Code</div>
                    <div>${CrudUtils.escapeHtml(data.employee_code || '-')}</div>
                </div>
                <div class="drawer-row mb-3">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Department</div>
                    <div>${CrudUtils.escapeHtml(data.department_name || '-')}</div>
                </div>
                <div class="drawer-row mb-3">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Designation</div>
                    <div>${CrudUtils.escapeHtml(data.designation_name || '-')}</div>
                </div>
                <div class="drawer-row mb-3">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Status</div>
                    <div>${renderParticipantStatus(data.status)}</div>
                </div>
            `;
            }
        });
        loadAvailableEmployees();

        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault();
            const participantId = $(this).data('id');
            const row = findParticipantRow(participantId);
            if (!row) {
                APP.error('Unable to load participant details.');
                return;
            }
            openParticipantEdit(row);
        });

        $('#crudForm').on('submit', function(e) {
            e.preventDefault();
            const participantId = $('#participant_id').val();
            if (participantId) {
                updateParticipant(participantId);
                return;
            }
            addParticipant();
        });

        $('#participantCancelButton').on('click', function() {
            resetParticipantForm();
        });

        $('#bulkParticipantSubmit').on('click', function() {
            addParticipantsBulk();
        });
    });

    function renderParticipantStatus(status) {
        const statuses = {
            active: '<span class="badge bg-success-subtle text-success">Active</span>',
            excluded: '<span class="badge bg-secondary-subtle text-secondary">Excluded</span>'
        };

        return statuses[status] || `
        <span class="badge bg-light text-dark border">
            ${CrudUtils.escapeHtml(status || '-')}
        </span>
    `;
    }

    function findParticipantRow(participantId) {
        let participant = null;
        $('#crudTable tbody tr').each(function() {
            const rowData = $(this).data('row');
            if (rowData && String(rowData.id) === String(participantId)) {
                participant = rowData;
                return false;
            }
        });
        return participant;
    }

    function loadAvailableEmployees() {
        const cycleId = <?= (int) $cycleId ?>;

        $.ajax({
            url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/participants/available-employees',
            type: 'GET',
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to load employees.');
                    return;
                }
                renderAvailableEmployees(response.data || []);
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) {
                    return;
                }
                APP.error(xhr.responseJSON?.message || 'Unable to load employees.');
            }
        });
    }

    function renderAvailableEmployees(employees) {
        const $employeeSelect = $('#employee_id');
        const $employeeList = $('#bulkEmployeeList');
        $employeeSelect.empty().append('<option value="">Select Employee</option>');

        if (!employees.length) {
            $employeeList.html(`
            <div class="text-center py-4 text-muted">
                <i class="bi bi-people fs-3 d-block mb-2"></i>
                No employees available to add.
            </div>
        `);
            return;
        }

        let html = '';

        employees.forEach(function(employee) {
            const fullName = [employee.first_name, employee.last_name].filter(Boolean).join(' ');
            $employeeSelect.append(
                $('<option>', {
                    value: employee.id,
                    text: fullName + (employee.employee_code ? ' (' + employee.employee_code + ')' : '')
                })
            );

            html += `
            <label class="d-flex align-items-center gap-3 p-3 border-bottom mb-0 employee-selection-row">
                <input class="form-check-input employee-checkbox m-0" type="checkbox" value="${employee.id}">

                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-semibold text-truncate">${CrudUtils.escapeHtml(fullName || '-')}</div>

                    <div class="small text-muted text-truncate">
                        ${CrudUtils.escapeHtml(employee.employee_code || '-')}
                        ${employee.department_name ? ' · ' + CrudUtils.escapeHtml(employee.department_name) : ''}
                        ${employee.designation_name ? ' · ' + CrudUtils.escapeHtml(employee.designation_name) : ''}
                    </div>
                </div>
            </label>
        `;
        });

        $employeeList.html(html);
    }

    function addParticipant() {
        const employeeId = $('#employee_id').val();
        if (!employeeId) {
            APP.error('Please select an employee.');
            return;
        }

        const cycleId = <?= (int) $cycleId ?>;
        const $button = $('#participantSubmitButton');
        $button.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/participants',
            type: 'POST',
            data: {
                employee_id: employeeId
            },

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to add participant.');
                    return;
                }
                APP.success(response.message || 'Participant added successfully.');
                resetParticipantForm();
                refreshParticipantData();
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) {
                    return;
                }
                APP.error(xhr.responseJSON?.message || 'Unable to add participant.');
            },

            complete() {
                $button.prop('disabled', false);
            }
        });
    }

    function openParticipantEdit(participant) {
        $('#participant_id').val(participant.id);
        $('#employee_id').val(participant.employee_id).prop('disabled', true);
        $('#status').val(participant.status || 'active');
        $('#participantStatusWrapper').removeClass('d-none');
        $('#participantSubmitButton').html('<i class="bi bi-check2 me-1"></i> Update Status');
        $('#participantCancelButton').removeClass('d-none');
        $('html, body').animate({
            scrollTop: $('#crudForm').offset().top - 100
        }, 250);
    }

    function updateParticipant(participantId) {
        const $button = $('#participantSubmitButton');
        $button.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/participants/' + participantId + '/update',
            type: 'POST',
            data: {
                status: $('#status').val()
            },

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to update participant.');
                    return;
                }
                APP.success(response.message || 'Participant updated successfully.');
                resetParticipantForm();
                refreshParticipantData();
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) {
                    return;
                }
                APP.error(xhr.responseJSON?.message || 'Unable to update participant.');
            },

            complete() {
                $button.prop('disabled', false);
            }
        });
    }

    function resetParticipantForm() {
        $('#crudForm')[0].reset();
        $('#participant_id').val('');
        $('#employee_id').prop('disabled', false);
        $('#participantStatusWrapper').addClass('d-none');
        $('#participantSubmitButton')
            .html('<i class="bi bi-person-plus me-1"></i> Add Participant');
        $('#participantCancelButton').addClass('d-none');
    }

    $(document).on('change', '#selectAllEmployees', function() {
        $('.employee-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedEmployeeCount();
    });

    $(document).on('change', '.employee-checkbox', function() {
        const total = $('.employee-checkbox').length;
        const selected = $('.employee-checkbox:checked').length;
        $('#selectAllEmployees').prop('checked', total > 0 && total === selected);
        updateSelectedEmployeeCount();
    });

    $(document).on('change', '.employee-checkbox', function() {

        $(this)
            .closest('.employee-selection-row')
            .toggleClass('participant-selected-row', $(this).is(':checked'));
    });

    function updateSelectedEmployeeCount() {

        const count = $('.employee-checkbox:checked').length;

        $('#selectedEmployeeCount').text(count + ' selected');
    }

    function addParticipantsBulk() {

        const employeeIds = $('.employee-checkbox:checked')
            .map(function() {
                return $(this).val();
            })
            .get();

        if (!employeeIds.length) {
            APP.error('Please select at least one employee.');
            return;
        }

        const cycleId = <?= (int) $cycleId ?>;
        const $button = $('#bulkParticipantSubmit');

        $button.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/participants/bulk',
            type: 'POST',
            data: {
                employee_ids: employeeIds
            },

            success(response) {

                if (!response.success) {
                    APP.error(response.message || 'Unable to add participants.');
                    return;
                }

                APP.success(response.message || 'Participants added successfully.');

                $('#selectAllEmployees').prop('checked', false);
                $('#selectedEmployeeCount').text('0 selected');

                refreshParticipantData();
            },

            error(xhr) {

                if (APP.handleUnauthorized(xhr)) {
                    return;
                }

                APP.error(xhr.responseJSON?.message || 'Unable to add participants.');
            },

            complete() {
                $button.prop('disabled', false);
            }
        });
    }

    function refreshParticipantData() {

        loadAvailableEmployees();

        if (window.participantCrud && typeof window.participantCrud.load === 'function') {
            window.participantCrud.load();
            return;
        }

        location.reload();
    }
</script>

<?= $this->endSection() ?>