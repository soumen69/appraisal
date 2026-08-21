<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/employee-view.css') ?>">
<?= $this->endSection() ?>

<?php
$fullName = trim(
    $employee['full_name']
        ?? trim(
            ($employee['first_name'] ?? '') . ' ' .
                ($employee['last_name'] ?? '')
        )
);

$fullName = $fullName ?: 'Employee';

$initials = '';

$nameParts = preg_split(
    '/\s+/',
    trim($fullName)
);

if (!empty($nameParts)) {

    $initials =
        strtoupper(
            substr($nameParts[0], 0, 1) .
                (
                    count($nameParts) > 1
                    ? substr(
                        $nameParts[count($nameParts) - 1],
                        0,
                        1
                    )
                    : ''
                )
        );
}

$employeePhoto = $employee['profile_photo'] ?? '';

if (
    $employeePhoto &&
    !str_starts_with($employeePhoto, 'http://') &&
    !str_starts_with($employeePhoto, 'https://') &&
    !str_starts_with($employeePhoto, '/')
) {
    $employeePhoto =
        base_url(
            'uploads/employees/' .
                ltrim($employeePhoto, '/')
        );
}

$isActive =
    ($employee['status'] ?? '') === 'active';

$formatDate = static function ($value, $format = 'd M Y') {
    if (empty($value)) {
        return '-';
    }

    $timestamp = strtotime($value);

    return $timestamp
        ? date($format, $timestamp)
        : '-';
};

$value = static function ($value) {
    return !empty($value) ? $value : '-';
};
?>

<div class="employee-view-page">

    <!-- =========================================================
         Header / Profile Hero
         ========================================================= -->

    <div class="employee-view-header">

        <div class="employee-view-header-main">

            <a
                href="<?= base_url('employees') ?>"
                class="employee-back-link">

                <i class="bi bi-arrow-left"></i>
                Employees

            </a>

            <div class="employee-profile">

                <div class="employee-profile-avatar">

                    <?php if ($employeePhoto): ?>

                        <img
                            src="<?= esc($employeePhoto) ?>"
                            alt="<?= esc($fullName) ?>">

                    <?php else: ?>

                        <span>
                            <?= esc($initials ?: 'E') ?>
                        </span>

                    <?php endif; ?>

                </div>

                <div class="employee-profile-content">

                    <div class="employee-profile-name-row">

                        <h4 class="employee-view-title">
                            <?= esc($fullName) ?>
                        </h4>

                        <span
                            class="status-badge <?= $isActive
                                                    ? 'status-active'
                                                    : 'status-inactive' ?>">

                            <?= $isActive
                                ? 'Active'
                                : 'Inactive' ?>

                        </span>

                    </div>

                    <div class="employee-profile-meta">

                        <?php if (!empty($employee['employee_code'])): ?>

                            <span>
                                <i class="bi bi-person-badge"></i>
                                <?= esc(
                                    $employee['employee_code']
                                ) ?>
                            </span>

                        <?php endif; ?>

                        <?php if (!empty($employee['designation_name'])): ?>

                            <span>
                                <i class="bi bi-briefcase"></i>
                                <?= esc(
                                    $employee['designation_name']
                                ) ?>
                            </span>

                        <?php endif; ?>

                        <?php if (!empty($employee['organization_name'])): ?>

                            <span>
                                <i class="bi bi-building"></i>
                                <?= esc(
                                    $employee['organization_name']
                                ) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="employee-view-actions">

            <a
                href="<?= base_url(
                            'employees/edit/' .
                                (int) $employee['id']
                        ) ?>"
                class="btn app-btn-primary">

                <i class="bi bi-pencil me-1"></i>
                Edit Employee

            </a>

        </div>

    </div>


    <!-- =========================================================
         Quick Summary
         ========================================================= -->

    <div class="employee-summary-grid">

        <div class="employee-summary-item">

            <div class="employee-summary-icon">
                <i class="bi bi-building"></i>
            </div>

            <div>

                <span class="employee-summary-label">
                    Organization
                </span>

                <strong>
                    <?= esc(
                        $value(
                            $employee['organization_name'] ?? null
                        )
                    ) ?>
                </strong>

            </div>

        </div>


        <div class="employee-summary-item">

            <div class="employee-summary-icon">
                <i class="bi bi-diagram-3"></i>
            </div>

            <div>

                <span class="employee-summary-label">
                    Department
                </span>

                <strong>
                    <?= esc(
                        $value(
                            $employee['department_name'] ?? null
                        )
                    ) ?>
                </strong>

            </div>

        </div>


        <div class="employee-summary-item">

            <div class="employee-summary-icon">
                <i class="bi bi-calendar3"></i>
            </div>

            <div>

                <span class="employee-summary-label">
                    Joining Date
                </span>

                <strong>
                    <?= esc(
                        $formatDate(
                            $employee['joining_date'] ?? null
                        )
                    ) ?>
                </strong>

            </div>

        </div>


        <div class="employee-summary-item">

            <div class="employee-summary-icon">
                <i class="bi bi-person-check"></i>
            </div>

            <div>

                <span class="employee-summary-label">
                    Reporting Manager
                </span>

                <strong>
                    <?= esc(
                        $value(
                            $employee['reporting_manager_name']
                                ?? null
                        )
                    ) ?>
                </strong>

            </div>

        </div>

    </div>


    <!-- =========================================================
         Details
         ========================================================= -->

    <div class="row g-4">

        <!-- Personal -->

        <div class="col-xl-6">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-person"></i>
                    </div>

                    <div>

                        <h6>Personal Information</h6>

                        <p>
                            Basic employee details.
                        </p>

                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid">

                        <div class="employee-detail">

                            <span>First Name</span>

                            <strong>
                                <?= esc(
                                    $value(
                                        $employee['first_name']
                                            ?? null
                                    )
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Last Name</span>

                            <strong>
                                <?= esc(
                                    $value(
                                        $employee['last_name']
                                            ?? null
                                    )
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Gender</span>

                            <strong>
                                <?= !empty($employee['gender'])
                                    ? esc(
                                        ucfirst(
                                            $employee['gender']
                                        )
                                    )
                                    : '-'
                                ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Date of Birth</span>

                            <strong>
                                <?= esc(
                                    $formatDate(
                                        $employee['dob'] ?? null
                                    )
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Contact -->

        <div class="col-xl-6">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>

                    <div>

                        <h6>Contact Information</h6>

                        <p>
                            Employee contact details.
                        </p>

                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid">

                        <div class="employee-detail">

                            <span>Email</span>

                            <strong>
                                <?= esc(
                                    $value(
                                        $employee['email']
                                            ?? null
                                    )
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Phone</span>

                            <strong>
                                <?= esc(
                                    $value(
                                        $employee['phone']
                                            ?? null
                                    )
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Employment -->

        <div class="col-xl-6">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-briefcase"></i>
                    </div>

                    <div>

                        <h6>Employment</h6>

                        <p>
                            Employee placement and reporting.
                        </p>

                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid">

                        <div class="employee-detail">

                            <span>Employee Code</span>

                            <strong>
                                <?= esc(
                                    $value(
                                        $employee['employee_code']
                                            ?? null
                                    )
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Joining Date</span>

                            <strong>
                                <?= esc(
                                    $formatDate(
                                        $employee['joining_date']
                                            ?? null
                                    )
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail employee-detail-wide">

                            <span>Reporting Manager</span>

                            <strong>
                                <?= esc(
                                    $value(
                                        $employee['reporting_manager_name']
                                            ?? null
                                    )
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Account -->

        <div class="col-xl-6">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <div>

                        <h6>Account</h6>

                        <p>
                            System account activity.
                        </p>

                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid">

                        <div class="employee-detail">

                            <span>Status</span>

                            <strong class="<?= $isActive
                                                ? 'text-success'
                                                : 'text-danger' ?>">

                                <?= $isActive
                                    ? 'Active'
                                    : 'Inactive' ?>

                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Last Login</span>

                            <strong>

                                <?php if (
                                    !empty($employee['last_login'])
                                ): ?>

                                    <?= esc(
                                        $formatDate(
                                            $employee['last_login'],
                                            'd M Y, h:i A'
                                        )
                                    ) ?>

                                <?php else: ?>

                                    Never

                                <?php endif; ?>

                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Created</span>

                            <strong>
                                <?= esc(
                                    $formatDate(
                                        $employee['created_at']
                                            ?? null,
                                        'd M Y, h:i A'
                                    )
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         Footer Navigation
         ========================================================= -->

    <div class="employee-view-footer">

        <a
            href="<?= base_url('employees') ?>"
            class="btn branch-cancel-btn">

            <i class="bi bi-arrow-left me-1"></i>
            Back to Employees

        </a>

    </div>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
    window.EmployeeViewConfig = {
        employeeId: <?= (int) $employee['id'] ?>
    };
</script>

<?= $this->endSection() ?>