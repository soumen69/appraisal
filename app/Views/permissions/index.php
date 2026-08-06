<?= $this->extend('layouts/master') ?>

<?= $this->section('styles') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/permissions.css') . '?v=' . time() ?>">

<?= $this->endSection() ?>


<?= $this->section('content') ?>

<div id="permissionRegistry"></div>

<?= view('layouts/components/crud_modal', [
    'entity'     => 'Permission',
    'modalTitle' => 'Custom Permission',
    'form'       => view('permissions/form')
]) ?>

<?= view('layouts/components/crud_drawer') ?>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.drawer.js') ?>"></script>

<script src="<?= base_url('assets/js/permissions.js') . '?v=' . time() ?>"></script>

<?= $this->endSection() ?>