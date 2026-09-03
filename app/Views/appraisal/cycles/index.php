<?= $this->extend('layouts/master') ?>


<?= $this->section('content') ?>


<?= view('layouts/components/crud_toolbar', [
    'entity'       => 'Appraisal Cycle',
    'entityPlural' => 'Appraisal Cycles',
]) ?>


<?= view('layouts/components/crud_table') ?>


<?= view('appraisal/cycles/form') ?>


<?= view('layouts/components/crud_drawer') ?>


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


        const cycleCrud = new Crud({

            endpoint: '<?= base_url('cycles') ?>',
            table: '#crudTable',
            modal: '#crudModal',
            form: '#crudForm',
            entity: 'Appraisal Cycle',
            entityPlural: 'Appraisal Cycles',
            permissionResource: 'appraisal-cycle',

            columns: [{
                    key: 'cycle_name',
                    label: 'Appraisal Cycle',
                    render: function(value, row) {
                        return `
                        <div class="d-flex flex-column">
                            <div class="fw-semibold">
                                ${CrudUtils.escapeHtml(value || '-')}
                            </div>
                            ${
                                row.cycle_code
                                    ? `
                                        <small class="text-muted">
                                            ${CrudUtils.escapeHtml(
                                                row.cycle_code
                                            )}
                                        </small>
                                    `
                                    : ''
                            }
                        </div>
                    `;
                    }
                },


                {
                    key: 'organization_name',
                    label: 'Organization',
                    render: function(value) {
                        return CrudUtils.escapeHtml(
                            value || '-'
                        );
                    }
                },


                {
                    key: 'start_date',
                    label: 'Period',
                    render: function(value, row) {
                        return `
                        <div class="d-flex flex-column">
                            <span>
                                ${formatCycleDate(value)}
                            </span>
                            <small class="text-muted">
                                to ${formatCycleDate(row.end_date)}
                            </small>
                        </div>
                    `;
                    }
                },


                {
                    key: 'status',
                    label: 'Status',
                    render: function(value) {
                        const statuses = {
                            draft: `
                            <span class="badge bg-secondary-subtle text-secondary">
                                Draft
                            </span>
                        `,
                            active: `
                            <span class="badge bg-success-subtle text-success">
                                Active
                            </span>
                        `,
                            completed: `
                            <span class="badge bg-primary-subtle text-primary">
                                Completed
                            </span>
                        `,
                            closed: `
                            <span class="badge bg-danger-subtle text-danger">
                                Closed
                            </span>
                        `
                        };

                        return statuses[value] || `
                        <span class="badge bg-light text-dark border">
                            ${CrudUtils.escapeHtml(value || '-')}
                        </span>
                    `;
                    }
                }

            ],


            actionRenderer: function(row, id, crud) {

                const actions = [];

                if (crud.can('view')) {
                    actions.push(`
            <li>
                <a class="dropdown-item btn-view" href="#" data-id="${id}">
                    <i class="bi bi-eye me-2"></i>
                    View
                </a>
            </li>
        `);
                }

                actions.push(`
        <li>
            <a class="dropdown-item btn-participants" href="<?= base_url('appraisal/cycles') ?>/${id}/participants">
                <i class="bi bi-people me-2"></i>
                Manage Participants
            </a>
        </li>
    `);

                if (crud.can('edit')) {
                    actions.push(`
            <li>
                <a class="dropdown-item btn-edit" href="#" data-id="${id}">
                    <i class="bi bi-pencil me-2"></i>
                    Edit
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
                    Delete
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


            drawerRenderer: function(
                data,
                crud
            ) {

                const description =
                    data.description ?
                    CrudUtils.escapeHtml(
                        data.description
                    ) :
                    '-';


                return `

                <div class="drawer-row mb-4">

                    <div
                        class="small text-muted text-uppercase fw-semibold mb-1">

                        Appraisal Cycle

                    </div>

                    <div class="fw-semibold fs-5">

                        ${CrudUtils.escapeHtml(
                            data.cycle_name || '-'
                        )}

                    </div>

                </div>


                <div class="drawer-row mb-3">

                    <div
                        class="small text-muted text-uppercase fw-semibold mb-1">

                        Cycle Code

                    </div>

                    <div>

                        ${CrudUtils.escapeHtml(
                            data.cycle_code || '-'
                        )}

                    </div>

                </div>


                <div class="drawer-row mb-3">

                    <div
                        class="small text-muted text-uppercase fw-semibold mb-1">

                        Organization

                    </div>

                    <div>

                        ${CrudUtils.escapeHtml(
                            data.organization_name || '-'
                        )}

                    </div>

                </div>


                <div class="drawer-row mb-3">

                    <div
                        class="small text-muted text-uppercase fw-semibold mb-1">

                        Start Date

                    </div>

                    <div>

                        ${formatCycleDate(
                            data.start_date
                        )}

                    </div>

                </div>


                <div class="drawer-row mb-3">

                    <div
                        class="small text-muted text-uppercase fw-semibold mb-1">

                        End Date

                    </div>

                    <div>

                        ${formatCycleDate(
                            data.end_date
                        )}

                    </div>

                </div>


                <div class="drawer-row mb-3">

                    <div
                        class="small text-muted text-uppercase fw-semibold mb-1">

                        Status

                    </div>

                    <div>

                        ${renderCycleStatus(
                            data.status
                        )}

                    </div>

                </div>


                <div class="drawer-row mb-3">

                    <div
                        class="small text-muted text-uppercase fw-semibold mb-1">

                        Created By

                    </div>

                    <div>

                        ${CrudUtils.escapeHtml(
                            data.created_by_name || '-'
                        )}

                    </div>

                </div>


                <div class="drawer-row mb-3">

                    <div
                        class="small text-muted text-uppercase fw-semibold mb-1">

                        Description

                    </div>

                    <div>

                        ${description}

                    </div>

                </div>

            `;
            }

        });

        loadOrganizations();
    });


    function formatCycleDate(value) {

        if (!value) {

            return '-';
        }


        const date =
            new Date(value);


        if (
            Number.isNaN(
                date.getTime()
            )
        ) {

            return CrudUtils.escapeHtml(
                value
            );
        }


        return date.toLocaleDateString(
            'en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }
        );
    }


    function renderCycleStatus(status) {

        const statuses = {

            draft: `
            <span class="badge bg-secondary-subtle text-secondary">
                Draft
            </span>
        `,

            active: `
            <span class="badge bg-success-subtle text-success">
                Active
            </span>
        `,

            completed: `
            <span class="badge bg-primary-subtle text-primary">
                Completed
            </span>
        `,

            closed: `
            <span class="badge bg-danger-subtle text-danger">
                Closed
            </span>
        `
        };


        return statuses[status] || '-';
    }

    function loadOrganizations() {

        $.ajax({

            url: '<?= base_url('organizations/options') ?>',

            type: 'GET',

            success(response) {

                if (!response.success) {

                    APP.error(
                        response.message ||
                        'Unable to load organizations.'
                    );

                    return;
                }

                const $organization =
                    $('#organization_id');

                const selectedValue =
                    $organization.val();

                $organization
                    .empty()
                    .append(
                        '<option value="">Select Organization</option>'
                    );


                const organizations =
                    response.data || [];


                organizations.forEach(
                    function(organization) {

                        $organization.append(
                            $('<option>', {
                                value: organization.id,
                                text: organization.name
                            })
                        );

                    }
                );


                if (selectedValue) {

                    $organization.val(
                        selectedValue
                    );

                }

                $organization.trigger('change');
            },

            error(xhr) {

                if (
                    APP.handleUnauthorized(xhr)
                ) {
                    return;
                }

                APP.error(
                    xhr.responseJSON?.message ||
                    'Unable to load organizations.'
                );

            }

        });

    }
</script>


<?= $this->endSection() ?>