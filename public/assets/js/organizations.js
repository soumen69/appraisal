// (() => {

//     'use strict';

//     const OrganizationPage = {

//         init() {

//             this.crud = new Crud({

//                 endpoint:
//                     window.OrganizationConfig?.endpoint ||
//                     '/organizations',

//                 table: '#crudTable',

//                 modal: '#crudModal',

//                 form: '#crudForm',

//                 entity: 'Organization',

//                 entityPlural: 'Organizations',

//                 columns: [

//                     {
//                         key: 'organization_code',
//                         label: 'Code'
//                     },

//                     {
//                         key: 'name',
//                         label: 'Organization'
//                     },

//                     {
//                         key: 'email',
//                         label: 'Email'
//                     },

//                     {
//                         key: 'phone',
//                         label: 'Phone'
//                     },

//                     {
//                         key: 'city',
//                         label: 'Location'
//                     },

//                     {
//                         key: 'status',
//                         label: 'Status'
//                     }

//                 ]

//             });

//         }

//     };


//     $(function () {

//         OrganizationPage.init();

//     });

// })();



(() => {

    'use strict';

    const OrganizationPage = {

        init() {

            this.crud = new Crud({

                endpoint:
                    window.OrganizationConfig?.endpoint ||
                    `${APP.baseUrl}/organizations`,

                table:
                    '#crudTable',

                modal:
                    '#crudModal',

                form:
                    '#crudForm',

                entity:
                    'Organization',

                entityPlural:
                    'Organizations',

                permissionResource:
                    'organization',

                permissions: {
                    view: 'organization.view',
                    create: 'organization.create',
                    edit: 'organization.edit',
                    delete: 'organization.delete'
                },

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