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

                crud.data = response.data || [];

                crud.total = response.total || 0;

                CrudTable.render(crud);

                CrudPagination.render(crud);

            },

            error() {

                APP.error('Unable to load data.');

            }

        });

    }

};