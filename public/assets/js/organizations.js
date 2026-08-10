(() => {

    'use strict';

    const OrganizationPage = {

        init() {

            this.crud = new Crud({

                endpoint:
                    window.OrganizationConfig?.endpoint ||
                    '/organizations',

                table: '#crudTable',

                modal: '#crudModal',

                form: '#crudForm',

                entity: 'Organization',

                entityPlural: 'Organizations',

                columns: [

                    {
                        key: 'organization_code',
                        label: 'Code'
                    },

                    {
                        key: 'name',
                        label: 'Organization'
                    },

                    {
                        key: 'email',
                        label: 'Email'
                    },

                    {
                        key: 'phone',
                        label: 'Phone'
                    },

                    {
                        key: 'city',
                        label: 'Location'
                    },

                    {
                        key: 'status',
                        label: 'Status'
                    }

                ]

            });

        }

    };


    $(function () {

        OrganizationPage.init();

    });

})();