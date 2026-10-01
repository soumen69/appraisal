<?php

use App\Services\SidebarService;

/*
|--------------------------------------------------------------------------
| Resolve current route
|--------------------------------------------------------------------------
*/

$currentPath = trim(
    service('request')
        ->getUri()
        ->getPath(),
    '/'
);

$currentPath = preg_replace(
    '#/+#',
    '/',
    $currentPath
);

$currentPath = preg_replace(
    '#(^|/)index\.php(?=/|$)#i',
    '',
    $currentPath
);

$currentPath = trim(
    $currentPath,
    '/'
);

/*
|--------------------------------------------------------------------------
| Remove configured application base path
|--------------------------------------------------------------------------
*/

$basePath = trim(
    parse_url(
        base_url(),
        PHP_URL_PATH
    ) ?? '',
    '/'
);

if ($basePath !== '') {

    $basePath = preg_replace(
        '#(^|/)index\.php(?=/|$)#i',
        '',
        $basePath
    );

    $basePath = trim(
        $basePath,
        '/'
    );

    if ($basePath !== '') {

        $currentPath = preg_replace(
            '#^' .
                preg_quote($basePath, '#') .
                '(?:/|$)#i',
            '',
            $currentPath
        );

        $currentPath = trim(
            $currentPath,
            '/'
        );
    }
}

$sidebar = (new SidebarService())->get();

/*
|--------------------------------------------------------------------------
| Route helpers
|--------------------------------------------------------------------------
*/

$normalizeRoute = static function (?string $route): string {

    $route = trim(
        (string) $route
    );

    if ($route === '') {
        return '';
    }

    $route = preg_split(
        '/[?#]/',
        $route,
        2
    )[0] ?? '';

    $route = preg_replace(
        '#/+#',
        '/',
        $route
    );

    $route = preg_replace(
        '#(^|/)index\.php(?=/|$)#i',
        '',
        $route
    );

    return trim(
        $route,
        '/'
    );
};

$isMenuActive = static function (
    ?string $menuRoute,
    string $currentPath
) use ($normalizeRoute): bool {

    $menuRoute = $normalizeRoute(
        $menuRoute
    );

    $currentPath = $normalizeRoute(
        $currentPath
    );

    if (
        $menuRoute === '' ||
        $currentPath === ''
    ) {
        return false;
    }

    if ($currentPath === $menuRoute) {
        return true;
    }

    return str_starts_with(
        $currentPath,
        $menuRoute . '/'
    );
};

?>

<aside
    class="app-sidebar"
    aria-label="Main navigation">

    <!-- =========================================================
         Brand
         ========================================================= -->
    <div class="sidebar-brand">

        <a
            href="<?= base_url('dashboard') ?>"
            class="sidebar-brand-link"
            aria-label="Appraisal Dashboard">

            <span class="sidebar-brand-mark">
                <i class="bi bi-bar-chart-line-fill"></i>
            </span>

            <span class="sidebar-brand-text">
                APPRAISAL
            </span>

        </a>

    </div>


    <!-- =========================================================
         Search
         ========================================================= -->
    <div class="sidebar-search">

        <div class="sidebar-search-box">

            <i
                class="bi bi-search sidebar-search-icon"
                aria-hidden="true"></i>

            <input
                type="search"
                id="sidebarSearch"
                class="sidebar-search-input"
                placeholder="Search menu..."
                autocomplete="off"
                aria-label="Search navigation">

            <kbd class="sidebar-search-shortcut">
                /
            </kbd>

        </div>

    </div>


    <!-- =========================================================
         Navigation
         ========================================================= -->
    <nav
        class="sidebar-navigation"
        aria-label="Application navigation">

        <ul class="sidebar-menu">


            <!-- =================================================
                 Dashboard
                 ================================================= -->
            <?php
            $dashboardActive =
                $currentPath === 'dashboard';
            ?>

            <li class="sidebar-item">

                <a
                    href="<?= base_url('dashboard') ?>"
                    title="Dashboard"
                    class="sidebar-link <?= $dashboardActive
                                            ? 'active'
                                            : ''
                                        ?>"
                    <?= $dashboardActive
                        ? 'aria-current="page"'
                        : ''
                    ?>>

                    <span class="sidebar-link-icon">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </span>

                    <span class="sidebar-link-label">
                        Dashboard
                    </span>

                </a>

            </li>


            <!-- =================================================
                 Permission Controlled Navigation
                 ================================================= -->
            <?php foreach ($sidebar as $section): ?>

                <?php
                $sectionMenus =
                    $section['menus'] ?? [];

                if (empty($sectionMenus)) {
                    continue;
                }
                ?>

                <li class="sidebar-section">

                    <div class="sidebar-section-title">
                        <?= esc(
                            $section['module'] ?? ''
                        ) ?>
                    </div>

                </li>


                <?php foreach ($sectionMenus as $menu): ?>

                    <?php

                    $title = trim(
                        (string) (
                            $menu['title'] ?? ''
                        )
                    );

                    $route = $normalizeRoute(
                        $menu['route'] ?? ''
                    );

                    $isActive = $isMenuActive(
                        $route,
                        $currentPath
                    );

                    $href = $route !== ''
                        ? base_url($route)
                        : '#';

                    $icon = trim(
                        (string) (
                            $menu['icon'] ?? ''
                        )
                    );

                    ?>

                    <li class="sidebar-item">

                        <a
                            href="<?= esc($href) ?>"
                            title="<?= esc($title) ?>"
                            class="sidebar-link <?= $isActive
                                                    ? 'active'
                                                    : ''
                                                ?>"
                            <?= $isActive
                                ? 'aria-current="page"'
                                : ''
                            ?>
                            <?= $route === ''
                                ? 'aria-disabled="true"'
                                : ''
                            ?>>

                            <span class="sidebar-link-icon">

                                <i class="<?= esc(
                                                $icon !== ''
                                                    ? $icon
                                                    : 'bi bi-circle'
                                            ) ?>"></i>

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