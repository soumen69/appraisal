<?= $this->extend('layouts/master') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/role-permissions.css') . '?v=' . time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="permission-workspace">
    <div class="permission-toolbar">
        <div class="permission-search">
            <i class="bi bi-search"></i>
            <input type="search" id="areaSearch" class="form-control" placeholder="Search application areas..." autocomplete="off">
        </div>
        <div class="permission-search">
            <i class="bi bi-search"></i>
            <input type="search" id="permissionSearch" class="form-control" placeholder="Search permissions..." autocomplete="off">
        </div>
    </div>

    <div class="permission-layout">
        <aside class="permission-sidebar">
            <div id="areaList"></div>
        </aside>

        <section class="permission-content">
            <div id="permissionWorkspace"></div>
        </section>
    </div>

    <div class="permission-footer">
        <div>
            <span id="permissionDirty" class="text-muted">No changes</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('roles') ?>" class="btn app-btn-light">Cancel</a>
            <button type="button" id="btnSavePermissions" class="btn app-btn-primary" disabled>
                <i class="bi bi-check2"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<input type="hidden" id="roleId" value="<?= (int) $role['id'] ?>">

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.roleId = <?= (int) $role['id'] ?>;
</script>
<script src="<?= base_url('assets/js/role-permissions.js') . '?v=' . time() ?>"></script>
<?= $this->endSection() ?>