const CrudApi = {

    list(crud) {
        crud.body.innerHTML = CrudUtils.loadingRows(
            crud.columns.length + 2
        );

        $.ajax({
            url: crud.endpoint + '/list',
            type: 'GET',
            data: {
                page: crud.page,
                pageSize: crud.pageSize,
                search: crud.search,
                status: crud.status,
                orderBy: crud.orderBy,
                direction: crud.direction
            },
            success(response) {
                if (!response.success) {
                    APP.error(response.message);
                    return;
                }
                crud.data = response.data.data || [];
                crud.total = response.data.total || 0;
                crud.page = response.data.page || 1;
                crud.lastPage = response.data.lastPage || 1;
                CrudTable.render(crud);
                CrudPagination.render(crud);
                $('#crudSummary').html(
                    `Showing ${crud.data.length} of ${crud.total} records`
                );
            },
            error(xhr) {
                if (xhr.status === 403) {
                    APP.error('You are not authorized.');
                    return;
                }
                APP.error('Request failed.');
            }
        });
    }
};
