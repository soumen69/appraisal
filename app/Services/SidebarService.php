<?php

namespace App\Services;

class SidebarService
{
    protected MenuService $menus;

    public function __construct()
    {
        $this->menus = new MenuService();
    }

    public function get(): array
    {
        $rows = $this->menus->getSidebarMenus();

        $isSuper = (bool) session('is_super');

        $permissions = session('permissions') ?? [];

        $sidebar = [];

        foreach ($rows as $row) {

            if (
                !$isSuper &&
                !empty($row['permission_slug']) &&
                !in_array($row['permission_slug'], $permissions, true)
            ) {
                continue;
            }

            $module = $row['module_name'];

            if (!isset($sidebar[$module])) {

                $sidebar[$module] = [
                    'module' => $module,
                    'menus'  => []
                ];
            }

            $sidebar[$module]['menus'][] = $row;
        }

        return array_values($sidebar);
    }
}
