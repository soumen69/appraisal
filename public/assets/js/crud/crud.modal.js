

// const CrudModal = {

//     bind(crud) {

//         if (!crud.modal) {
//             return;
//         }
//         $(document)
//             .off(
//                 'click.crudModal',
//                 '#btnAdd'
//             )
//             .on(
//                 'click.crudModal',
//                 '#btnAdd',
//                 function (e) {

//                     e.preventDefault();

//                     CrudForm.reset(crud);

//                     $('#crudModalTitle')
//                         .text(
//                             `Create ${crud.entity}`
//                         );

//                     bootstrap.Modal
//                         .getOrCreateInstance(
//                             crud.modal
//                         )
//                         .show();
//                 }
//             );

//         $(document)
//             .off(
//                 'click.crudModalEdit',
//                 '.btn-edit'
//             )
//             .on(
//                 'click.crudModalEdit',
//                 '.btn-edit',
//                 function (e) {

//                     e.preventDefault();

//                     CrudForm.load(
//                         crud,
//                         $(this).data('id')
//                     );
//                 }
//             );


//         $(crud.modal)
//             .off(
//                 'hidden.bs.modal.crudModal'
//             )
//             .on(
//                 'hidden.bs.modal.crudModal',
//                 function () {

//                     CrudForm.reset(crud);

//                 }
//             );
//     }

// };


const CrudModal = {

    bind(crud) {

        if (!crud.modal) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $(document)
            .off(
                'click.crudModal',
                '#btnAdd'
            )
            .on(
                'click.crudModal',
                '#btnAdd',
                function (e) {

                    e.preventDefault();

                    if (!crud.can('create')) {
                        APP.error(
                            'You are not authorized to create this record.'
                        );

                        return;
                    }

                    CrudForm.reset(crud);

                    $('#crudModalTitle')
                        .text(
                            `Create ${crud.entity}`
                        );

                    bootstrap.Modal
                        .getOrCreateInstance(
                            crud.modal
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
                'click.crudModalEdit',
                '.btn-edit'
            )
            .on(
                'click.crudModalEdit',
                '.btn-edit',
                function (e) {

                    e.preventDefault();

                    if (!crud.can('edit')) {
                        APP.error(
                            'You are not authorized to edit this record.'
                        );

                        return;
                    }

                    CrudForm.load(
                        crud,
                        $(this).data('id')
                    );
                }
            );


        $(crud.modal)
            .off(
                'hidden.bs.modal.crudModal'
            )
            .on(
                'hidden.bs.modal.crudModal',
                function () {

                    CrudForm.reset(crud);

                }
            );
    }
};