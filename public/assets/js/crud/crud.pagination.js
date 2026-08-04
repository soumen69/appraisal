const CrudPagination = {

    render(crud) {

        let pages = Math.ceil(crud.total / crud.pageSize);

        if (pages <= 1) {

            crud.pagination.innerHTML = '';

            return;

        }

        let html = '';

        html += '<nav><ul class="pagination pagination-sm mb-0">';

        for (let i = 1; i <= pages; i++) {

            html += `

                <li class="page-item ${i === crud.page ? 'active' : ''}">

                    <a

                        class="page-link crud-page"

                        href="#"

                        data-page="${i}">

                        ${i}

                    </a>

                </li>

            `;

        }

        html += '</ul></nav>';

        crud.pagination.innerHTML = html;

        $('.crud-page').click(function (e) {

            e.preventDefault();

            crud.page = $(this).data('page');

            crud.reload();

        });

    }

};