<?php

$userName = session('full_name') ?? 'Guest';

$roleName = session('primary_role') ?? 'User';

$avatar = session('avatar');

$avatar = !empty($avatar)
    ? base_url($avatar)
    : base_url('assets/images/avatar/default.png');

?>

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
                    src="<?= esc($avatar) ?>"
                    class="user-avatar" alt="<?= esc($userName) ?>">
                <div>
                    <strong><?= esc($userName) ?></strong>
                    <small><?= esc($roleName) ?></small>
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
                    <a class="dropdown-item" href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>