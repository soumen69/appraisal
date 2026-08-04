<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="row g-4">
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div>
                <span>Total Employees</span>
                <h2>248</h2>
                <small class="text-success">
                    +12 This Month
                </small>
            </div>
            <div class="dashboard-icon bg-primary">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div>
                <span>Pending Reviews</span>
                <h2>41</h2>
                <small class="text-warning">
                    Needs Attention
                </small>
            </div>
            <div class="dashboard-icon bg-warning">
                <i class="bi bi-clipboard-check"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div>
                <span>Completed</span>
                <h2>207</h2>
                <small class="text-success">
                    84% Completed
                </small>
            </div>
            <div class="dashboard-icon bg-success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="dashboard-card">
            <div>
                <span>Branches</span>
                <h2>12</h2>
                <small class="text-info">
                    Across India
                </small>
            </div>
            <div class="dashboard-icon bg-info">
                <i class="bi bi-building"></i>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <strong>Appraisal Completion</strong>
            </div>
            <div class="card-body">
                <div class="dashboard-chart-wrapper">
                    <canvas id="dashboardChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <strong>Quick Actions</strong>
            </div>

            <div class="card-body">
                <div class="d-grid gap-3">
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        Create Cycle
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-ui-checks"></i>
                        Templates
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-people"></i>
                        Employees
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        Reports
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Pending Reviews</strong>
                <a href="#" class="btn btn-sm btn-primary">
                    View All
                </a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Rahul Sharma</td>
                            <td>Development</td>
                            <td>
                                <span class="badge bg-warning">
                                    Pending
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    Review
                                </button>
                            </td>
                        </tr>

                        <tr>
                            <td>Amit Roy</td>
                            <td>HR</td>
                            <td>
                                <span class="badge bg-warning">
                                    Pending
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary">
                                    Review
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <strong>Recent Activity</strong>
            </div>

            <div class="card-body">
                <ul class="activity-list">

                    <li>
                        <span class="dot bg-success"></span>
                        HR submitted Rahul's appraisal.
                    </li>

                    <li>
                        <span class="dot bg-primary"></span>
                        New appraisal cycle created.
                    </li>

                    <li>
                        <span class="dot bg-warning"></span>
                        15 employees pending self review.
                    </li>

                    <li>
                        <span class="dot bg-danger"></span>
                        Review deadline in 2 days.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>