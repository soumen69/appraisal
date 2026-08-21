const BranchList = {
    endpoint: window.BranchConfig.endpoint,
    page: 1,
    pageSize: 10,
    search: '',
    status: '',
    organizationId: '',
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
        let timer;

        $('#branchSearch').on(
            'input',
            () => {
                clearTimeout(timer);

                timer = setTimeout(() => {
                    this.search =
                        $('#branchSearch')
                            .val()
                            .trim();

                    this.page = 1;

                    this.load();
                }, 350);
            }
        );

        $('#branchOrganization').on(
            'change',
            () => {
                this.organizationId =
                    $('#branchOrganization')
                        .val();

                this.page = 1;

                this.load();
            }
        );

        $('#branchStatus').on(
            'change',
            () => {
                this.status =
                    $('#branchStatus')
                        .val();

                this.page = 1;

                this.load();
            }
        );

        $('#branchPageSize').on(
            'change',
            () => {
                this.pageSize =
                    parseInt(
                        $('#branchPageSize')
                            .val(),
                        10
                    );

                this.page = 1;

                this.load();
            }
        );

        $('#branchRefresh').on(
            'click',
            () => this.load()
        );

        $(document).on(
            'click',
            '.branch-page-link',
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
            '.branch-delete',
            (e) => {
                e.preventDefault();

                this.delete(
                    $(e.currentTarget)
                        .data('id')
                );
            }
        );

        $('#branchCheckAll').on(
            'change',
            function () {
                $('.branch-row-check')
                    .prop(
                        'checked',
                        $(this).prop('checked')
                    );
            }
        );
    },

    load() {
        $('#branchBody').html(`
            <tr>
                <td colspan="7">
                    <div class="branch-loading">

                        <span
                            class="spinner-border spinner-border-sm"
                        ></span>

                        Loading branches...

                    </div>
                </td>
            </tr>
        `);

        $.ajax({
            url:
                `${this.endpoint}/list`,

            type: 'GET',

            data: {
                page: this.page,

                pageSize: this.pageSize,

                search: this.search,

                status: this.status,

                organizationId:
                    this.organizationId,

                orderBy: this.orderBy,

                direction: this.direction
            },

            success: (response) => {
                if (!response.success) {
                    APP.error(
                        response.message ||
                        'Unable to load branches.'
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
                    'Unable to load branches.'
                );
            }
        });
    },

    render() {
        this.renderTable();

        this.renderPagination();

        this.renderSummary();

        $('#branchCheckAll')
            .prop('checked', false);
    },

    renderTable() {
        if (!this.data.length) {
            $('#branchBody').html(`
                <tr>

                    <td colspan="7">

                        <div class="branch-empty">

                            <div class="branch-empty-icon">
                                <i class="bi bi-building"></i>
                            </div>

                            <h5>
                                No branches found
                            </h5>

                            <p>
                                ${this.search ||
                    this.status ||
                    this.organizationId
                    ? 'Try changing your filters.'
                    : 'No branches have been created yet.'
                }
                            </p>

                            ${!this.search &&
                    !this.status &&
                    !this.organizationId
                    ? `
                                        <a
                                            href="${this.endpoint}/create"
                                            class="btn app-btn-primary"
                                        >
                                            <i class="bi bi-plus-lg me-1"></i>
                                            Add Branch
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

        this.data.forEach(
            (branch) => {
                const status =
                    branch.status === 'active'
                        ? `
                            <span class="status-badge status-active">
                                Active
                            </span>
                        `
                        : `
                            <span class="status-badge status-inactive">
                                Inactive
                            </span>
                        `;

                const location =
                    [
                        branch.city,
                        branch.state
                    ]
                        .filter(Boolean)
                        .join(', ');

                html += `
                    <tr>

                        <td class="text-center">

                            <input
                                type="checkbox"
                                class="form-check-input branch-row-check"
                                value="${branch.id} "
                            >

                        </td>

                        <td>

                            <div class="branch-identity">

                                <div class="branch-icon">
                                    <i class="bi bi-building"></i>
                                </div>

                                <div>

                                    <div class="branch-name">
                                        ${this.escape(
                    branch.name
                )}
                                    </div>

                                    ${branch.branch_code
                        ? `
                                                <div class="branch-code">
                                                    ${this.escape(
                            branch.branch_code
                        )}
                                                </div>
                                            `
                        : ''
                    }

                                </div>

                            </div>

                        </td>

                        <td>

                            <span class="branch-organization">
                                ${this.escape(
                        branch.organization_name ||
                        '-'
                    )}
                            </span>

                        </td>

                        <td>

                            ${branch.email
                        ? `
                                        <div class="branch-contact">
                                            <i class="bi bi-envelope"></i>
                                            ${this.escape(
                            branch.email
                        )}
                                        </div>
                                    `
                        : ''
                    }

                            ${branch.phone
                        ? `
                                        <div class="branch-contact">
                                            <i class="bi bi-telephone"></i>
                                            ${this.escape(
                            branch.phone
                        )}
                                        </div>
                                    `
                        : ''
                    }

                            ${!branch.email &&
                        !branch.phone
                        ? '<span class="text-muted">-</span>'
                        : ''
                    }

                        </td>

                        <td>

                            <div class="branch-location">

                                <i class="bi bi-geo-alt"></i>

                                <span>
                                    ${this.escape(
                        location || '-'
                    )}
                                </span>

                            </div>

                        </td>

                        <td>
                            ${status}
                        </td>

                        <td class="text-center">

                            <div class="dropdown">

                                <button
                                    type="button"
                                    class="action-btn branch-action-btn"
                                    aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="${this.endpoint}/edit/${branch.id}"
                                        >
                                            <i class="bi bi-pencil me-2"></i>
                                            Edit
                                        </a>

                                    </li>

                                    <li>

                                        <a
                                            class="dropdown-item branch-view"
                                            href="#"
                                            data-id="${branch.id} "
                                        >
                                            <i class="bi bi-eye me-2"></i>
                                            View
                                        </a>

                                    </li>

                                    <li>

                                        <hr class="dropdown-divider">

                                    </li>

                                    <li>

                                        <a
                                            class="dropdown-item text-danger branch-delete"
                                            href="#"
                                            data-id="${branch.id} "
                                        >
                                            <i class="bi bi-trash me-2"></i>
                                            Delete
                                        </a>

                                    </li>

                                </ul>

                            </div>

                        </td>

                    </tr>
                `;
            }
        );

        $('#branchBody').html(html);
        this.bindDropdowns();
    },

    bindDropdowns() {

        $('#branchBody .branch-action-btn')
            .off('click.branchDropdown')
            .on('click.branchDropdown', function (e) {

                e.preventDefault();
                e.stopPropagation();

                const button = this;

                const dropdown =
                    bootstrap.Dropdown
                        .getOrCreateInstance(
                            button,
                            {
                                display: 'static'
                            }
                        );

                dropdown.toggle();
            });


        /*
         * Close after clicking an action.
         */
        $('#branchBody .dropdown-item')
            .off('click.branchDropdownAction')
            .on('click.branchDropdownAction', function () {

                const button =
                    $(this)
                        .closest('.dropdown')
                        .find('.branch-action-btn')[0];

                if (!button) {
                    return;
                }

                const dropdown =
                    bootstrap.Dropdown
                        .getInstance(button);

                if (dropdown) {
                    dropdown.hide();
                }
            });


        /*
         * Close when clicking outside.
         */
        $(document)
            .off('click.branchDropdown')
            .on('click.branchDropdown', function (e) {

                if (
                    $(e.target)
                        .closest('#branchBody .dropdown')
                        .length
                ) {
                    return;
                }

                $('#branchBody .branch-action-btn')
                    .each(function () {

                        const dropdown =
                            bootstrap.Dropdown
                                .getInstance(this);

                        if (dropdown) {
                            dropdown.hide();
                        }
                    });
            });
    },

    delete(id) {
        Swal.fire({
            title: 'Delete branch?',

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
                            'Unable to delete branch.'
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
                        xhr.responseJSON?.message ||
                        'Unable to delete branch.'
                    );
                }
            });
        });
    },

    renderPagination() {
        const $pagination =
            $('#branchPagination');

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
                    class="page-link branch-page-link"
                    data-page="${this.page - 1} "
                >
                    <i class="bi bi-chevron-left"></i>
                </a>

            </li>
        `;

        for (
            let i = 1;
            i <= this.lastPage;
            i++
        ) {
            if (
                this.lastPage > 7 &&
                i > 2 &&
                i < this.lastPage - 1 &&
                Math.abs(i - this.page) > 1
            ) {
                if (
                    i === 3 ||
                    i === this.lastPage - 2
                ) {
                    html += `
                        <li class="page-item disabled">
                            <span class="page-link">
                                ...
                            </span>
                        </li>
                    `;
                }

                continue;
            }

            html += `
                <li class="page-item ${i === this.page
                    ? 'active'
                    : ''
                }">

                    <a
                        href="#"
                        class="page-link branch-page-link"
                        data-page="${i} "
                    >
                        ${i}
                    </a>

                </li>
            `;
        }

        html += `
            <li class="page-item ${this.page >= this.lastPage
                ? 'disabled'
                : ''
            }">

                <a
                    href="#"
                    class="page-link branch-page-link"
                    data-page="${this.page + 1} "
                >
                    <i class="bi bi-chevron-right"></i>
                </a>

            </li>
        `;

        $pagination.html(html);
    },

    renderSummary() {
        if (!this.total) {
            $('#branchSummary')
                .text(
                    'Showing 0 of 0 branches'
                );

            return;
        }

        const start =
            ((this.page - 1) *
                this.pageSize) + 1;

        const end =
            Math.min(
                this.page * this.pageSize,
                this.total
            );

        $('#branchSummary')
            .text(
                `Showing ${start} -${end} of ${this.total} branches`
            );
    },

    escape(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
};

$(function () {
    BranchList.init();
});