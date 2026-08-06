document.addEventListener('DOMContentLoaded', () => {

    loadPermissions();

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

        let html = '';

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

                        <button
                            class="btn btn-sm app-btn-primary btn-add-permission"
                            data-module="${group.permissions[0].module_id}">

                            <i class="bi bi-plus-lg"></i>

                            Custom Permission

                        </button>

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

                            <span class="badge bg-secondary permission-badge">

                                SYSTEM

                            </span>

                            <div>

                                <button
                                    class="btn btn-sm btn-light btn-edit"
                                    data-id="${permission.id}">

                                    <i class="bi bi-pencil"></i>

                                </button>

                                <button
                                    class="btn btn-sm btn-light text-danger btn-delete"
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