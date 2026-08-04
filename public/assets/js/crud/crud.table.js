const CrudTable = {

    render(crud) {

        if (crud.data.length === 0) {

            crud.body.innerHTML = `

                <tr>

                    <td colspan="${crud.columns.length + 2}">

                        <div class="table-empty">

                            <i class="bi bi-folder2-open"></i>

                            <h5>No records found</h5>

                            <p>Try changing your search or filters.</p>

                        </div>

                    </td>

                </tr>

            `;

            return;

        }

        let html = '';

        crud.data.forEach(row => {

            html += '<tr>';

            html += `<td><input type="checkbox" value="${row.id}"></td>`;

            crud.columns.forEach(col => {

                let value = row[col.key] ?? '';

                if (col.key === 'status') {

                    value = row.status === 'active'

                        ? '<span class="status-badge status-active">Active</span>'

                        : '<span class="status-badge status-inactive">Inactive</span>';

                }

                html += `<td>${value}</td>`;

            });

            html += `

                <td>

                    <div class="dropdown">

                        <button
                            class="action-btn"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-three-dots"></i>

                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">

                            <li>

                                <a class="dropdown-item btn-view"
                                href="#"
                                data-id="${row.id}">

                                    <i class="bi bi-eye me-2"></i>

                                    View

                                </a>

                            </li>

                            <li>

                                <a class="dropdown-item btn-edit"
                                href="#"
                                data-id="${row.id}">

                                    <i class="bi bi-pencil-square me-2"></i>

                                    Edit

                                </a>

                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>

                                <a class="dropdown-item text-danger btn-delete"
                                href="#"
                                data-id="${row.id}">

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