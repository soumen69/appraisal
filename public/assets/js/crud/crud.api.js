const CrudApi = {

    list(crud) {

        if (!crud.can('view')) {

            crud.body.innerHTML = `
                <tr>
                    <td
                        colspan="${crud.columns.length + 2}"
                        class="text-center py-5">

                        <i class="bi bi-shield-lock display-5 text-muted"></i>

                        <h5 class="mt-3">
                            Access Restricted
                        </h5>

                        <p class="text-muted mb-0">
                            You are not authorized to view these records.
                        </p>

                    </td>
                </tr>
            `;

            $('#crudSummary')
                .html('Access restricted');

            return;
        }


        crud.body.innerHTML =
            CrudUtils.loadingRows(
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
                crud.data = response.data?.data || [];
                crud.total = response.data?.total || 0;
                crud.page = response.data?.page || 1;
                crud.lastPage = response.data?.lastPage || 1;

                CrudTable.render(crud);
                CrudPagination.render(crud);

                $('#crudSummary').html(
                    `Showing ${crud.data.length} of ${crud.total} records`
                );

                crud.applyCreatePermission();
            },

            error(xhr) {

                if (
                    APP.handleUnauthorized(xhr)
                ) {
                    return;
                }

                APP.error(
                    xhr.responseJSON?.message ||
                    'Request failed.'
                );
            }
        });
    }
};
