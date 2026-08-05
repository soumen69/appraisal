const CrudPagination = {

    render(crud) {
        const totalPages = Math.ceil(crud.total / crud.pageSize);
        if (totalPages <= 1) {
            crud.pagination.innerHTML = '';
            return;
        }
        let html = '<nav><ul class="pagination pagination-sm justify-content-end mb-0">';
        html += `
            <li class="page-item ${crud.page === 1 ? 'disabled' : ''}">
                <a href="#" class="page-link crud-page" data-page="${crud.page - 1}">
                    Previous
                </a>
            </li>
        `;
        for (
            let i = Math.max(1, crud.page - 2);
            i <= Math.min(totalPages, crud.page + 2);
            i++
        ) {
            html += `
                <li class="page-item ${crud.page === i ? 'active' : ''}">
                    <a
                        href="#"
                        class="page-link crud-page"
                        data-page="${i}">
                        ${i}
                    </a>
                </li>
            `;
        }
        html += `
            <li class="page-item ${crud.page === totalPages ? 'disabled' : ''}">
                <a href="#" class="page-link crud-page" data-page="${crud.page + 1}">
                    Next
                </a>
            </li>
        `;
        html += '</ul></nav>';
        crud.pagination.innerHTML = html;
        $('.crud-page').on('click', function (e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (
                page < 1 ||
                page > totalPages ||
                page === crud.page
            ) {
                return;
            }
            crud.page = page;
            crud.reload();
        });
    }
};