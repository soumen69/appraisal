<?php

use App\Services\SidebarService;

$currentRoute = service('uri')->getPath();

$sidebar = (new SidebarService())->get();

?>
<aside class="app-sidebar">
    <div class="sidebar-logo">
        <h4>APPRAISAL</h4>
    </div>

    <div class="sidebar-search px-3 py-3">

        <div class="position-relative">

            <i class="bi bi-search sidebar-search-icon"></i>

            <input
                type="text"
                class="form-control"
                id="sidebarSearch"
                placeholder="Search menu...">

        </div>

    </div>

    <ul class="sidebar-menu">

        <?php foreach ($sidebar as $section): ?>

            <?php if ($section['module'] !== 'Dashboard'): ?>

                <li class="menu-title">

                    <span><?= esc($section['module']) ?></span>

                </li>

            <?php endif; ?>

            <?php foreach ($section['menus'] as $menu):

                $active = trim($currentRoute, '/') === trim($menu['route'] ?? '', '/');

            ?>

                <li>

                    <a
                        href="<?= !empty($menu['route']) ? base_url($menu['route']) : '#' ?>"
                        title="<?= esc($menu['title']) ?>"
                        class="<?= $active ? 'active' : '' ?>">

                        <i class="<?= esc($menu['icon']) ?>"></i>

                        <span><?= esc($menu['title']) ?></span>

                    </a>

                </li>

            <?php endforeach; ?>

        <?php endforeach; ?>

    </ul>
</aside>