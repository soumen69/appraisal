<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="employee-form-page">
    <!-- Header -->
    <div class="employee-form-header">
        <div>
            <a href="<?= base_url('employees') ?>"
                class="employee-back-link">
                <i class="bi bi-arrow-left"></i>
                Employees
            </a>
        </div>
    </div>

    <?= $this->include('employees/_form') ?>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/employees/employee-form.js') ?>"></script>

<script>
    window.EmployeeFormConfig = {
        mode: 'create',
        employee: null
    };
</script>

<?= $this->endSection() ?>