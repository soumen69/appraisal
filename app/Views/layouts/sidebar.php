<?php
$currentPath = trim(service('request')->getUri()->getPath(), '/');
$currentPath = preg_replace('#/+#', '/', $currentPath);
$currentPath = preg_replace('#(^|/)index\.php(?=/|$)#i', '', $currentPath);
$currentPath = trim($currentPath, '/');

$basePath = trim(parse_url(base_url(), PHP_URL_PATH) ?? '', '/');
if ($basePath !== '') {
    $basePath = preg_replace('#(^|/)index\.php(?=/|$)#i', '', $basePath);
    $basePath = trim($basePath, '/');
    if ($basePath !== '') {
        $currentPath = preg_replace('#^' . preg_quote($basePath, '#') . '(?:/|$)#i', '', $currentPath);
        $currentPath = trim($currentPath, '/');
    }
}

$permissions = session('permissions') ?? [];
$isSuper = (bool)session('is_super');

$can = static function (string $permission) use ($permissions, $isSuper): bool {
    return $isSuper || in_array('*', $permissions, true) || in_array($permission, $permissions, true);
};

$normalizeRoute = static function (?string $route): string {
    $route = trim((string)$route);
    if ($route === '') return '';
    $route = preg_split('/[?#]/', $route, 2)[0] ?? '';
    $route = preg_replace('#/+#', '/', $route);
    $route = preg_replace('#(^|/)index\.php(?=/|$)#i', '', $route);
    return trim($route, '/');
};

$isMenuActive = static function (?string $menuRoute, string $currentPath) use ($normalizeRoute): bool {
    $menuRoute = $normalizeRoute($menuRoute);
    $currentPath = $normalizeRoute($currentPath);
    if ($menuRoute === '' || $currentPath === '') return false;
    if ($currentPath === $menuRoute) return true;
    return str_starts_with($currentPath, $menuRoute . '/');
};

$sidebarSections = [
    [
        'title' => 'Appraisals',
        'items' => [
            ['title' => 'Cycles', 'route' => 'cycles', 'icon' => 'bi bi-arrow-repeat', 'permission' => 'appraisal_cycle.view'],
            ['title' => 'Templates', 'route' => 'templates', 'icon' => 'bi bi-file-earmark-text', 'permission' => 'appraisal_template.view'],
            ['title' => 'Review Matrix', 'route' => 'review-matrix', 'icon' => 'bi bi-diagram-3', 'permission' => 'review_matrix.view'],
            ['title' => 'Reviews', 'route' => 'reviews', 'icon' => 'bi bi-chat-square-text', 'permission' => 'appraisal_review.view'],
        ],
    ],
    [
        'title' => 'People',
        'items' => [
            ['title' => 'Organizations', 'route' => 'organizations', 'icon' => 'bi bi-building', 'permission' => 'organization.view'],
            ['title' => 'Branches', 'route' => 'branches', 'icon' => 'bi bi-diagram-2', 'permission' => 'branch.view'],
            ['title' => 'Departments', 'route' => 'departments', 'icon' => 'bi bi-diagram-3', 'permission' => 'department.view'],
            ['title' => 'Designations', 'route' => 'designations', 'icon' => 'bi bi-person-badge', 'permission' => 'designation.view'],
            ['title' => 'Employees', 'route' => 'employees', 'icon' => 'bi bi-person-vcard', 'permission' => 'employee.view'],
        ],
    ],
    [
        'title' => 'Reports',
        'items' => [
            ['title' => 'Reports', 'route' => 'reports', 'icon' => 'bi bi-file-earmark-bar-graph', 'permission' => 'report.view'],
            ['title' => 'Audit Logs', 'route' => 'audit-logs', 'icon' => 'bi bi-clock-history', 'permission' => 'audit_log.view'],
        ],
    ],
    [
        'title' => 'Administration',
        'items' => [
            ['title' => 'Roles', 'route' => 'roles', 'icon' => 'bi bi-person-gear', 'permission' => 'role.view'],
            ['title' => 'Settings', 'route' => 'settings', 'icon' => 'bi bi-gear', 'permission' => 'settings.view'],
        ],
    ],
];
?>

<aside class="app-sidebar" aria-label="Main navigation">
    <div class="sidebar-brand">
        <a href="<?= base_url('dashboard') ?>" class="sidebar-brand-link" aria-label="Appraisal Dashboard">
            <span class="sidebar-brand-mark">
                <i class="bi bi-bar-chart-line-fill"></i>
            </span>
            <span class="sidebar-brand-text">
                APPRAISAL
            </span>
        </a>
    </div>

    <div class="sidebar-search">
        <div class="sidebar-search-box">
            <i class="bi bi-search sidebar-search-icon" aria-hidden="true"></i>
            <input type="search" id="sidebarSearch" class="sidebar-search-input" placeholder="Search menu..." autocomplete="off" aria-label="Search navigation">
            <kbd class="sidebar-search-shortcut">
                /
            </kbd>
        </div>
    </div>



    <nav class="sidebar-navigation" aria-label="Application navigation">
        <ul class="sidebar-menu">
            <?php
            $dashboardActive = $currentPath === 'dashboard';
            ?>

            <li class="sidebar-item">
                <a
                    href="<?= base_url('dashboard') ?>"
                    title="Dashboard"
                    class="sidebar-link <?= $dashboardActive ? 'active' : '' ?>"
                    <?= $dashboardActive ? 'aria-current="page"' : '' ?>>
                    <span class="sidebar-link-icon">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </span>
                    <span class="sidebar-link-label">
                        Dashboard
                    </span>
                </a>
            </li>
            <!-- =================================================
                 My Reviews
                 ================================================= -->
            <li class="sidebar-item">
                <a
                    href="<?= base_url('my-reviews') ?>"
                    title="My Reviews"
                    class="sidebar-link <?= $isMenuActive('my-reviews', $currentPath) ? 'active' : '' ?>"
                    <?= $isMenuActive('my-reviews', $currentPath) ? 'aria-current="page"' : '' ?>>
                    <span class="sidebar-link-icon">
                        <i class="bi bi-person-check-fill"></i>
                    </span>
                    <span class="sidebar-link-label">
                        My Reviews
                    </span>
                </a>
            </li>

            <!-- =================================================
                 Permission Controlled Navigation
                 ================================================= -->
            <?php foreach ($sidebarSections as $section): ?>
                <?php
                $visibleItems = array_filter(
                    $section['items'],
                    static fn(array $item): bool => $can($item['permission'])
                );
                if (empty($visibleItems)) {
                    continue;
                }
                ?>
                <li class="sidebar-section">
                    <div class="sidebar-section-title">
                        <?= esc($section['title']) ?>
                    </div>
                </li>

                <?php foreach ($visibleItems as $menu): ?>
                    <?php
                    $title = trim((string)($menu['title'] ?? ''));
                    $route = $normalizeRoute($menu['route'] ?? '');
                    $isActive = $isMenuActive($route, $currentPath);
                    $href = $route !== '' ? base_url($route) : '#';
                    $icon = trim((string)($menu['icon'] ?? ''));
                    ?>

                    <li class="sidebar-item">
                        <a
                            href="<?= esc($href) ?>"
                            title="<?= esc($title) ?>"
                            class="sidebar-link <?= $isActive ? 'active' : '' ?>"
                            <?= $isActive ? 'aria-current="page"' : '' ?>
                            <?= $route === '' ? 'aria-disabled="true"' : '' ?>
                            data-sidebar-label="<?= esc(strtolower($title)) ?>">
                            <span class="sidebar-link-icon">
                                <i class="<?= esc($icon !== '' ? $icon : 'bi bi-circle') ?>"></i>
                            </span>
                            <span class="sidebar-link-label">
                                <?= esc($title) ?>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('sidebarSearch');
        if (!input) return;

        input.addEventListener('input', function() {
            const keyword = this.value.trim().toLowerCase();

            document.querySelectorAll('.sidebar-menu .sidebar-item').forEach(function(item) {
                const link = item.querySelector('.sidebar-link');
                if (!link) return;

                const label = (link.dataset.sidebarLabel || link.querySelector('.sidebar-link-label')?.textContent || '').trim().toLowerCase();

                item.style.display = !keyword || label.includes(keyword) ? '' : 'none';
            });

            document.querySelectorAll('.sidebar-menu .sidebar-section').forEach(function(section) {
                let next = section.nextElementSibling;
                let visible = false;

                while (next && !next.classList.contains('sidebar-section')) {
                    if (next.classList.contains('sidebar-item') && next.style.display !== 'none') {
                        visible = true;
                    }
                    next = next.nextElementSibling;
                }

                section.style.display = !keyword || visible ? '' : 'none';
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                input.focus();
            }
        });
    });
</script>