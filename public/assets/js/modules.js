document.addEventListener('DOMContentLoaded', () => {

    new Crud({

        endpoint: baseUrl + '/modules',

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

});