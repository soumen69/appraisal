<aside class="app-sidebar">

    <div class="sidebar-logo">
        <h4>APPRAISAL</h4>
    </div>
    <div class="sidebar-search px-3 py-3">

        <div class="position-relative">

            <i class="bi bi-search sidebar-search-icon"></i>

            <input type="text" class="form-control" id="sidebarSearch" placeholder="Search menu...">

        </div>

    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?= base_url('dashboard') ?>" title="Dashboard">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="menu-title">
            <span>Organization</span>
        </li>
        <li><a href="#"><i class="bi bi-building"></i> <span>Organizations</span></a></li>
        <li><a href="#"><i class="bi bi-diagram-3"></i> <span>Branches</span></a></li>
        <li><a href="#"><i class="bi bi-diagram-2"></i> <span>Departments</span></a></li>
        <li><a href="#"><i class="bi bi-award"></i> <span>Designations</span></a></li>
        <li class="menu-title">
            <span>User Management</span>
        </li>
        <li><a href="#"><i class="bi bi-people"></i> <span>Employees</span></a></li>
        <li><a href="#"><i class="bi bi-person-badge"></i> <span>Roles</span></a></li>
        <li><a href="#"><i class="bi bi-shield-lock"></i> <span>Permissions</span></a></li>
        <li class="menu-title">
            <span>Appraisal</span>
        </li>
        <li><a href="#"><i class="bi bi-calendar-event"></i> <span>Cycles</span></a></li>
        <li><a href="#"><i class="bi bi-ui-checks"></i> <span>Templates</span></a></li>
        <li><a href="#"><i class="bi bi-list-task"></i> <span>Questions</span></a></li>
        <li><a href="#"><i class="bi bi-diagram-3-fill"></i> <span>Review Matrix</span></a></li>
        <li><a href="#"><i class="bi bi-pencil-square"></i> <span>Reviews</span></a></li>
        <li class="menu-title">
            <span>Reports</span>
        </li>
        <li><a href="#"><i class="bi bi-bar-chart"></i> <span>Reports</span></a></li>
        <li><a href="#"><i class="bi bi-clock-history"></i> <span>Audit Logs</span></a></li>
        <li><a href="#"><i class="bi bi-gear"></i> <span>Settings</span></a></li>

        <li>
            <a href="<?= base_url('modules') ?>" title="Modules">
                <i class="bi bi-box"></i>
                <span>Modules</span>
            </a>
        </li>
    </ul>
</aside>