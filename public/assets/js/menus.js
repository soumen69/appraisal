document.addEventListener('DOMContentLoaded', () => {

    const crud = new Crud({
        entity: 'Menu',
        entityPlural: 'Menus',
        endpoint: `${APP.baseUrl.replace(/\/$/, '')}/menus`,
        table: '#crudTable',
        modal: '#crudModal',
        form: '#crudForm',
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
            bindIconPreview();
        }
    });

    function loadOptions(instance) {
        $.get(`${instance.endpoint}/options`, function (response) {
            if (!response.success) {
                APP.error(response.message);
                return;
            }
            fillModules(response.data.modules);
            fillParents(response.data.parents);
            fillPermissions(response.data.permissions);
        });
    }

    function fillModules(rows) {
        let html = '<option value="">Select Module</option>';
        rows.forEach(row => {
            html += `<option value="${row.id}">${row.name}</option>`;
        });
        $('#module_id').html(html);
    }

    function fillParents(rows) {
        let html = '<option value="">Root Menu</option>';
        rows.forEach(row => {
            html += `<option value="${row.id}">${row.title}</option>`;
        });
        $('#parent_id').html(html);
    }

    function fillPermissions(rows) {
        let html = '<option value="">No Permission</option>';
        rows.forEach(row => {
            html += `<option value="${row.id}">${row.name}</option>`;
        });
        $('#permission_id').html(html);
    }

    function bindIconPreview() {
        $(document).on('keyup change', '#icon', function () {
            const icon = $(this).val().trim();
            $('#iconPreview').attr('class', icon || 'bi bi-grid');
        });
    }
});