<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="employee-page">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <h4 class="page-title mb-1">
                Employees
            </h4>

            <p class="page-subtitle mb-0">
                Manage employee records, organization details and access.
            </p>
        </div>

        <div class="page-header-actions">
            <a
                href="<?= base_url('employees/create') ?>"
                class="btn app-btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Add Employee
            </a>
        </div>
    </div>


    <!-- Employee Card -->
    <div class="card employee-card">

        <!-- Toolbar -->
        <div class="employee-toolbar">

            <div class="employee-toolbar-left">

                <!-- Search -->
                <div class="employee-search">
                    <i class="bi bi-search"></i>

                    <input
                        type="search"
                        id="employeeSearch"
                        class="form-control"
                        placeholder="Search employees..."
                        autocomplete="off">
                </div>

                <!-- Status -->
                <select
                    id="employeeStatus"
                    class="form-select employee-filter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

            </div>


            <div class="employee-toolbar-right">

                <button
                    type="button"
                    class="btn employee-refresh-btn"
                    id="employeeRefresh"
                    title="Refresh">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>

                <select
                    id="employeePageSize"
                    class="form-select employee-page-size"
                    title="Rows per page">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>

            </div>

        </div>


        <!-- Table -->
        <div class="table-responsive">

            <table class="table employee-table mb-0">

                <thead>
                    <tr>

                        <th class="employee-check-col">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="employeeCheckAll">
                        </th>

                        <th>
                            Employee
                        </th>

                        <th>
                            Organization
                        </th>

                        <th>
                            Department
                        </th>

                        <th>
                            Designation
                        </th>

                        <th>
                            Reporting Manager
                        </th>

                        <th>
                            Joining Date
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="employee-action-col">
                            Action
                        </th>

                    </tr>
                </thead>

                <tbody id="employeeTableBody">

                    <tr>
                        <td colspan="9">

                            <div class="employee-table-loading">

                                <div
                                    class="spinner-border spinner-border-sm"
                                    role="status"></div>

                                <span>
                                    Loading employees...
                                </span>

                            </div>

                        </td>
                    </tr>

                </tbody>

            </table>

        </div>


        <!-- Footer -->
        <div class="employee-table-footer">

            <div
                class="employee-summary"
                id="employeeSummary">
                Showing 0 of 0 employees
            </div>

            <nav aria-label="Employee pagination">

                <ul
                    class="pagination employee-pagination mb-0"
                    id="employeePagination"></ul>

            </nav>

        </div>

    </div>

    <!-- Employee Details Drawer -->
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="crudDrawer"
        aria-labelledby="crudDrawerTitle"
        style="width: 480px;">
        <div class="offcanvas-header border-bottom">

            <div>
                <h5
                    class="offcanvas-title mb-1"
                    id="crudDrawerTitle">
                    Employee Details
                </h5>

                <small class="text-muted">
                    Employee information
                </small>
            </div>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Close">
            </button>

        </div>

        <div
            class="offcanvas-body"
            id="crudDrawerBody">
        </div>
    </div>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/crud/crud.drawer.js') ?>"></script>
<script src="<?= base_url('assets/js/employees/employee-drawer.js') ?>"></script>
<script src="<?= base_url('assets/js/employees/employee-list.js') ?>"></script>

<?= $this->endSection() ?>