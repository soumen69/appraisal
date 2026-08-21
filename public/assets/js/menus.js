// document.addEventListener('DOMContentLoaded', () => {

//     const crud = new Crud({
//         entity: 'Menu',
//         entityPlural: 'Menus',
//         endpoint: `${APP.baseUrl.replace(/\/$/, '')}/menus`,
//         table: '#crudTable',
//         modal: '#crudModal',
//         form: '#crudForm',
//         columns: [
//             {
//                 key: 'title',
//                 label: 'Menu'
//             },
//             {
//                 key: 'module_name',
//                 label: 'Module'
//             },
//             {
//                 key: 'parent_name',
//                 label: 'Parent'
//             },
//             {
//                 key: 'permission_name',
//                 label: 'Permission'
//             },
//             {
//                 key: 'route',
//                 label: 'Route'
//             },
//             {
//                 key: 'status',
//                 label: 'Status'
//             }
//         ],

//         onInit(instance) {
//             loadOptions(instance);
//             bindIconPreview();
//         }
//     });

//     function loadOptions(instance) {
//         $.get(`${instance.endpoint}/options`, function (response) {
//             if (!response.success) {
//                 APP.error(response.message);
//                 return;
//             }
//             fillModules(response.data.modules);
//             fillParents(response.data.parents);
//             fillPermissions(response.data.permissions);
//         });
//     }

//     function fillModules(rows) {
//         let html = '<option value="">Select Module</option>';
//         rows.forEach(row => {
//             html += `<option value="${row.id}">${row.name}</option>`;
//         });
//         $('#module_id').html(html);
//     }

//     function fillParents(rows) {
//         let html = '<option value="">Root Menu</option>';
//         rows.forEach(row => {
//             html += `<option value="${row.id}">${row.title}</option>`;
//         });
//         $('#parent_id').html(html);
//     }

//     function fillPermissions(rows) {
//         let html = '<option value="">No Permission</option>';
//         rows.forEach(row => {
//             html += `<option value="${row.id}">${row.name}</option>`;
//         });
//         $('#permission_id').html(html);
//     }

//     function bindIconPreview() {
//         $(document).on('keyup change', '#icon', function () {
//             const icon = $(this).val().trim();
//             $('#iconPreview').attr('class', icon || 'bi bi-grid');
//         });
//     }
// });


document.addEventListener('DOMContentLoaded', () => {

    const crud = new Crud({

        entity: 'Menu',

        entityPlural: 'Menus',

        endpoint: `${APP.baseUrl}/menus`,

        table: '#crudTable',

        modal: '#crudModal',

        form: '#crudForm',

        permissionResource: 'menu',

        permissions: {
            view: 'menu.view',
            create: 'menu.create',
            edit: 'menu.edit',
            delete: 'menu.delete'
        },

        columns: [

            {
                key: 'title',
                label: 'Menu'
            },

            {
                key: 'module_name',
                label: 'Module'
            },

            {
                key: 'parent_name',
                label: 'Parent'
            },

            {
                key: 'permission_name',
                label: 'Permission'
            },

            {
                key: 'route',
                label: 'Route'
            },

            {
                key: 'status',
                label: 'Status'
            }

        ],

        onInit(instance) {

            loadOptions(instance);

            bindModulePermissionDependency();

            bindIconPreview();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Cached options
    |--------------------------------------------------------------------------
    */

    let permissionRows = [];

    let currentSelectedPermission = null;


    /*
    |--------------------------------------------------------------------------
    | Load all menu options
    |--------------------------------------------------------------------------
    */

    function loadOptions(instance) {

        $.ajax({

            url:
                `${instance.endpoint}/options`,

            type:
                'GET',

            success:
                function (response) {

                    if (
                        !response ||
                        response.success !== true
                    ) {

                        APP.error(
                            response?.message ||
                            'Unable to load menu options.'
                        );

                        return;
                    }


                    const data =
                        response.data || {};


                    fillModules(
                        Array.isArray(
                            data.modules
                        )
                            ? data.modules
                            : []
                    );


                    fillParents(
                        Array.isArray(
                            data.parents
                        )
                            ? data.parents
                            : []
                    );


                    permissionRows =
                        Array.isArray(
                            data.permissions
                        )
                            ? data.permissions
                            : [];


                    /*
                    |--------------------------------------------------------------------------
                    | On edit, the existing form may already contain module_id
                    |--------------------------------------------------------------------------
                    */

                    const moduleId =
                        $('#module_id')
                            .val();


                    if (moduleId) {

                        fillPermissions(
                            moduleId,
                            currentSelectedPermission
                        );

                    } else {

                        resetPermissions();

                    }

                },

            error:
                function (xhr) {

                    if (
                        APP.handleUnauthorized &&
                        APP.handleUnauthorized(xhr)
                    ) {
                        return;
                    }

                    APP.error(
                        xhr.responseJSON?.message ||
                        'Unable to load menu options.'
                    );

                }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Module dropdown
    |--------------------------------------------------------------------------
    */

    function fillModules(rows) {

        let html =
            '<option value="">Select Module</option>';


        rows.forEach(
            row => {

                html += `
                    <option value="${escapeHtml(row.id)}">
                        ${escapeHtml(row.name)}
                    </option>
                `;

            }
        );


        $('#module_id')
            .html(html);
    }


    /*
    |--------------------------------------------------------------------------
    | Parent menu dropdown
    |--------------------------------------------------------------------------
    */

    function fillParents(rows) {

        let html =
            '<option value="">Root Menu</option>';


        rows.forEach(
            row => {

                html += `
                    <option value="${escapeHtml(row.id)}">
                        ${escapeHtml(row.title)}
                    </option>
                `;

            }
        );


        $('#parent_id')
            .html(html);
    }


    /*
    |--------------------------------------------------------------------------
    | Permission dropdown
    |--------------------------------------------------------------------------
    |
    | Only permissions belonging to the selected module are displayed.
    |
    */

    function fillPermissions(
        moduleId,
        selectedPermissionId = null
    ) {

        const $permission =
            $('#permission_id');


        if (!$permission.length) {
            return;
        }


        if (!moduleId) {

            resetPermissions();

            return;
        }


        const normalizedModuleId =
            String(moduleId);


        const filtered =
            permissionRows.filter(
                permission =>
                    String(
                        permission.module_id
                    ) ===
                    normalizedModuleId
            );


        let html =
            '<option value="">No Permission</option>';


        filtered.forEach(
            permission => {

                html += `
                    <option value="${escapeHtml(permission.id)}">
                        ${escapeHtml(
                    permission.name
                )}
                    </option>
                `;

            }
        );


        $permission
            .html(html);


        /*
        |--------------------------------------------------------------------------
        | Restore selected permission
        |--------------------------------------------------------------------------
        */

        if (
            selectedPermissionId !== null &&
            selectedPermissionId !== undefined &&
            selectedPermissionId !== ''
        ) {

            const exists =
                filtered.some(
                    permission =>
                        String(
                            permission.id
                        ) ===
                        String(
                            selectedPermissionId
                        )
                );


            if (exists) {

                $permission.val(
                    String(
                        selectedPermissionId
                    )
                );

            } else {

                $permission.val('');

            }

        } else {

            $permission.val('');

        }


        $permission.trigger('change');

    }


    /*
    |--------------------------------------------------------------------------
    | Reset permission dropdown
    |--------------------------------------------------------------------------
    */

    function resetPermissions() {

        $('#permission_id')
            .html(`
                <option value="">
                    Select Module First
                </option>
            `)
            .val('')
            .trigger('change');

    }


    /*
    |--------------------------------------------------------------------------
    | Module → Permission dependency
    |--------------------------------------------------------------------------
    */

    function bindModulePermissionDependency() {

        $(document)
            .off(
                'change.menuPermissionDependency',
                '#module_id'
            )
            .on(
                'change.menuPermissionDependency',
                '#module_id',
                function () {

                    const moduleId =
                        $(this).val();


                    /*
                    |--------------------------------------------------------------------------
                    | Once module changes manually,
                    | previous permission is no longer trusted.
                    |--------------------------------------------------------------------------
                    */

                    currentSelectedPermission =
                        null;


                    fillPermissions(
                        moduleId
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Capture existing permission when editing
        |--------------------------------------------------------------------------
        |
        | CrudForm.load() can populate the form after our options request.
        | This listener ensures the selected permission is preserved when
        | module_id changes programmatically during edit.
        |
        */

        $(document)
            .off(
                'change.menuPermissionCapture',
                '#permission_id'
            )
            .on(
                'change.menuPermissionCapture',
                '#permission_id',
                function () {

                    const value =
                        $(this).val();

                    if (
                        value !== null &&
                        value !== undefined &&
                        value !== ''
                    ) {

                        currentSelectedPermission =
                            value;

                    }

                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Icon preview
    |--------------------------------------------------------------------------
    */

    function bindIconPreview() {

        $(document)
            .off(
                'keyup.menuIcon change.menuIcon',
                '#icon'
            )
            .on(
                'keyup.menuIcon change.menuIcon',
                '#icon',
                function () {

                    const icon =
                        $(this)
                            .val()
                            .trim();


                    $('#iconPreview')
                        .attr(
                            'class',
                            icon ||
                            'bi bi-grid'
                        );

                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | HTML escaping
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return String(
            value ?? ''
        )
            .replace(
                /&/g,
                '&amp;'
            )
            .replace(
                /</g,
                '&lt;'
            )
            .replace(
                />/g,
                '&gt;'
            )
            .replace(
                /"/g,
                '&quot;'
            )
            .replace(
                /'/g,
                '&#039;'
            );

    }

});