<?= $this->extend('layouts/master') ?>

<?= $this->section('styles') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/role-permissions.css') . '?v=' . time() ?>">

<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="permission-workspace">

    <!-- <div class="permission-header">

        <div>

            <a
                href="<?= base_url('roles') ?>"
                class="permission-back">
                <i class="bi bi-arrow-left"></i>
                Back to Roles
            </a>

            <h2 class="permission-role-name">
                <?= esc(
                    $role['display_name']
                        ?: $role['name']
                ) ?>
            </h2>

            <?php if (!empty($role['description'])): ?>

                <p class="permission-role-description">
                    <?= esc($role['description']) ?>
                </p>

            <?php else: ?>

                <p class="permission-role-description">
                    Configure what this role can access and perform.
                </p>

            <?php endif; ?>

        </div>

        <div class="permission-header-actions">

            <span
                class="status-badge
                    <?= ($role['status'] ?? '') === 'active'
                        ? 'status-active'
                        : 'status-inactive' ?>">

                <?= ucfirst(
                    $role['status'] ?? 'inactive'
                ) ?>

            </span>

        </div>

    </div> -->


    <div class="permission-toolbar">

        <div class="permission-search">

            <i class="bi bi-search"></i>

            <input
                type="search"
                id="moduleSearch"
                class="form-control"
                placeholder="Search modules..."
                autocomplete="off">

        </div>

        <div class="permission-search">

            <i class="bi bi-search"></i>

            <input
                type="search"
                id="permissionSearch"
                class="form-control"
                placeholder="Search permissions..."
                autocomplete="off">

        </div>

    </div>


    <div class="permission-layout">

        <aside class="permission-sidebar">

            <div id="moduleList"></div>

        </aside>


        <section class="permission-content">

            <div id="permissionWorkspace"></div>

        </section>

    </div>


    <div class="permission-footer">

        <div>

            <span
                id="permissionDirty"
                class="text-muted">
                No changes
            </span>

        </div>


        <div class="d-flex gap-2">

            <a
                href="<?= base_url('roles') ?>"
                class="btn app-btn-light">

                Cancel

            </a>

            <button
                type="button"
                id="btnSavePermissions"
                class="btn app-btn-primary"
                disabled>

                <i class="bi bi-check2"></i>

                Save Changes

            </button>

        </div>

    </div>

</div>


<input
    type="hidden"
    id="roleId"
    value="<?= (int) $role['id'] ?>">

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    window.roleId = <?= (int)$role['id'] ?>;
</script>

<script src="<?= base_url('assets/js/role-permissions.js') . '?v=' . time() ?>"></script>

<?= $this->endSection() ?>