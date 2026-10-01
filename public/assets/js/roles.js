document.addEventListener('DOMContentLoaded', () => {
    const crud = new Crud({
        entity: 'Role',
        entityPlural: 'Roles',
        endpoint: `${APP.baseUrl}/roles`,
        table: '#crudTable',
        modal: '#crudModal',
        form: '#crudForm',
        permissionResource: 'role',
        permissions: {
            view: 'role.view',
            create: 'role.create',
            edit: 'role.edit',
            delete: 'role.delete',
            manage: 'role.permission'
        },
        columns: [
            {
                key: 'display_name',
                label: 'Role'
            },
            {
                key: 'permission_count',
                label: 'Permissions'
            },
            {
                key: 'status',
                label: 'Status'
            }
        ],
        onInit(instance) {
            bindIconPreview();
            bindRoleForm();
        }
    });

    function bindIconPreview() {
        $(document)
            .off('input.roleIcon change.roleIcon', '#icon')
            .on('input.roleIcon change.roleIcon', '#icon', function () {
                $('#iconPreview').attr(
                    'class',
                    $(this).val().trim() || 'bi bi-person-badge'
                );
            });
    }

    function bindRoleForm() {
        $(document)
            .off('input.roleName', '#name')
            .on('input.roleName', '#name', function () {
                if ($('#slug').data('manual')) {
                    return;
                }

                const name = $(this).val().trim();

                const slug = name
                    .toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w-]/g, '');

                $('#slug').val(slug);

                if (!$('#display_name').val().trim()) {
                    $('#display_name').val(name);
                }
            });

        $(document)
            .off('input.roleSlug', '#slug')
            .on('input.roleSlug', '#slug', function () {
                $(this).data('manual', true);
            });

        $(document)
            .off('show.bs.modal.roleForm', '#crudModal')
            .on('show.bs.modal.roleForm', '#crudModal', function () {
                $('#slug').removeData('manual');
            });
    }
});