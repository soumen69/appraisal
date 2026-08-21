// document.addEventListener('DOMContentLoaded', () => {
//     const permissionCrud = {
//         endpoint: `${APP.baseUrl.replace(/\/$/, '')}/permissions`,
//         form: document.querySelector('#crudForm'),
//         modal: document.querySelector('#crudModal'),
//         editId: null,
//         reload() {
//             loadPermissions();
//         }
//     };

//     loadPermissions();
//     loadModules();
//     bindEvents();

//     function bindEvents() {
//         CrudForm.bind(permissionCrud);
//         CrudDelete.bind(permissionCrud);

//         $('#btnAddPermission').on('click', function () {
//             $('#crudModalTitle').text('Custom Permission');
//             $('#crudForm')[0].reset();
//             permissionCrud.editId = null;
//             new bootstrap.Modal('#crudModal').show();
//         });

//         $(document).on('click', '.btn-edit', function () {
//             CrudForm.load(permissionCrud, $(this).data('id'));
//             $('#crudModalTitle').text('Edit Permission');
//         });
//     }

//     function loadModules() {
//         $.get(`${APP.baseUrl}/menus/options`, function (response) {
//             let html = '<option value="">Select Module</option>';
//             response.data.modules.forEach(module => {
//                 html += `<option value="${module.id}">${module.name}</option>`;
//             });
//             $('#module_id').html(html);
//         });
//     }

//     function loadPermissions() {
//         $.get(`${APP.baseUrl}/permissions/list`, function (response) {
//             if (!response.success) {
//                 APP.error(response.message);
//                 return;
//             }
//             render(response.data);
//         });
//     }

//     function render(groups) {
//         let html = `
//             <div class="d-flex justify-content-between align-items-center mb-4">
//                 <h5 class="mb-0 fw-semibold">
//                     Permission Registry
//                 </h5>
//                 <button
//                     class="btn app-btn-primary"
//                     id="btnAddPermission">
//                     <i class="bi bi-plus-lg"></i>
//                     Custom Permission
//                 </button>
//             </div>
//         `;

//         groups.forEach(group => {
//             html += `
//                 <div class="permission-group">
//                     <div class="permission-group-header">
//                         <div>
//                             <div class="permission-group-title">
//                                 ${group.module}
//                             </div>
//                             <div class="permission-count">
//                                 ${group.permissions.length} Permissions
//                             </div>
//                         </div>
//                     </div>
//                     <div class="permission-list">
//             `;

//             group.permissions.forEach(permission => {
//                 html += `
//                     <div class="permission-card">
//                         <div class="permission-name">
//                             ${permission.name}
//                         </div>
//                         <div class="permission-slug">
//                             ${permission.slug}
//                         </div>
//                         <div class="permission-footer">
//                             <span class="badge bg-secondary">
//                                 SYSTEM
//                             </span>
//                             <div class="d-flex gap-1">
//                                 <button
//                                     class="btn btn-light btn-sm btn-edit"
//                                     data-id="${permission.id}">
//                                     <i class="bi bi-pencil"></i>
//                                 </button>
//                                 <button
//                                     class="btn btn-light btn-sm text-danger btn-delete"
//                                     data-id="${permission.id}">
//                                     <i class="bi bi-trash"></i>
//                                 </button>
//                             </div>
//                         </div>
//                     </div>
//                 `;
//             });

//             html += `
//                     </div>
//                 </div>
//             `;
//         });
//         $('#permissionRegistry').html(html);
//     }
// });



document.addEventListener('DOMContentLoaded', () => {

    'use strict';

    const permissionCrud = {

        endpoint:
            `${APP.baseUrl}/permissions`,

        form:
            document.querySelector('#crudForm'),

        modal:
            document.querySelector('#crudModal'),

        editId:
            null,

        reload() {
            loadPermissions();
        },

        can(action) {
            return APP.can(
                `permission.${action}`
            );
        }
    };


    loadPermissions();
    loadModules();
    bindEvents();


    function bindEvents() {

        CrudForm.bind(permissionCrud);
        CrudDelete.bind(permissionCrud);


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $(document)
            .off(
                'click.permissionCreate',
                '#btnAddPermission'
            )
            .on(
                'click.permissionCreate',
                '#btnAddPermission',
                function () {

                    if (!permissionCrud.can('create')) {

                        APP.error(
                            'You are not authorized to create permissions.'
                        );

                        return;
                    }

                    $('#crudModalTitle')
                        .text('Custom Permission');

                    $('#crudForm')[0].reset();

                    permissionCrud.editId =
                        null;

                    bootstrap.Modal
                        .getOrCreateInstance(
                            '#crudModal'
                        )
                        .show();
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Edit
        |--------------------------------------------------------------------------
        */

        $(document)
            .off(
                'click.permissionEdit',
                '.btn-permission-edit'
            )
            .on(
                'click.permissionEdit',
                '.btn-permission-edit',
                function (e) {

                    e.preventDefault();

                    if (!permissionCrud.can('edit')) {

                        APP.error(
                            'You are not authorized to edit permissions.'
                        );

                        return;
                    }

                    permissionCrud.editId =
                        $(this).data('id');

                    CrudForm.load(
                        permissionCrud,
                        permissionCrud.editId
                    );

                    $('#crudModalTitle')
                        .text('Edit Permission');
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        $(document)
            .off(
                'click.permissionDelete',
                '.btn-permission-delete'
            )
            .on(
                'click.permissionDelete',
                '.btn-permission-delete',
                function (e) {

                    e.preventDefault();

                    if (!permissionCrud.can('delete')) {

                        APP.error(
                            'You are not authorized to delete permissions.'
                        );

                        return;
                    }

                    $(this)
                        .closest('.permission-card')
                        .find('.btn-delete')
                        .trigger('click');
                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Modules
    |--------------------------------------------------------------------------
    */

    function loadModules() {

        $.get(
            `${APP.baseUrl}/permissions/options`,
            function (response) {

                if (!response.success) {

                    APP.error(
                        response.message ||
                        'Unable to load modules.'
                    );

                    return;
                }

                let html =
                    '<option value="">Select Module</option>';

                (
                    response.data?.modules ||
                    []
                ).forEach(module => {

                    html += `
                        <option value="${module.id}">
                            ${escapeHtml(module.name)}
                        </option>
                    `;
                });

                $('#module_id')
                    .html(html);

            }
        ).fail(function (xhr) {

            if (APP.handleUnauthorized(xhr)) {
                return;
            }

            APP.error(
                'Unable to load modules.'
            );
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Permission registry
    |--------------------------------------------------------------------------
    */

    function loadPermissions() {

        $.get(
            `${APP.baseUrl}/permissions/list`,
            function (response) {

                if (!response.success) {

                    APP.error(
                        response.message ||
                        'Unable to load permissions.'
                    );

                    return;
                }

                render(
                    response.data || []
                );
            }
        ).fail(function (xhr) {

            if (APP.handleUnauthorized(xhr)) {
                return;
            }

            APP.error(
                'Unable to load permissions.'
            );
        });
    }


    function render(groups) {

        const canCreate =
            permissionCrud.can('create');

        const canEdit =
            permissionCrud.can('edit');

        const canDelete =
            permissionCrud.can('delete');


        let html = `
            <div
                class="d-flex
                       justify-content-between
                       align-items-center
                       mb-4">

                <div>
                    <h5 class="mb-1 fw-semibold">
                        Permission Registry
                    </h5>

                    <p class="text-muted mb-0">
                        Manage application capabilities.
                    </p>
                </div>

                ${canCreate
                ? `
                            <button
                                type="button"
                                class="btn app-btn-primary"
                                id="btnAddPermission">

                                <i class="bi bi-plus-lg me-1"></i>
                                Custom Permission

                            </button>
                        `
                : ''
            }

            </div>
        `;


        if (!groups.length) {

            html += `
                <div class="text-center py-5">

                    <i class="
                        bi bi-shield-exclamation
                        display-5
                        text-muted
                    "></i>

                    <h5 class="mt-3">
                        No permissions found
                    </h5>

                    <p class="text-muted mb-0">
                        No permissions are currently registered.
                    </p>

                </div>
            `;

            $('#permissionRegistry')
                .html(html);

            return;
        }


        groups.forEach(group => {

            html += `
                <div class="permission-group">

                    <div class="permission-group-header">

                        <div>

                            <div class="permission-group-title">
                                ${escapeHtml(group.module)}
                            </div>

                            <div class="permission-count">
                                ${group.permissions.length}
                                ${group.permissions.length === 1
                    ? 'Permission'
                    : 'Permissions'
                }
                            </div>

                        </div>

                    </div>

                    <div class="permission-list">
            `;


            group.permissions.forEach(permission => {

                const isSystem =
                    Number(permission.is_system) === 1;

                html += `
                    <div class="permission-card">

                        <div class="permission-name">
                            ${escapeHtml(permission.name)}
                        </div>

                        <div class="permission-slug">
                            ${escapeHtml(permission.slug)}
                        </div>

                        <div class="permission-footer">

                            <span
                                class="
                                    badge
                                    ${isSystem
                        ? 'bg-secondary'
                        : 'bg-primary-subtle text-primary'
                    }
                                ">

                                ${isSystem
                        ? 'SYSTEM'
                        : 'CUSTOM'
                    }

                            </span>

                            <div class="d-flex gap-1">

                                ${canEdit
                        ? `
                                            <button
                                                type="button"
                                                class="
                                                    btn
                                                    btn-light
                                                    btn-sm
                                                    btn-permission-edit
                                                "
                                                data-id="${permission.id}"
                                                title="Edit permission">

                                                <i class="bi bi-pencil"></i>

                                            </button>
                                        `
                        : ''
                    }

                                ${canDelete
                        ? `
                                            <button
                                                type="button"
                                                class="
                                                    btn
                                                    btn-light
                                                    btn-sm
                                                    text-danger
                                                    btn-permission-delete
                                                "
                                                data-id="${permission.id}"
                                                title="Delete permission">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                            <a
                                                href="#"
                                                class="d-none btn-delete"
                                                data-id="${permission.id}">
                                            </a>
                                        `
                        : ''
                    }

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


        $('#permissionRegistry')
            .html(html);
    }


    function escapeHtml(value) {

        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }

});