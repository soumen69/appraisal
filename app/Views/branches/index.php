<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="branch-page">

    <div class="page-header mb-4">

        <div>
            <h4 class="page-title mb-1">
                Branches
            </h4>

            <p class="page-subtitle mb-0">
                Manage organization branches and office locations.
            </p>
        </div>

        <div class="page-header-actions">

            <a
                href="<?= base_url('branches/create') ?>"
                class="btn app-btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Add Branch
            </a>

        </div>

    </div>


    <div class="card crud-card">

        <div class="card-body">

            <div class="branch-toolbar">

                <div class="branch-toolbar-left">

                    <div class="crud-search branch-search">

                        <i class="bi bi-search"></i>

                        <input
                            type="search"
                            id="branchSearch"
                            class="form-control"
                            placeholder="Search branches..."
                            autocomplete="off">

                    </div>


                    <select
                        id="branchOrganization"
                        class="form-select crud-filter branch-organization-filter">

                        <option value="">
                            All Organizations
                        </option>

                        <?php foreach ($organizations as $organization): ?>

                            <option
                                value="<?= (int) $organization['id'] ?>">
                                <?= esc($organization['name']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>


                    <select
                        id="branchStatus"
                        class="form-select crud-filter branch-status-filter">

                        <option value="">
                            All Status
                        </option>

                        <option value="active">
                            Active
                        </option>

                        <option value="inactive">
                            Inactive
                        </option>

                    </select>

                </div>


                <div class="branch-toolbar-right">

                    <button
                        type="button"
                        class="btn branch-refresh-btn"
                        id="branchRefresh"
                        title="Refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>

                    <select
                        id="branchPageSize"
                        class="form-select branch-page-size">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>

                </div>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table app-table branch-table mb-0">

                <thead id="branchHeader">
                    <tr>

                        <th width="40">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="branchCheckAll">
                        </th>

                        <th>Branch</th>

                        <th>Organization</th>

                        <th>Contact</th>

                        <th>Location</th>

                        <th>Status</th>

                        <th width="80">
                            Action
                        </th>

                    </tr>
                </thead>

                <tbody id="branchBody">

                    <tr>
                        <td colspan="7">

                            <div class="branch-loading">

                                <span
                                    class="spinner-border spinner-border-sm"></span>

                                Loading branches...

                            </div>

                        </td>
                    </tr>

                </tbody>

            </table>

        </div>


        <div class="crud-pagination">

            <div
                id="branchSummary"
                class="crud-summary">
                Showing 0 of 0 branches
            </div>

            <ul
                class="pagination mb-0"
                id="branchPagination"></ul>

        </div>

    </div>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
    window.BranchConfig = {
        endpoint: "<?= base_url('branches') ?>"
    };
</script>

<script
    src="<?= base_url('assets/js/branches/branch-list.js') ?>"></script>

<?= $this->endSection() ?>