const CrudView = {
    bind(crud) {
        $(document).on('click', '.btn-view', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            $.get(
                crud.endpoint + '/edit/' + id,
                function (response) {
                    if (!response.success) {
                        APP.error(response.message);
                        return;
                    }
                    CrudDrawer.show(
                        'Record Details',
                        response.data
                    );
                }
            );
        });
    }
};