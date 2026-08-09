<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="employee-view-page">

    <!-- =========================================================
         Header
         ========================================================= -->

    <div class="employee-view-header">

        <div>

            <a
                href="<?= base_url('employees') ?>"
                class="employee-back-link">
                <i class="bi bi-arrow-left"></i>
                Employees
            </a>

            <div class="d-flex align-items-center gap-3">

                <div class="employee-profile-avatar">

                    <?php if (!empty($employee['profile_photo'])): ?>

                        <img
                            src="<?= base_url(
                                        'uploads/employees/' .
                                            $employee['profile_photo']
                                    ) ?>"
                            alt="<?= esc(
                                        $employee['full_name']
                                    ) ?>">

                    <?php else: ?>

                        <span>
                            <?= strtoupper(
                                substr(
                                    $employee['first_name'] ?? 'E',
                                    0,
                                    1
                                )
                            ) ?>
                        </span>

                    <?php endif; ?>

                </div>

                <div>

                    <h4 class="employee-view-title mb-1">
                        <?= esc(
                            $employee['full_name'] ??
                                trim(
                                    ($employee['first_name'] ?? '') .
                                        ' ' .
                                        ($employee['last_name'] ?? '')
                                )
                        ) ?>
                    </h4>

                    <div class="employee-view-meta">

                        <?php if (!empty($employee['employee_code'])): ?>

                            <span>
                                <i class="bi bi-hash"></i>
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
         Status
         ========================================================= -->

    <div class="employee-status-strip">

        <div class="employee-status-info">

            <span class="employee-status-label">
                Employment Status
            </span>

            <?php if (($employee['status'] ?? '') === 'active'): ?>

                <span class="status-badge status-active">
                    Active
                </span>

            <?php else: ?>

                <span class="status-badge status-inactive">
                    Inactive
                </span>

            <?php endif; ?>

        </div>

        <?php if (!empty($employee['joining_date'])): ?>

            <div class="employee-joined">

                <i class="bi bi-calendar3"></i>

                Joined
                <?= date(
                    'd M Y',
                    strtotime(
                        $employee['joining_date']
                    )
                ) ?>

            </div>

        <?php endif; ?>

    </div>

    <div class="row g-4">

        <!-- =====================================================
             Personal Information
             ===================================================== -->

        <div class="col-xl-6">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-person"></i>
                    </div>

                    <div>
                        <h6>Personal Information</h6>
                        <p>Basic employee details.</p>
                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid">

                        <div class="employee-detail">

                            <span>First Name</span>

                            <strong>
                                <?= esc(
                                    $employee['first_name'] ?? '-'
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Last Name</span>

                            <strong>
                                <?= esc(
                                    $employee['last_name'] ?? '-'
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

                                <?php if (!empty($employee['dob'])): ?>

                                    <?= date(
                                        'd M Y',
                                        strtotime(
                                            $employee['dob']
                                        )
                                    ) ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- =====================================================
             Contact Information
             ===================================================== -->

        <div class="col-xl-6">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>

                    <div>
                        <h6>Contact Information</h6>
                        <p>Employee contact details.</p>
                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid">

                        <div class="employee-detail">

                            <span>Email</span>

                            <strong>
                                <?= esc(
                                    $employee['email'] ?? '-'
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Phone</span>

                            <strong>
                                <?= esc(
                                    $employee['phone'] ?? '-'
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- =====================================================
             Organization
             ===================================================== -->

        <div class="col-xl-6">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-building"></i>
                    </div>

                    <div>
                        <h6>Organization</h6>
                        <p>Organizational placement.</p>
                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid">

                        <div class="employee-detail">

                            <span>Organization</span>

                            <strong>
                                <?= esc(
                                    $employee['organization_name']
                                        ?? '-'
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Branch</span>

                            <strong>
                                <?= esc(
                                    $employee['branch_name']
                                        ?? '-'
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Department</span>

                            <strong>
                                <?= esc(
                                    $employee['department_name']
                                        ?? '-'
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Designation</span>

                            <strong>
                                <?= esc(
                                    $employee['designation_name']
                                        ?? '-'
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- =====================================================
             Employment
             ===================================================== -->

        <div class="col-xl-6">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-briefcase"></i>
                    </div>

                    <div>
                        <h6>Employment</h6>
                        <p>Employment and reporting information.</p>
                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid">

                        <div class="employee-detail">

                            <span>Employee Code</span>

                            <strong>
                                <?= esc(
                                    $employee['employee_code']
                                        ?? '-'
                                ) ?>
                            </strong>

                        </div>

                        <div class="employee-detail">

                            <span>Joining Date</span>

                            <strong>

                                <?php if (!empty($employee['joining_date'])): ?>

                                    <?= date(
                                        'd M Y',
                                        strtotime(
                                            $employee['joining_date']
                                        )
                                    ) ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </strong>

                        </div>

                        <div class="employee-detail employee-detail-wide">

                            <span>Reporting Manager</span>

                            <strong>
                                <?= esc(
                                    $employee['reporting_manager_name'] ?? '-'
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- =====================================================
             Account
             ===================================================== -->

        <div class="col-12">

            <div class="employee-info-card">

                <div class="employee-info-card-header">

                    <div class="employee-section-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <div>
                        <h6>Account Information</h6>
                        <p>System account information.</p>
                    </div>

                </div>

                <div class="employee-info-card-body">

                    <div class="employee-detail-grid employee-account-grid">

                        <div class="employee-detail">

                            <span>Account Status</span>

                            <?php if (
                                ($employee['status'] ?? '') === 'active'
                            ): ?>

                                <strong class="text-success">
                                    Active
                                </strong>

                            <?php else: ?>

                                <strong class="text-danger">
                                    Inactive
                                </strong>

                            <?php endif; ?>

                        </div>

                        <div class="employee-detail">

                            <span>Last Login</span>

                            <strong>

                                <?php if (!empty($employee['last_login'])): ?>

                                    <?= date(
                                        'd M Y, h:i A',
                                        strtotime(
                                            $employee['last_login']
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

                                <?php if (!empty($employee['created_at'])): ?>

                                    <?= date(
                                        'd M Y, h:i A',
                                        strtotime(
                                            $employee['created_at']
                                        )
                                    ) ?>

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="employee-view-footer">

        <a
            href="<?= base_url('employees') ?>"
            class="btn branch-cancel-btn">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Employees
        </a>

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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    window.EmployeeViewConfig = {
        employeeId: <?= (int) $employee['id'] ?>
    };
</script>

<?= $this->endSection() ?>