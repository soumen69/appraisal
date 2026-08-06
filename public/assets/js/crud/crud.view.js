const CrudView = {

    bind(crud) {
        $(document)
            .off('click', '.btn-view')
            .on('click', '.btn-view', function (e) {
                e.preventDefault();
                const id = $(this).data('id');

                $.get(
                    `${crud.endpoint}/edit/${id}`,
                    function (response) {
                        if (!response.success) {
                            APP.error(response.message);
                            return;
                        }

                        const title = crud.entity
                            ? `${crud.entity} Details`
                            : 'Details';

                        CrudDrawer.show(
                            title,
                            response.data,
                            crud
                        );
                    }
                );
            });
    }
};