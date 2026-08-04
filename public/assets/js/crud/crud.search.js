const CrudSearch = {

    bind(crud) {

        let timer;

        $('#crudSearch').on('keyup', function () {

            clearTimeout(timer);

            timer = setTimeout(() => {

                crud.search = this.value;

                crud.page = 1;

                crud.reload();

            }, 300);

        });

        $('#crudStatus').change(function () {

            crud.status = this.value;

            crud.reload();

        });

        $('#crudPageSize').change(function () {

            crud.pageSize = this.value;

            crud.reload();

        });

        $('#btnRefresh').click(function () {

            crud.reload();

        });

    }

};