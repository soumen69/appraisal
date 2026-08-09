<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="employee-form-page">

    <div class="employee-form-header">

        <div>

            <a
                href="<?= base_url('employees') ?>"
                class="employee-back-link">
                <i class="bi bi-arrow-left"></i>
                Employees
            </a>

            <h4 class="employee-form-title">
                Edit Employee
            </h4>

            <p class="employee-form-subtitle">
                Update employee information and system access.
            </p>

        </div>

    </div>

    <?= $this->include('employees/_form') ?>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
    window.EmployeeFormConfig = {
        mode: 'edit',
        employeeId: <?= (int) $employee['id'] ?>
    };
</script>

<script
    src="<?= base_url('assets/js/employees/employee-form.js') ?>"></script>

<?= $this->endSection() ?>