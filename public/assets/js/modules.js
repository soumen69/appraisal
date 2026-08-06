document.addEventListener('DOMContentLoaded', () => {
    new Crud({
        entity: 'Module',
        entityPlural: 'Modules',
        endpoint: `${APP.baseUrl.replace(/\/$/, '')}/modules`,
        table: '#crudTable',
        modal: '#crudModal',
        form: '#crudForm',
        columns: [
            {
                key: 'name',
                label: 'Module'
            },
            {
                key: 'slug',
                label: 'Slug'
            },
            {
                key: 'route',
                label: 'Route'
            },
            {
                key: 'status',
                label: 'Status'
            }
        ]
    });

    $(document).on('keyup', '#name', function () {
        if ($('#slug').data('manual')) {
            return;
        }

        $('#slug').val(
            $(this)
                .val()
                .trim()
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w-]/g, '')
        );
    });

    $('#slug').on('keyup', function () {
        $(this).data('manual', true);
    });
});