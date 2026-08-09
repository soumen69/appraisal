<?= $this->extend('layouts/master') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/permissions.css') . '?v=' . time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="crud-card">
    <div class="card-body">
        <div id="permissionRegistry"></div>
    </div>
</div>

<?= view('layouts/components/crud_modal', [
    'entity'     => 'Permission',
    'modalTitle' => 'Custom Permission',
    'form'       => view('permissions/form')
]) ?>
<?= view('layouts/components/crud_drawer') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.api.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.table.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.pagination.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.search.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.modal.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.form.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.delete.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.drawer.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.view.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.js') . '?v=' . time() ?>"></script>
<script src="<?= base_url('assets/js/permissions.js') . '?v=' . time() ?>"></script>
<?= $this->endSection() ?>