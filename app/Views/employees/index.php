<?= $this->extend('layouts/master') ?>


<?= $this->section('content') ?>


<?= view('layouts/components/crud_toolbar', [
    'entity'       => 'Employee',
    'entityPlural' => 'Employees',
]) ?>


<?= view('layouts/components/crud_table') ?>

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


<script src="<?= base_url('assets/js/employees/employee-drawer.js') ?>"></script>


<script src="<?= base_url('assets/js/crud/crud.js') . '?v=' . time() ?>"></script>


<script>
    $(function() {
        const employeeCrud = new Crud({

            endpoint: '<?= base_url('employees') ?>',

            viewEndpoint: '<?= base_url('employees/details') ?>',

            table: '#crudTable',

            entity: 'Employee',

            entityPlural: 'Employees',

            actionRenderer: function(row, id, crud) {

                const isActive =
                    row.status === 'active';

                const actions = [];

                if (crud.can('view')) {

                    actions.push(`
                <li>
                    <a
                        class="dropdown-item btn-view"
                        href="#"
                        data-id="${id}">

                        <i class="bi bi-eye me-2"></i>
                        View

                    </a>
                </li>
            `);
                }

                if (crud.can('edit')) {

                    actions.push(`
            <li>
                <a
                    class="dropdown-item employee-edit"
                    href="${APP.baseUrl}/employees/edit/${id}">

                    <i class="bi bi-pencil me-2"></i>
                    Edit

                </a>
            </li>
        `);
                }

                if (crud.can('edit')) {

                    actions.push(`
            <li>
                <a
                    class="dropdown-item employee-toggle-status"
                    href="#"
                    data-id="${id}">

                    <i class="bi bi-power me-2"></i>

                    ${
                        isActive
                            ? 'Deactivate'
                            : 'Activate'
                    }

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
                <a
                    class="dropdown-item text-danger employee-delete"
                    href="#"
                    data-id="${id}">

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

            columns: [{
                    key: 'employee',
                    label: 'Employee',

                    render: function(value, row) {

                        const fullName =
                            row.full_name || [
                                row.first_name,
                                row.last_name
                            ]
                            .filter(Boolean)
                            .join(' ') ||
                            'Employee';

                        const initials =
                            getEmployeeInitials(
                                fullName
                            );

                        const avatar =
                            row.profile_photo ?
                            `
                        <img
                            src="${escapeEmployeeAttribute(
                                resolveEmployeePhoto(
                                    row.profile_photo
                                )
                            )}"
                            alt="${escapeEmployeeAttribute(
                                fullName
                            )}">
                      ` :
                            `
                        <span class="employee-avatar-fallback">
                            ${escapeEmployeeHtml(
                                initials
                            )}
                        </span>
                      `;

                        return `
                <div class="employee-identity">

                    <div class="employee-avatar">
                        ${avatar}
                    </div>

                    <div class="employee-identity-content">

                        <div class="employee-name">
                            ${escapeEmployeeHtml(
                                fullName
                            )}
                        </div>

                        <div class="employee-code">
                            ${escapeEmployeeHtml(
                                row.employee_code ||
                                'No employee code'
                            )}
                        </div>

                    </div>

                </div>
            `;
                    }
                },

                {
                    key: 'organization_name',
                    label: 'Organization',

                    render: function(value, row) {

                        const organization =
                            value || '-';

                        const department =
                            row.department_name ||
                            'No department';

                        const branch =
                            row.branch_name || '';

                        return `
                <div class="employee-org-cell">

                    <div class="employee-primary-text">
                        ${escapeEmployeeHtml(
                            organization
                        )}
                    </div>

                    <div class="employee-secondary-text">

                        <i class="bi bi-diagram-3"></i>

                        ${escapeEmployeeHtml(
                            department
                        )}

                    </div>

                    ${
                        branch
                            ? `
                                <div class="employee-tertiary-text">

                                    <i class="bi bi-geo-alt"></i>

                                    ${escapeEmployeeHtml(
                                        branch
                                    )}

                                </div>
                              `
                            : ''
                    }

                </div>
            `;
                    }
                },

                {
                    key: 'designation_name',
                    label: 'Designation',

                    render: function(value) {

                        return `
                <div class="employee-primary-text">
                    ${escapeEmployeeHtml(
                        value || 'Not assigned'
                    )}
                </div>`;
                    }
                },


                {
                    key: 'role_name',
                    label: 'Role',

                    render: function(value) {

                        if (!value) {

                            return `
                    <span class="employee-role-badge employee-role-unassigned">
                        <i class="bi bi-shield-x"></i>
                        Unassigned
                    </span>`;
                        }
                        return `
                            <span class="employee-role-badge">
                                <i class="bi bi-shield-check"></i>
                                ${escapeEmployeeHtml(value)}
                            </span>`;
                    }
                },


                /*
                 * Joining Date
                 */
                {
                    key: 'joining_date',
                    label: 'Joining Date',

                    render: function(value) {

                        return formatEmployeeDate(
                            value
                        );
                    }
                },


                /*
                 * Status
                 */
                {
                    key: 'status',
                    label: 'Status'
                }

            ]

        });


        /*
         * ---------------------------------------------------------
         * Add Employee
         *
         * Reusable toolbar provides #btnAdd.
         * Employees use a dedicated create page instead
         * of the reusable CRUD modal.
         * ---------------------------------------------------------
         */

        $('#btnAdd')
            .off('click.employee')
            .on('click.employee', function() {

                window.location.href =
                    '<?= base_url('employees/create') ?>';

            });


        /*
         * ---------------------------------------------------------
         * Employee View
         *
         * CrudView handles the request and drawer opening.
         * We only provide the Employee-specific renderer.
         * ---------------------------------------------------------
         */

        employeeCrud.drawerRenderer =
            function(data) {

                return EmployeeDrawer.render(
                    data
                );

            };


        /*
         * ---------------------------------------------------------
         * Employee Status Toggle
         * ---------------------------------------------------------
         */

        $(document)
            .off(
                'click.employeeStatus',
                '.employee-toggle-status'
            )
            .on(
                'click.employeeStatus',
                '.employee-toggle-status',
                function(e) {

                    e.preventDefault();

                    const id =
                        $(this).data('id');

                    toggleEmployeeStatus(
                        employeeCrud,
                        id
                    );

                }
            );

    });


    /*
     * =============================================================
     * Employee Helpers
     * =============================================================
     */

    function getEmployeeInitials(name) {

        const parts =
            String(name)
            .trim()
            .split(/\s+/)
            .filter(Boolean);


        if (!parts.length) {
            return 'E';
        }


        if (parts.length === 1) {

            return parts[0]
                .substring(0, 2)
                .toUpperCase();

        }


        return (
            parts[0].charAt(0) +
            parts[parts.length - 1].charAt(0)
        ).toUpperCase();
    }


    function formatEmployeeDate(value) {

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

            return escapeEmployeeHtml(
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


    function resolveEmployeePhoto(path) {

        if (!path) {
            return '';
        }


        if (
            path.startsWith('http://') ||
            path.startsWith('https://') ||
            path.startsWith('/')
        ) {

            return path;

        }


        return `${APP.baseUrl}${path}`;
    }


    function escapeEmployeeHtml(value) {

        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    function escapeEmployeeAttribute(value) {
        return escapeEmployeeHtml(
            value
        );
    }


    /*
     * =============================================================
     * Employee Status
     * =============================================================
     */

    function toggleEmployeeStatus(crud, id) {
        const employee = crud.data.find(item => parseInt(item.id, 10) === parseInt(id, 10));

        if (!employee) {
            return;
        }

        const activate = employee.status !== 'active';
        Swal.fire({
            title: activate ? 'Activate employee?' : 'Deactivate employee?',
            text: activate ? 'This employee will be allowed to access the system.' : 'This employee will no longer be able to log in.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: activate ?
                'Activate' : 'Deactivate',
            cancelButtonText: 'Cancel',
            confirmButtonColor: activate ?
                '#198754' : '#dc3545'
        }).then(function(result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: `<?= base_url('employees/toggle-status') ?>/${id}`,
                type: 'POST',
                data: {
                    [APP.csrfName]: APP.csrfHash
                },

                success: function(response) {
                    if (!response.success) {
                        APP.error(
                            response.message ||
                            'Unable to update employee status.'
                        );
                        return;
                    }
                    APP.success(response.message);
                    crud.reload();
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        APP.error('You are not authorized.');
                        return;
                    }
                    APP.error('Unable to update employee status.');
                }
            });
        });
    }
</script>


<?= $this->endSection() ?>