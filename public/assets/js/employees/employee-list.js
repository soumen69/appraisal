const EmployeeList = {
    endpoint: `${APP.baseUrl}employees`,
    page: 1,

    pageSize: 10,

    search: '',

    status: '',

    orderBy: 'id',

    direction: 'desc',

    total: 0,

    lastPage: 1,

    data: [],

    init() {
        this.bindEvents();

        this.load();
    },

    bindEvents() {
        let searchTimer;

        $('#employeeSearch').on('input', () => {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(() => {
                const value =
                    $('#employeeSearch')
                        .val()
                        .trim();

                if (this.search === value) {
                    return;
                }

                this.search = value;

                this.page = 1;

                this.load();
            }, 350);
        });

        $('#employeeStatus').on('change', () => {
            this.status =
                $('#employeeStatus').val();

            this.page = 1;

            this.load();
        });

        $('#employeePageSize').on('change', () => {
            this.pageSize =
                parseInt(
                    $('#employeePageSize').val(),
                    10
                );

            this.page = 1;

            this.load();
        });

        $('#employeeRefresh').on('click', () => {
            this.load();
        });

        $(document).on(
            'click',
            '.employee-page-link',
            (e) => {
                e.preventDefault();

                const page =
                    parseInt(
                        $(e.currentTarget)
                            .data('page'),
                        10
                    );

                if (
                    !page ||
                    page < 1 ||
                    page > this.lastPage ||
                    page === this.page
                ) {
                    return;
                }

                this.page = page;

                this.load();
            }
        );

        $(document).on(
            'click',
            '.employee-sort',
            (e) => {
                e.preventDefault();

                const column =
                    $(e.currentTarget)
                        .data('sort');

                if (this.orderBy === column) {
                    this.direction =
                        this.direction === 'asc'
                            ? 'desc'
                            : 'asc';
                } else {
                    this.orderBy = column;

                    this.direction = 'asc';
                }

                this.load();
            }
        );

        $(document).on(
            'click',
            '.employee-toggle-status',
            (e) => {
                e.preventDefault();

                const id =
                    $(e.currentTarget)
                        .data('id');

                this.toggleStatus(id);
            }
        );

        $(document).on(
            'click',
            '.employee-delete',
            (e) => {
                e.preventDefault();

                const id =
                    $(e.currentTarget)
                        .data('id');

                this.delete(id);
            }
        );

        $('#employeeCheckAll').on(
            'change',
            function () {
                $('.employee-row-check')
                    .prop(
                        'checked',
                        $(this).prop('checked')
                    );
            }
        );

        $(document).on(
            'click',
            '.employee-view',
            (e) => {
                e.preventDefault();

                const id =
                    $(e.currentTarget).data('id');

                this.view(id);
            }
        );
    },

    load() {
        this.showLoading();

        $.ajax({
            url: `${this.endpoint}/list`,

            type: 'GET',

            data: {
                page: this.page,
                pageSize: this.pageSize,
                search: this.search,
                status: this.status,
                orderBy: this.orderBy,
                direction: this.direction
            },

            success: (response) => {
                if (!response.success) {
                    APP.error(
                        response.message ||
                        'Unable to load employees.'
                    );

                    return;
                }

                const result =
                    response.data || {};

                this.data =
                    result.data || [];

                this.total =
                    parseInt(
                        result.total || 0,
                        10
                    );

                this.page =
                    parseInt(
                        result.page || 1,
                        10
                    );

                this.lastPage =
                    parseInt(
                        result.lastPage || 1,
                        10
                    );

                this.render();
            },

            error: (xhr) => {
                if (xhr.status === 403) {
                    APP.error(
                        'You are not authorized.'
                    );

                    return;
                }

                APP.error(
                    'Unable to load employees.'
                );
            }
        });
    },

    render() {
        this.renderTable();

        this.renderPagination();

        this.renderSummary();

        $('#employeeCheckAll')
            .prop('checked', false);
    },

    renderTable() {
        const $body =
            $('#employeeTableBody');

        if (!this.data.length) {
            $body.html(`
                <tr>
                    <td colspan="9">
                        <div class="employee-empty">

                            <div class="employee-empty-icon">
                                <i class="bi bi-people"></i>
                            </div>

                            <h5>
                                No employees found
                            </h5>

                            <p>
                                ${this.search ||
                    this.status
                    ? 'Try changing your search or filters.'
                    : 'There are no employee records yet.'
                }
                            </p>

                            ${!this.search && !this.status
                    ? `
                                        <a
                                            href="${this.endpoint}/create"
                                            class="btn app-btn-primary"
                                        >
                                            <i class="bi bi-plus-lg me-1"></i>
                                            Add Employee
                                        </a>
                                    `
                    : ''
                }

                        </div>
                    </td>
                </tr>
            `);

            return;
        }

        let html = '';

        this.data.forEach((employee) => {
            const initials =
                this.getInitials(
                    employee.full_name ||
                    employee.first_name ||
                    'Employee'
                );

            const avatar =
                employee.profile_photo
                    ? `
                        <img
                            src="${this.escapeAttribute(
                        this.resolvePhoto(
                            employee.profile_photo
                        )
                    )}"
                            alt="${this.escapeAttribute(
                        employee.full_name || 'Employee'
                    )}"
                        >
                    `
                    : `
                        <span class="employee-avatar-fallback">
                            ${this.escapeHtml(initials)}
                        </span>
                    `;

            const status =
                employee.status === 'active'
                    ? `
                        <span class="employee-status employee-status-active">
                            Active
                        </span>
                    `
                    : `
                        <span class="employee-status employee-status-inactive">
                            Inactive
                        </span>
                    `;

            const joiningDate =
                this.formatDate(
                    employee.joining_date
                );

            html += `
                <tr>

                    <td class="text-center">

                        <input
                            type="checkbox"
                            class="form-check-input employee-row-check"
                            value="${employee.id} "
                        >

                    </td>

                    <td>

                        <div class="employee-identity">

                            <div class="employee-avatar">
                                ${avatar}
                            </div>

                            <div>

                                <div class="employee-name">
                                    ${this.escapeHtml(
                employee.full_name ||
                '-'
            )}
                                </div>

                                <div class="employee-code">
                                    ${this.escapeHtml(
                employee.employee_code ||
                'No employee code'
            )}
                                </div>

                            </div>

                        </div>

                    </td>

                    <td>

                        <div class="employee-primary-text">
                            ${this.escapeHtml(
                employee.organization_name ||
                '-'
            )}
                        </div>

                        ${employee.branch_name
                    ? `
                                    <div class="employee-secondary-text">
                                        ${this.escapeHtml(
                        employee.branch_name
                    )}
                                    </div>
                                `
                    : ''
                }

                    </td>

                    <td>

                        <div class="employee-primary-text">
                            ${this.escapeHtml(
                    employee.department_name ||
                    '-'
                )}
                        </div>

                    </td>

                    <td>

                        <div class="employee-primary-text">
                            ${this.escapeHtml(
                    employee.designation_name ||
                    '-'
                )}
                        </div>

                    </td>

                    <td>

                        <div class="employee-primary-text">
                            ${this.escapeHtml(
                    employee.reporting_manager_name ||
                    'Not assigned'
                )}
                        </div>

                    </td>

                    <td>

                        <div class="employee-primary-text">
                            ${joiningDate}
                        </div>

                    </td>

                    <td>
                        ${status}
                    </td>

                    <td class="text-center">

                        <div class="dropdown">

                            <button
                                type="button"
                                class="employee-action-btn"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                title="Actions"
                            >
                                <i class="bi bi-three-dots"></i>
                            </button>

                            <ul
                                class="dropdown-menu dropdown-menu-end employee-action-menu"
                            >

                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="${this.endpoint}/edit/${employee.id}"
                                    >
                                        <i class="bi bi-pencil"></i>
                                        Edit
                                    </a>

                                </li>

                                <li>

                                    <a
                                        class="dropdown-item employee-view"
                                        href="#"
                                        data-id="${employee.id} "
                                    >
                                        <i class="bi bi-eye"></i>
                                        View
                                    </a>

                                </li>

                                <li>

                                    <a
                                        class="dropdown-item employee-toggle-status"
                                        href="#"
                                        data-id="${employee.id} "
                                    >
                                        <i class="bi bi-power"></i>

                                        ${employee.status === 'active'
                    ? 'Deactivate'
                    : 'Activate'
                }

                                    </a>

                                </li>

                                <li>

                                    <hr class="dropdown-divider">

                                </li>

                                <li>

                                    <a
                                        class="dropdown-item text-danger employee-delete"
                                        href="#"
                                        data-id="${employee.id} "
                                    >
                                        <i class="bi bi-trash"></i>
                                        Delete
                                    </a>

                                </li>

                            </ul>

                        </div>

                    </td>

                </tr>
            `;
        });

        $body.html(html);
    },

    view(id) {

        const url = `${this.endpoint}/details/${id}`;

        console.log('Employee View URL:', url);

        $.ajax({

            url: url,

            type: 'GET',

            dataType: 'json',

            success: (response) => {

                console.log(
                    'Employee View Response:',
                    response
                );

                if (!response.success) {

                    APP.error(
                        response.message ||
                        'Unable to load employee details.'
                    );

                    return;
                }

                CrudDrawer.show(
                    'Employee Details',
                    response.data,
                    {
                        entity: 'Employee',
                        drawerRenderer: (data) =>
                            EmployeeDrawer.render(data)
                    }
                );
            },

            error: (xhr, status, error) => {

                console.error(
                    'Employee View Error:',
                    {
                        status: xhr.status,
                        textStatus: status,
                        error: error,
                        responseText: xhr.responseText
                    }
                );

                APP.error(
                    `Unable to load employee details. HTTP ${xhr.status}`
                );
            }
        });
    },


    toggleStatus(id) {
        const employee =
            this.data.find(
                item => parseInt(item.id, 10) === parseInt(id, 10)
            );

        if (!employee) {
            return;
        }

        const activate =
            employee.status !== 'active';

        Swal.fire({
            title:
                activate
                    ? 'Activate employee?'
                    : 'Deactivate employee?',

            text:
                activate
                    ? 'This employee will be allowed to access the system.'
                    : 'This employee will no longer be able to log in.',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText:
                activate
                    ? 'Activate'
                    : 'Deactivate',

            cancelButtonText: 'Cancel',

            confirmButtonColor:
                activate
                    ? '#198754'
                    : '#dc3545'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url:
                    `${this.endpoint}/toggle-status/${id}`,

                type: 'POST',

                data: {
                    [APP.csrfName]:
                        APP.csrfHash
                },

                success: (response) => {
                    if (!response.success) {
                        APP.error(
                            response.message ||
                            'Unable to update employee status.'
                        );

                        return;
                    }

                    APP.success(
                        response.message
                    );

                    this.load();
                },

                error: (xhr) => {
                    if (xhr.status === 403) {
                        APP.error(
                            'You are not authorized.'
                        );

                        return;
                    }

                    APP.error(
                        'Unable to update employee status.'
                    );
                }
            });
        });
    },

    delete(id) {
        Swal.fire({
            title: 'Delete employee?',

            text:
                'This action cannot be undone.',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Delete',

            cancelButtonText: 'Cancel',

            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url:
                    `${this.endpoint}/delete/${id}`,

                type: 'POST',

                data: {
                    [APP.csrfName]:
                        APP.csrfHash
                },

                success: (response) => {
                    if (!response.success) {
                        APP.error(
                            response.message ||
                            'Unable to delete employee.'
                        );

                        return;
                    }

                    APP.success(
                        response.message
                    );

                    this.load();
                },

                error: (xhr) => {
                    if (xhr.status === 403) {
                        APP.error(
                            'You are not authorized.'
                        );

                        return;
                    }

                    APP.error(
                        'Unable to delete employee.'
                    );
                }
            });
        });
    },

    renderPagination() {
        const $pagination =
            $('#employeePagination');

        if (this.lastPage <= 1) {
            $pagination.empty();

            return;
        }

        let html = '';

        html += `
            <li class="page-item ${this.page <= 1
                ? 'disabled'
                : ''
            }">

                <a
                    href="#"
                    class="page-link employee-page-link"
                    data-page="${this.page - 1} "
                >
                    <i class="bi bi-chevron-left"></i>
                </a>

            </li>
        `;

        const pages =
            this.getPaginationPages();

        pages.forEach((page) => {
            if (page === '...') {
                html += `
                    <li class="page-item disabled">
                        <span class="page-link">
                            ...
                        </span>
                    </li>
                `;

                return;
            }

            html += `
                <li class="page-item ${page === this.page
                    ? 'active'
                    : ''
                }">

                    <a
                        href="#"
                        class="page-link employee-page-link"
                        data-page="${page} "
                    >
                        ${page}
                    </a>

                </li>
            `;
        });

        html += `
            <li class="page-item ${this.page >= this.lastPage
                ? 'disabled'
                : ''
            }">

                <a
                    href="#"
                    class="page-link employee-page-link"
                    data-page="${this.page + 1} "
                >
                    <i class="bi bi-chevron-right"></i>
                </a>

            </li>
        `;

        $pagination.html(html);
    },

    getPaginationPages() {
        const pages = [];

        if (this.lastPage <= 7) {
            for (
                let i = 1;
                i <= this.lastPage;
                i++
            ) {
                pages.push(i);
            }

            return pages;
        }

        pages.push(1);

        if (this.page > 4) {
            pages.push('...');
        }

        const start =
            Math.max(
                2,
                this.page - 1
            );

        const end =
            Math.min(
                this.lastPage - 1,
                this.page + 1
            );

        for (
            let i = start;
            i <= end;
            i++
        ) {
            pages.push(i);
        }

        if (this.page < this.lastPage - 3) {
            pages.push('...');
        }

        pages.push(this.lastPage);

        return pages;
    },

    renderSummary() {
        if (!this.total) {
            $('#employeeSummary')
                .text('Showing 0 of 0 employees');

            return;
        }

        const start =
            ((this.page - 1) * this.pageSize) + 1;

        const end =
            Math.min(
                this.page * this.pageSize,
                this.total
            );

        $('#employeeSummary')
            .text(
                `Showing ${start} -${end} of ${this.total} employees`
            );
    },

    showLoading() {
        $('#employeeTableBody').html(`
            <tr>
                <td colspan="9">

                    <div class="employee-table-loading">

                        <div
                            class="spinner-border spinner-border-sm"
                            role="status"
                        ></div>

                        <span>
                            Loading employees...
                        </span>

                    </div>

                </td>
            </tr>
        `);
    },

    formatDate(date) {
        if (!date) {
            return '-';
        }

        const parsed =
            new Date(date);

        if (Number.isNaN(parsed.getTime())) {
            return this.escapeHtml(date);
        }

        return parsed.toLocaleDateString(
            'en-GB',
            {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }
        );
    },

    getInitials(name) {
        const parts =
            String(name)
                .trim()
                .split(/\s+/)
                .filter(Boolean);

        if (!parts.length) {
            return 'E';
        }

        if (parts.length === 1) {
            return parts[0].substring(0, 2);
        }

        return (
            parts[0].charAt(0) +
            parts[parts.length - 1].charAt(0)
        );
    },

    resolvePhoto(path) {
        if (!path) {
            return '';
        }

        if (
            path.startsWith('http://') ||
            path.startsWith('https://') ||
            path.startsWith('/')
        ) {
            return path;
        }

        return `${APP.baseUrl}${path} `;
    },

    escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    },

    escapeAttribute(value) {
        return this.escapeHtml(value);
    }
};

$(function () {
    EmployeeList.init();
});