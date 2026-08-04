<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="page-header mb-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap">

        <div>

            <h3 class="page-title mb-1">

                Modules

            </h3>

            <p class="text-muted mb-0">

                Manage application modules and access foundation.

            </p>

        </div>

        <nav>

            <ol class="breadcrumb mb-0">

                <li class="breadcrumb-item">

                    Dashboard

                </li>

                <li class="breadcrumb-item active">

                    Modules

                </li>

            </ol>

        </nav>

    </div>

</div>

<?php echo view('layouts/components/crud_toolbar'); ?>

<?php echo view('layouts/components/crud_table'); ?>

<?= view('layouts/components/crud_modal',['form'=>view('modules/form')]) ?>

<?= $this->endSection(); ?>


<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>

<script src="<?= base_url('assets/js/crud/crud.api.js') ?>"></script>

<script src="<?= base_url('assets/js/crud/crud.table.js') ?>"></script>

<script src="<?= base_url('assets/js/crud/crud.pagination.js') ?>"></script>

<script src="<?= base_url('assets/js/crud/crud.search.js') ?>"></script>

<script src="<?= base_url('assets/js/crud/crud.modal.js') ?>"></script>

<script src="<?= base_url('assets/js/crud/crud.form.js') ?>"></script>

<script src="<?= base_url('assets/js/crud/crud.js') ?>"></script>

<script src="<?= base_url('assets/js/modules.js') ?>"></script>

<?= $this->endSection(); ?>