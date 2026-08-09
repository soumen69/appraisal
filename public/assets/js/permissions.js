document.addEventListener('DOMContentLoaded', () => {
    const permissionCrud = {

        endpoint: `${APP.baseUrl.replace(/\/$/, '')}/permissions`,

        form: document.querySelector('#crudForm'),

        modal: document.querySelector('#crudModal'),

        editId: null,

        reload() {

            loadPermissions();

        }

    };
    loadPermissions();
    loadModules();
    bindEvents();

    function bindEvents() {

        CrudForm.bind(permissionCrud);

        CrudDelete.bind(permissionCrud);

        $('#btnAddPermission').on('click', function () {

            $('#crudModalTitle').text('Custom Permission');

            $('#crudForm')[0].reset();

            permissionCrud.editId = null;

            new bootstrap.Modal('#crudModal').show();

        });

        $(document).on('click', '.btn-edit', function () {

            CrudForm.load(permissionCrud, $(this).data('id'));

            $('#crudModalTitle').text('Edit Permission');

        });

    }

    function loadModules() {

        $.get(`${APP.baseUrl}/menus/options`, function (response) {

            let html = '<option value="">Select Module</option>';

            response.data.modules.forEach(module => {

                html += `<option value="${module.id}">${module.name}</option>`;

            });

            $('#module_id').html(html);

        });

    }

    function loadPermissions() {

        $.get(`${APP.baseUrl}/permissions/list`, function (response) {

            if (!response.success) {

                APP.error(response.message);

                return;

            }

            render(response.data);

        });

    }

    function render(groups) {

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-4">

                <h5 class="mb-0 fw-semibold">

                    Permission Registry

                </h5>

                <button
                    class="btn app-btn-primary"
                    id="btnAddPermission">

                    <i class="bi bi-plus-lg"></i>

                    Custom Permission

                </button>

            </div>
        `;

        groups.forEach(group => {

            html += `
                <div class="permission-group">

                    <div class="permission-group-header">

                        <div>

                            <div class="permission-group-title">

                                ${group.module}

                            </div>

                            <div class="permission-count">

                                ${group.permissions.length} Permissions

                            </div>

                        </div>

                    </div>

                    <div class="permission-list">
            `;

            group.permissions.forEach(permission => {

                html += `
                    <div class="permission-card">

                        <div class="permission-name">

                            ${permission.name}

                        </div>

                        <div class="permission-slug">

                            ${permission.slug}

                        </div>

                        <div class="permission-footer">

                            <span class="badge bg-secondary">

                                SYSTEM

                            </span>

                            <div class="d-flex gap-1">

                                <button
                                    class="btn btn-light btn-sm btn-edit"
                                    data-id="${permission.id}">

                                    <i class="bi bi-pencil"></i>

                                </button>

                                <button
                                    class="btn btn-light btn-sm text-danger btn-delete"
                                    data-id="${permission.id}">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </div>

                        </div>

                    </div>
                `;

            });

            html += `
                    </div>

                </div>
            `;

        });

        $('#permissionRegistry').html(html);

    }

});