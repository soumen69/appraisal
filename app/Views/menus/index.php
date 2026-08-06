<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<?= view('layouts/components/crud_toolbar', ['entity' => 'Menu', 'entityPlural' => 'Menus']) ?>
<?= view('layouts/components/crud_modal', ['entity' => 'Menu', 'form' => view('menus/form')]) ?>
<?= view('layouts/components/crud_table') ?>
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

<script src="<?= base_url('assets/js/menus.js') . '?v=' . time() ?>"></script>

<?= $this->endSection() ?>