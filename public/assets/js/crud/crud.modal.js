// const CrudModal = {

//     bind(crud) {
//         $('#btnAdd').on('click', function () {
//             CrudForm.reset(crud);
//             $('#crudModalTitle').text(`Create ${crud.entity}`);
//             new bootstrap.Modal(crud.modal).show();
//         });

//         $(document).on('click', '.btn-edit', function (e) {
//             e.preventDefault();
//             CrudForm.load(
//                 crud,
//                 $(this).data('id')
//             );
//         });

//         $(crud.modal).on('hidden.bs.modal', function () {
//             CrudForm.reset(crud);
//         });
//     }
// };

const CrudModal = {

    bind(crud) {

        /*
         * ---------------------------------------------------------
         * Modal CRUD only
         *
         * Employee uses dedicated create/edit pages,
         * therefore it has no modal and no CRUD form.
         * ---------------------------------------------------------
         */
        if (!crud.modal) {
            return;
        }


        /*
         * ---------------------------------------------------------
         * Create
         * ---------------------------------------------------------
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
         * ---------------------------------------------------------
         * Edit
         * ---------------------------------------------------------
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

                    CrudForm.load(
                        crud,
                        $(this).data('id')
                    );
                }
            );


        /*
         * ---------------------------------------------------------
         * Reset when modal closes
         * ---------------------------------------------------------
         */
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