const CrudModal = {

    bind(crud) {
        $('#btnAdd').on('click', function () {
            CrudForm.reset(crud);
            $('#crudModalTitle').text(`Create ${crud.entity}`);
            new bootstrap.Modal(crud.modal).show();
        });

        $(document).on('click', '.btn-edit', function (e) {
            e.preventDefault();
            CrudForm.load(
                crud,
                $(this).data('id')
            );
        });

        $(crud.modal).on('hidden.bs.modal', function () {
            CrudForm.reset(crud);
        });
    }
};