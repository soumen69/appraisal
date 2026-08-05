<?php

namespace App\Services;

use App\Models\Admin\MenuModel;
use App\Models\Admin\PermissionModel;

class MenuGeneratorService
{
    protected MenuModel $menuModel;

    protected PermissionModel $permissionModel;

    public function __construct()
    {
        $this->menuModel = new MenuModel();

        $this->permissionModel = new PermissionModel();
    }

    public function generate(int $moduleId, string $title, string $route, ?string $icon = null, int $sortOrder = 1): bool
    {
        $permission = $this->permissionModel
            ->where('module_id', $moduleId)
            ->where('slug', $route . '.view')
            ->first();

        $exists = $this->menuModel
            ->where('module_id', $moduleId)
            ->first();

        if ($exists) {
            return true;
        }

        $this->menuModel->insert([
            'module_id' => $moduleId,
            'parent_id' => null,
            'title' => $title,
            'icon' => $icon,
            'route' => $route,
            'permission_id' => $permission['id'] ?? null,
            'sort_order' => $sortOrder,
            'is_sidebar' => 1,
            'is_visible' => 1,
            'status' => 'active'
        ]);
        return true;
    }

    public function remove(int $moduleId): bool
    {
        $this->menuModel
            ->where('module_id', $moduleId)
            ->delete();
        return true;
    }
}
