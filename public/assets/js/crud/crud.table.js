const CrudTable = {

    render(crud) {
        if (!crud.data.length) {
            const entity = crud.entity || 'Record';
            crud.body.innerHTML = `
                <tr>
                    <td colspan="${crud.columns.length + 2}">
                        <div class="table-empty">

                            <i class="bi bi-database display-5 text-secondary"></i>

                            <h5 class="mt-3">
                                No ${entity}s Found
                            </h5>

                            <p class="text-muted mb-3">
                                No ${entity.toLowerCase()} records are available.
                            </p>

                            <button
                                class="btn app-btn-primary"
                                id="btnEmptyCreate">
                                <i class="bi bi-plus-lg"></i>
                                Create ${entity}
                            </button>
                        </div>
                    </td>
                </tr>
            `;

            $(document)
                .off('click', '#btnEmptyCreate')
                .on('click', '#btnEmptyCreate', function () {

                    $('#btnAdd').trigger('click');

                });

            return;
        }

        let html = '';

        crud.data.forEach(row => {

            html += '<tr>';

            html += `
                <td>
                    <input
                        type="checkbox"
                        value="${row.id}">
                </td>
            `;

            crud.columns.forEach(col => {
                let value = row[col.key] ?? '';
                if (typeof col.render === 'function') {
                    value = col.render(value, row);
                }
                else {
                    switch (col.key) {
                        case 'status':
                            value = row.status === 'active'
                                ? `<span class="status-badge status-active">Active</span>`
                                : `<span class="status-badge status-inactive">Inactive</span>`;
                            break;
                        case 'is_sidebar':
                            value = row.is_sidebar == 1
                                ? `<span class="badge bg-success-subtle text-success">Yes</span>`
                                : `<span class="badge bg-secondary-subtle text-secondary">No</span>`;
                            break;
                        case 'is_visible':
                            value = row.is_visible == 1
                                ? `<span class="badge bg-success-subtle text-success">Visible</span>`
                                : `<span class="badge bg-warning-subtle text-warning">Hidden</span>`;
                            break;
                        case 'icon':
                            value = value
                                ? `<i class="${value} fs-5"></i>`
                                : '-';
                            break;
                        default: value = value || '-';
                    }
                }
                html += `<td>${value}</td>`;
            });

            html += `
                <td>
                    <div class="dropdown">
                        <button class="action-btn" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item btn-view" href="#" data-id="${row.id}">
                                    <i class="bi bi-eye me-2"></i>
                                    View
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item btn-edit" href="#" data-id="${row.id}">
                                    <i class="bi bi-pencil me-2"></i>
                                    Edit
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger btn-delete" href="#" data-id="${row.id}">
                                    <i class="bi bi-trash me-2"></i>
                                    Delete
                                </a>
                            </li>
                        </ul>
                    </div>
              </td>
            `;
            html += '</tr>';
        });
        crud.body.innerHTML = html;
    }
};