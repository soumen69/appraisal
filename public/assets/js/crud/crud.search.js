const CrudSearch = {

    bind(crud) {
        let timer;
        $('#crudSearch').on('keyup', function () {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const value = $(this).val().trim();
                if (crud.search === value) {
                    return;
                }
                crud.search = value;
                crud.page = 1;
                crud.reload();
            }, 400);
        });
        $('#crudStatus').on('change', function () {
            crud.status = $(this).val();
            crud.page = 1;
            crud.reload();
        });
        $('#crudPageSize').on('change', function () {
            crud.pageSize = parseInt($(this).val());
            crud.page = 1;
            crud.reload();
        });
        $('#btnRefresh').on('click', function () {
            crud.reload();
        });
    }
};