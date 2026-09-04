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

            <!-- Individual Participant -->

            <div class="card border-0 shadow-sm mb-3">

                <div class="card-body p-3">

                    <div class="d-flex align-items-center justify-content-between mb-3">

                        <div class="d-flex align-items-center gap-2">

                            <div class="bg-primary-subtle text-primary rounded-2 px-2 py-1">
                                <i class="bi bi-person-plus"></i>
                            </div>

                            <div>

                                <div class="fw-semibold">
                                    Add Participant
                                </div>

                                <small class="text-muted">
                                    Individual employee
                                </small>

                            </div>

                        </div>

                    </div>


                    <form id="crudForm">

                        <div class="mb-3">

                            <label
                                for="employee_id"
                                class="form-label small mb-1">

                                Employee
                                <span class="text-danger">*</span>

                            </label>

                            <select
                                id="employee_id"
                                name="employee_id"
                                class="form-select form-select-sm">
                                <option value="">
                                    Select Employee
                                </option>
                            </select>
                        </div>

                        <input type="hidden" id="participant_id" name="participant_id">
                        <div id="participantStatusWrapper" class="mb-3 d-none">
                            <label for="status" class="form-label small mb-1">
                                Status
                            </label>

                            <select id="status" name="status" class="form-select form-select-sm">
                                <option value="active">
                                    Active
                                </option>
                                <option value="excluded">
                                    Excluded
                                </option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1" id="participantSubmitButton">
                                <i class="bi bi-person-plus me-1"></i>
                                Add
                            </button>
                            <button type="button" class="btn btn-light btn-sm d-none" id="participantCancelButton">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bulk Participants -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-success-subtle text-success rounded-2 px-2 py-1">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">
                                    Bulk Add
                                </div>
                                <small class="text-muted">
                                    By department or employee
                                </small>
                            </div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary" id="selectedEmployeeCount">
                            0 selected
                        </span>
                    </div>
                    <!-- Global Actions -->
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                        <small class="fw-semibold text-muted">
                            AVAILABLE EMPLOYEES
                        </small>
                        <div class="form-check form-check-sm mb-0">
                            <input class="form-check-input" type="checkbox" id="selectAllEmployees">
                            <label class="form-check-label small" for="selectAllEmployees">
                                All
                            </label>
                        </div>
                    </div>
                    <!-- Department Wise List -->
                    <div id="bulkEmployeeList" class="participant-employee-list">
                        <div class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm me-2"></div>
                            Loading...
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm w-100 mt-3" id="bulkParticipantSubmit">
                        <i class="bi bi-person-plus me-1"></i>
                        Add Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="col-xl-8">

            <?= view('layouts/components/crud_toolbar', [
                'entity' => 'Participant',
                'entityPlural' => 'Cycle Participants',
            ]) ?>

            <?= view('layouts/components/crud_table') ?>

        </div>

    </div>

</div>

<style>
    .participant-employee-list {
        max-height: 520px;
        overflow-y: auto;
        border: 1px solid var(--bs-border-color);
        border-radius: .5rem;
    }

    .department-group {
        border-bottom: 1px solid var(--bs-border-color);
    }

    .department-group:last-child {
        border-bottom: 0;
    }

    .department-header {
        min-height: 38px;
        padding: .45rem .65rem;
        background: var(--bs-light);

    }

    .department-employees {
        background: #fff;
    }

    .employee-selection-row {
        cursor: pointer;
        min-height: 38px;
        padding: .4rem .75rem .4rem 2.15rem;
        border-bottom: 1px solid rgba(0, 0, 0, .04);
        transition: background-color .12s ease;
    }

    .employee-selection-row:last-child {
        border-bottom: 0;
    }

    .employee-selection-row:hover {
        background: var(--bs-light);
    }

    .participant-selected-row {
        background: var(--bs-primary-bg-subtle);
    }

    .employee-meta {
        font-size: .72rem;
    }

    .department-checkbox,
    .employee-checkbox {
        cursor: pointer;
    }

    @media (max-width: 1199.98px) {
        .participant-employee-list {
            max-height: 400px;
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

                        return `<div><div class="fw-semibold">${CrudUtils.escapeHtml(fullName || '-')}</div><small class="text-muted">${CrudUtils.escapeHtml(row.email || '-')}</small></div>`;
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
                            return '<span class="text-muted small">Not Assigned</span>';
                        }

                        const sourceLabels = {
                            employee: 'Employee Override',
                            designation: 'Designation Rule',
                            department: 'Department Rule'
                        };

                        const source = sourceLabels[row.template_source] || 'Assignment Rule';

                        return `<div><div class="fw-semibold">${CrudUtils.escapeHtml(value)}</div><small class="text-muted">${CrudUtils.escapeHtml(source)}</small></div>`;
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
                    actions.push(`<li><a class="dropdown-item btn-edit" href="#" data-id="${id}"><i class="bi bi-pencil me-2"></i>Manage Status</a></li>`);
                }

                if (crud.can('delete')) {

                    if (actions.length) {
                        actions.push('<li><hr class="dropdown-divider"></li>');
                    }

                    actions.push(`<li><a class="dropdown-item text-danger btn-delete" href="#" data-id="${id}"><i class="bi bi-trash me-2"></i>Remove</a></li>`);
                }

                if (!actions.length) {
                    actions.push('<li><span class="dropdown-item-text text-muted">No available actions</span></li>');
                }

                return actions.join('');
            }
        });

        $('#btnAdd').on('click', function(e) {
            e.preventDefault();
            $('#employee_id').focus();
            $('html, body').animate({
                scrollTop: $('#crudForm').offset().top - 100
            }, 250);
        });

        $('#btnExport').hide();

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

        return statuses[status] || `<span class="badge bg-light text-dark border">${CrudUtils.escapeHtml(status || '-')}</span>`;
    }

    function findParticipantRow(participantId) {
        return (window.participantCrud.data || []).find(row => String(row.id) === String(participantId)) || null;
    }

    function loadAvailableEmployees() {
        const cycleId = <?= (int) $cycleId ?>;

        $('#bulkEmployeeList').html('<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading employees...</div>');

        $.ajax({
            url: '<?= base_url('appraisal/cycles') ?>/' + cycleId + '/participants/available-employees',
            type: 'GET',
            success(response) {
                console.log('Available Employees Response:', response);

                if (!response || !response.success) {
                    $('#bulkEmployeeList').html('<div class="text-center py-4 text-danger"><small>' + CrudUtils.escapeHtml(response?.message || 'Unable to load employees.') + '</small></div>');
                    APP.error(response?.message || 'Unable to load employees.');
                    return;
                }

                renderAvailableEmployees(response.data || []);
            },
            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;

                console.error('Available Employees Error:', xhr);

                $('#bulkEmployeeList').html('<div class="text-center py-4 text-danger"><small>' + CrudUtils.escapeHtml(xhr.responseJSON?.message || 'Unable to load employees.') + '</small></div>');

                APP.error(xhr.responseJSON?.message || 'Unable to load employees.');
            }
        });
    }

    function renderAvailableEmployees(employees) {
        const $employeeSelect = $('#employee_id');
        const $employeeList = $('#bulkEmployeeList');

        $employeeSelect.empty().append('<option value="">Select Employee</option>');

        if (!Array.isArray(employees) || !employees.length) {
            $employeeList.html('<div class="text-center py-4 text-muted"><i class="bi bi-people fs-4 d-block mb-2"></i><small>No employees available.</small></div>');
            return;
        }

        const departments = {};

        employees.forEach(function(employee) {
            const departmentName = employee.department_name && String(employee.department_name).trim() ? String(employee.department_name).trim() : 'Unassigned';

            if (!departments[departmentName]) departments[departmentName] = [];

            departments[departmentName].push(employee);

            const fullName = [employee.first_name, employee.last_name].filter(Boolean).join(' ');

            $employeeSelect.append($('<option>', {
                value: employee.id,
                text: fullName + (employee.employee_code ? ' (' + employee.employee_code + ')' : '')
            }));
        });

        let html = '';

        Object.keys(departments).sort().forEach(function(departmentName, index) {
            const departmentEmployees = departments[departmentName];
            const departmentId = 'departmentEmployees' + index;

            html += `<div class="department-group"><div class="department-header d-flex align-items-center gap-2"><button type="button" class="btn btn-sm p-0 border-0 text-muted department-toggle" data-target="${departmentId}"><i class="bi bi-chevron-down"></i></button><input type="checkbox" class="form-check-input m-0 department-checkbox" data-department="${departmentId}"><div class="flex-grow-1 text-truncate"><span class="fw-semibold small">${CrudUtils.escapeHtml(departmentName)}</span><span class="text-muted small ms-1">(${departmentEmployees.length})</span></div></div><div class="department-employees" id="${departmentId}">`;

            departmentEmployees.forEach(function(employee) {
                const fullName = [employee.first_name, employee.last_name].filter(Boolean).join(' ');
                const employeeCode = employee.employee_code ? CrudUtils.escapeHtml(employee.employee_code) : '';
                const designationName = employee.designation_name ? ' · ' + CrudUtils.escapeHtml(employee.designation_name) : '';

                html += `<label class="employee-selection-row d-flex align-items-center gap-2 mb-0"><input class="form-check-input employee-checkbox m-0" type="checkbox" value="${employee.id}" data-department="${departmentId}"><div class="flex-grow-1 overflow-hidden"><div class="small fw-medium text-truncate">${CrudUtils.escapeHtml(fullName || '-')}</div><div class="employee-meta text-muted text-truncate">${employeeCode}${designationName}</div></div></label>`;
            });

            html += `</div></div>`;
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
        $('#participantSubmitButton').html('<i class="bi bi-person-plus me-1"></i> Add Participant');
        $('#participantCancelButton').addClass('d-none');
    }

    $(document).on('change', '#selectAllEmployees',
        function() {
            const checked = $(this).is(':checked');
            $('.employee-checkbox').prop('checked', checked).trigger('change');
            updateDepartmentCheckboxes();
            updateSelectedEmployeeCount();
        }
    );

    $(document).on('change', '.department-checkbox',
        function() {
            const departmentId = $(this).data('department');
            const checked = $(this).is(':checked');
            $(`#${departmentId}`).find('.employee-checkbox').prop('checked', checked);
            $(`#${departmentId}`).find('.employee-selection-row').toggleClass('participant-selected-row', checked);
            updateGlobalCheckbox();
            updateSelectedEmployeeCount();
        }
    );

    $(document).on('change', '.employee-checkbox',
        function() {
            $(this).closest('.employee-selection-row').toggleClass('participant-selected-row', $(this).is(':checked'));
            updateDepartmentCheckboxes();
            updateGlobalCheckbox();
            updateSelectedEmployeeCount();
        }
    );

    $(document).on('click', '.department-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const targetId = $(this).data('target');
        const $employees = $('#' + targetId);
        const $icon = $(this).find('i');

        $employees.stop(true, true).slideToggle(150);

        $icon.toggleClass('bi-chevron-down bi-chevron-up');
    });

    function updateDepartmentCheckboxes() {
        $('.department-group')
            .each(
                function() {
                    const $group = $(this);
                    const $employees = $group.find('.employee-checkbox');
                    const total = $employees.length;
                    const selected = $employees.filter(':checked').length;
                    const $checkbox = $group.find('.department-checkbox');
                    $checkbox.prop('checked', total > 0 && total === selected);
                    $checkbox.prop('indeterminate', selected > 0 && selected < total);
                }
            );
    }

    function updateGlobalCheckbox() {
        const total = $('.employee-checkbox').length;
        const selected = $('.employee-checkbox:checked').length;
        $('#selectAllEmployees').prop('checked', total > 0 && total === selected).prop('indeterminate', selected > 0 && selected < total);
    }

    function updateSelectedEmployeeCount() {
        const selected = $('.employee-checkbox:checked').length;
        $('#selectedEmployeeCount').text(selected + ' selected');
    }

    $(document).on('change', '.employee-checkbox', function() {

        const total = $('.employee-checkbox').length;
        const selected = $('.employee-checkbox:checked').length;

        $('#selectAllEmployees').prop('checked', total > 0 && total === selected);

        $(this).closest('.employee-selection-row').toggleClass('participant-selected-row', $(this).is(':checked'));

        updateSelectedEmployeeCount();
    });

    function updateSelectedEmployeeCount() {
        $('#selectedEmployeeCount').text($('.employee-checkbox:checked').length + ' selected');
    }

    function addParticipantsBulk() {

        const employeeIds = $('.employee-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

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