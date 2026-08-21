// (() => {

//     'use strict';


//     const BranchPage = {

//         init() {

//             this.crud = new Crud({

//                 endpoint:
//                     window.BranchConfig?.endpoint ||
//                     '/branches',

//                 table:
//                     '#crudTable',

//                 modal:
//                     '#crudModal',

//                 form:
//                     '#crudForm',

//                 entity:
//                     'Branch',

//                 entityPlural:
//                     'Branches',

//                 filters: {

//                     organizationId:
//                         '#crudOrganization'

//                 },


//                 columns: [

//                     {
//                         key: 'branch_code',
//                         label: 'Code'
//                     },

//                     {
//                         key: 'name',
//                         label: 'Branch'
//                     },

//                     {
//                         key: 'organization_name',
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

//         BranchPage.init();

//     });

// })();



(() => {

    'use strict';

    const BranchPage = {

        init() {

            this.crud = new Crud({

                endpoint:
                    window.BranchConfig?.endpoint ||
                    `${APP.baseUrl}/branches`,

                table:
                    '#crudTable',

                modal:
                    '#crudModal',

                form:
                    '#crudForm',

                entity:
                    'Branch',

                entityPlural:
                    'Branches',

                permissionResource:
                    'branch',

                permissions: {
                    view: 'branch.view',
                    create: 'branch.create',
                    edit: 'branch.edit',
                    delete: 'branch.delete'
                },

                filters: {
                    organizationId:
                        '#crudOrganization'
                },

                columns: [

                    {
                        key: 'branch_code',
                        label: 'Code'
                    },

                    {
                        key: 'name',
                        label: 'Branch'
                    },

                    {
                        key: 'organization_name',
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

        BranchPage.init();

    });

})();