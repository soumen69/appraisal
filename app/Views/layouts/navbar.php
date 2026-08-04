<nav class="app-navbar">
    <div class="app-navbar-left">
        <button class="btn-toggle-sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="app-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search..." class="form-control">
        </div>
    </div>
    <div class="app-navbar-right">
        <button class="icon-btn">
            <i class="bi bi-bell"></i>
            <span class="badge-dot"></span>
        </button>
        <div class="dropdown">
            <button
                class="user-dropdown"
                data-bs-toggle="dropdown">
                <img
                    src="<?= base_url('assets/images/avatar/default.png') ?>"
                    class="user-avatar">
                <div>
                    <strong>Super Admin</strong>
                    <small>Administrator</small>
                </div>
                <i class="bi bi-chevron-down"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-person"></i>
                        Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>