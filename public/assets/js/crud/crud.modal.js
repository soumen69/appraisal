const CrudModal = {

    bind(crud) {

        $('#btnAdd').click(function () {

            crud.form.reset();

            $('#crudModalTitle').text('Add');

            new bootstrap.Modal(crud.modal).show();

        });

        $(document).on('click', '.btn-edit', function (e) {

            e.preventDefault();

            CrudForm.load(

                crud,

                $(this).data('id')

            );

        });

    }

};