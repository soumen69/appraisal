<?= $this->extend('layouts/master') ?>


<?= $this->section('content') ?>


<?= view('layouts/components/crud_toolbar', [
    'entity'       => 'Appraisal Template',
    'entityPlural' => 'Appraisal Templates',
]) ?>


<?= view('layouts/components/crud_table') ?>


<?= view('appraisal/templates/form') ?>


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

        const templateCrud = new Crud({

            endpoint: '<?= base_url('templates') ?>',

            table: '#crudTable',

            modal: '#crudModal',

            form: '#crudForm',

            entity: 'Appraisal Template',

            entityPlural: 'Appraisal Templates',

            permissionResource: 'appraisal-template',


            columns: [

                {
                    key: 'template_name',

                    label: 'Template',

                    render: function(
                        value,
                        row
                    ) {

                        return `
                            <div class="d-flex flex-column">

                                <div class="fw-semibold">

                                    ${CrudUtils.escapeHtml(
                                        value || '-'
                                    )}

                                </div>

                                ${
                                    row.is_default == 1

                                    ? `
                                        <small
                                            class="text-primary fw-semibold">

                                            Default Template

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
                    key: 'section_count',

                    label: 'Sections',

                    render: function(value) {

                        return `
                            <span
                                class="badge
                                bg-primary-subtle
                                text-primary">

                                ${value || 0}

                            </span>
                        `;
                    }
                },


                {
                    key: 'question_count',

                    label: 'Questions',

                    render: function(value) {

                        return `
                            <span
                                class="badge
                                bg-info-subtle
                                text-info">

                                ${value || 0}

                            </span>
                        `;
                    }
                },


                {
                    key: 'status',

                    label: 'Status',

                    render: function(value) {

                        const statuses = {

                            active: `
                <span
                    class="badge bg-success-subtle text-success">
                    Active
                </span>
            `,

                            inactive: `
                <span
                    class="badge bg-secondary-subtle text-secondary">
                    Inactive
                </span>
            `
                        };

                        const status =
                            String(value || '')
                            .toLowerCase();

                        return statuses[status] || `
            <span
                class="badge bg-light text-dark border">
                -
            </span>
        `;
                    }
                }

            ],


            actionRenderer: function(row, id, crud) {

                const actions = [];

                if (crud.can('edit')) {

                    actions.push(`
                        <li>

                            <a
                                class="dropdown-item
                                btn-configure-template"
                                href="<?= base_url(
                                            'templates'
                                        ) ?>/${id}/builder">

                                <i
                                    class="bi bi-sliders
                                    me-2">
                                </i>

                                Configure

                            </a>

                        </li>
                    `);
                }


                if (crud.can('view')) {

                    actions.push(`
                        <li>

                            <a
                                class="dropdown-item btn-view"
                                href="#"
                                data-id="${id}">

                                <i
                                    class="bi bi-eye me-2">
                                </i>

                                View

                            </a>

                        </li>
                    `);
                }


                if (crud.can('edit')) {

                    actions.push(`
                        <li>

                            <a
                                class="dropdown-item btn-edit"
                                href="#"
                                data-id="${id}">

                                <i
                                    class="bi bi-pencil me-2">
                                </i>

                                Edit

                            </a>

                        </li>
                    `);
                }


                if (crud.can('delete')) {

                    if (actions.length) {

                        actions.push(`
                            <li>

                                <hr
                                    class="dropdown-divider">

                            </li>
                        `);
                    }


                    actions.push(`
                        <li>

                            <a
                                class="dropdown-item
                                text-danger
                                btn-delete"
                                href="#"
                                data-id="${id}">

                                <i
                                    class="bi bi-trash me-2">
                                </i>

                                Delete

                            </a>

                        </li>
                    `);
                }


                if (!actions.length) {

                    actions.push(`
                        <li>

                            <span
                                class="dropdown-item-text
                                text-muted">

                                No available actions

                            </span>

                        </li>
                    `);
                }


                return actions.join('');
            },


            drawerRenderer: function(data, crud) {

                return `

                    <div
                        class="drawer-row mb-4">

                        <div
                            class="small
                            text-muted
                            text-uppercase
                            fw-semibold
                            mb-1">

                            Appraisal Template

                        </div>


                        <div
                            class="fw-semibold fs-5">

                            ${CrudUtils.escapeHtml(
                                data.template_name || '-'
                            )}

                        </div>

                    </div>


                    <div
                        class="drawer-row mb-3">

                        <div
                            class="small
                            text-muted
                            text-uppercase
                            fw-semibold
                            mb-1">

                            Organization

                        </div>


                        <div>

                            ${CrudUtils.escapeHtml(
                                data.organization_name || '-'
                            )}

                        </div>

                    </div>


                    <div
                        class="drawer-row mb-3">

                        <div
                            class="small
                            text-muted
                            text-uppercase
                            fw-semibold
                            mb-1">

                            Template Type

                        </div>


                        <div>

                            ${
                                data.is_default == 1

                                ? `
                                    <span
                                        class="badge
                                        bg-primary-subtle
                                        text-primary">

                                        Default Template

                                    </span>
                                `

                                : `
                                    <span
                                        class="badge
                                        bg-light
                                        text-dark
                                        border">

                                        Standard Template

                                    </span>
                                `
                            }

                        </div>

                    </div>


                    <div
                        class="drawer-row mb-3">

                        <div
                            class="small
                            text-muted
                            text-uppercase
                            fw-semibold
                            mb-1">

                            Sections

                        </div>


                        <div>

                            ${data.section_count || 0}

                        </div>

                    </div>


                    <div
                        class="drawer-row mb-3">

                        <div
                            class="small
                            text-muted
                            text-uppercase
                            fw-semibold
                            mb-1">

                            Questions

                        </div>


                        <div>

                            ${data.question_count || 0}

                        </div>

                    </div>


                    <div
                        class="drawer-row mb-3">

                        <div
                            class="small
                            text-muted
                            text-uppercase
                            fw-semibold
                            mb-1">

                            Status

                        </div>


                        <div>

                            ${renderTemplateStatus(
                                data.status
                            )}

                        </div>

                    </div>


                    <div
                        class="drawer-row mb-3">

                        <div
                            class="small
                            text-muted
                            text-uppercase
                            fw-semibold
                            mb-1">

                            Description

                        </div>


                        <div>

                            ${
                                data.description

                                ? CrudUtils.escapeHtml(
                                    data.description
                                )

                                : '-'
                            }

                        </div>

                    </div>

                `;
            }

        });


        loadOrganizations();

    });


    function renderTemplateStatus(status) {

        const statuses = {

            active: `
            <span
                class="badge
                bg-success-subtle
                text-success">

                Active

            </span>
        `,

            inactive: `
            <span
                class="badge
                bg-secondary-subtle
                text-secondary">

                Inactive

            </span>
        `
        };

        const normalizedStatus =
            String(status || '')
            .toLowerCase();

        return statuses[normalizedStatus] || `
        <span
            class="badge
            bg-light
            text-dark
            border">

            -

        </span>
    `;
    }

    function loadOrganizations() {

        $.ajax({

            url: '<?= base_url(
                        'organizations/options'
                    ) ?>',

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
                    .append(`
                        <option value="">

                            Select Organization

                        </option>
                    `);


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

    $(document).on(
        'crud:editLoaded',
        function(event, data) {

            const isDefault =
                Number(data.is_default) === 1;

            $('#is_default')
                .prop(
                    'checked',
                    isDefault
                )
                .trigger('change');

        }
    );
</script>


<?= $this->endSection() ?>