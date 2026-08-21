// document.addEventListener('DOMContentLoaded', () => {

//     const crud = new Crud({

//         entity: 'Role',

//         entityPlural: 'Roles',

//         endpoint: `${APP.baseUrl.replace(/\/$/, '')}/roles`,

//         table: '#crudTable',

//         modal: '#crudModal',

//         form: '#crudForm',

//         columns: [

//             {
//                 key: 'display_name',
//                 label: 'Role'
//             },

//             {
//                 key: 'parent_role',
//                 label: 'Parent'
//             },

//             {
//                 key: 'permission_count',
//                 label: 'Permissions'
//             },

//             {
//                 key: 'status',
//                 label: 'Status'
//             }

//         ],

//         onInit(instance) {

//             loadParentRoles(instance);

//             bindIconPreview();

//         }

//     });

//     function loadParentRoles(instance) {

//         $.get(`${instance.endpoint}/options`, function (response) {

//             if (!response.success) {

//                 APP.error(response.message);

//                 return;

//             }

//             let html = '<option value="">Root Role</option>';

//             const parents = response.data.parents ?? [];

//             parents.forEach(role => {
//                 html += `<option value="${role.id}">
//                     ${role.display_name || role.name}
//                 </option>`;
//             });

//             // $('#parent_role_id').html(html);

//         });

//     }

//     function bindIconPreview() {

//         $(document).on('keyup change', '#icon', function () {

//             $('#iconPreview').attr(
//                 'class',
//                 $(this).val() || 'bi bi-person-badge'
//             );

//         });

//     }

//     $(document).on('keyup', '#name', function () {

//         if ($('#slug').data('manual')) {
//             return;
//         }

//         const slug = $(this)
//             .val()
//             .trim()
//             .toLowerCase()
//             .replace(/\s+/g, '-')
//             .replace(/[^\w-]/g, '');

//         $('#slug').val(slug);

//         if (!$('#display_name').val()) {
//             $('#display_name').val($(this).val());
//         }

//     });

//     $('#slug').on('input', function () {

//         $(this).data('manual', true);

//     });

// });


document.addEventListener('DOMContentLoaded', () => {

    const crud = new Crud({

        entity:
            'Role',

        entityPlural:
            'Roles',

        endpoint:
            `${APP.baseUrl}/roles`,

        table:
            '#crudTable',

        modal:
            '#crudModal',

        form:
            '#crudForm',

        permissionResource:
            'role',

        permissions: {

            view:
                'role.view',

            create:
                'role.create',

            edit:
                'role.edit',

            delete:
                'role.delete',

            manage:
                'role.permission'
        },

        columns: [

            {
                key:
                    'display_name',
                label:
                    'Role'
            },

            {
                key:
                    'permission_count',
                label:
                    'Permissions'
            },

            {
                key:
                    'status',
                label:
                    'Status'
            }

        ],

        onInit(instance) {

            bindIconPreview();

        }

    });


    function bindIconPreview() {

        $(document)
            .off(
                'keyup.roleIcon change.roleIcon',
                '#icon'
            )
            .on(
                'keyup.roleIcon change.roleIcon',
                '#icon',
                function () {

                    $('#iconPreview')
                        .attr(
                            'class',
                            $(this)
                                .val()
                                .trim() ||
                            'bi bi-person-badge'
                        );

                }
            );
    }


    $(document)
        .off(
            'keyup.roleName',
            '#name'
        )
        .on(
            'keyup.roleName',
            '#name',
            function () {

                if (
                    $('#slug')
                        .data('manual')
                ) {
                    return;
                }

                const slug =
                    $(this)
                        .val()
                        .trim()
                        .toLowerCase()
                        .replace(
                            /\s+/g,
                            '-'
                        )
                        .replace(
                            /[^\w-]/g,
                            ''
                        );

                $('#slug')
                    .val(slug);

                if (
                    !$('#display_name').val()
                ) {
                    $('#display_name')
                        .val(
                            $(this).val()
                        );
                }
            }
        );


    $(document)
        .off(
            'input.roleSlug',
            '#slug'
        )
        .on(
            'input.roleSlug',
            '#slug',
            function () {

                $(this)
                    .data(
                        'manual',
                        true
                    );

            }
        );

});