<?php

namespace App\Services;

use App\Models\Admin\MenuModel;
use App\Models\Admin\PermissionModel;

class MenuGeneratorService
{
    protected MenuModel $menuModel;
    protected PermissionModel $permissionModel;
    protected PermissionGeneratorService $permissionGenerator;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
        $this->permissionModel = new PermissionModel();
        $this->permissionGenerator = new PermissionGeneratorService();
    }

    /**
     * Generate a default menu for a module.
     */
    public function generate(
        int $moduleId,
        string $title,
        string $route,
        ?string $icon = null,
        int $sortOrder = 1,
        array $actions = []
    ): int {
        $existing = $this->menuModel
            ->where('module_id', $moduleId)
            ->first();

        if ($existing) {
            return (int) $existing['id'];
        }

        $resource = $this->resolveResource(
            $title,
            $route
        );

        $menuId = (int) $this->menuModel->insert([
            'module_id'     => $moduleId,
            'parent_id'     => null,
            'type'          => 'menu',
            'title'         => $title,
            'icon'          => $icon,
            'route'         => $route ?: null,
            'permission_id' => null,
            'is_system'     => 1,
            'sort_order'    => $sortOrder,
            'is_sidebar'    => 1,
            'is_visible'    => 1,
            'status'        => 'active'
        ], true);

        /**
         * Generate permissions for this menu resource.
         */
        $permissions = $this->permissionGenerator
            ->generateForResource(
                $moduleId,
                $resource,
                $title,
                $actions
            );

        /**
         * Sidebar uses the VIEW permission.
         */
        if (isset($permissions['view'])) {

            $this->menuModel->update(
                $menuId,
                [
                    'permission_id' => $permissions['view']
                ]
            );
        }

        return $menuId;
    }

    /**
     * Synchronize permissions for an existing menu.
     *
     * Existing permissions are intentionally NOT deleted.
     * This prevents accidentally breaking role assignments.
     */
    public function syncPermissions(
        int $menuId,
        array $actions = []
    ): array {
        $menu = $this->menuModel->find($menuId);

        if (!$menu) {
            throw new \RuntimeException(
                'Menu not found.'
            );
        }

        $resource = $this->resolveResource(
            $menu['title'],
            $menu['route'] ?? ''
        );

        $permissions = $this->permissionGenerator
            ->generateForResource(
                (int) $menu['module_id'],
                $resource,
                $menu['title'],
                $actions
            );

        if (isset($permissions['view'])) {
            $this->menuModel->update(
                $menuId,
                [
                    'permission_id' =>
                    $permissions['view']
                ]
            );
        }

        return $permissions;
    }

    /**
     * Resolve the permission resource from the menu.
     *
     * Examples:
     *
     * Employees  + employees   => employee
     * Roles      + roles       => role
     * Permissions + permissions => permission
     */
    public function resolveResource(
        string $title,
        ?string $route = null
    ): string {
        $route = trim((string) $route);

        if ($route !== '') {

            $route = strtolower($route);

            $route = trim(
                preg_replace(
                    '/[^a-z0-9\/_-]+/',
                    '',
                    $route
                ),
                '/'
            );

            if ($route !== '') {

                $resource = explode('/', $route)[0];

                $resource = str_replace(
                    ['-', '_'],
                    ' ',
                    $resource
                );

                return $this->normalizeResourceName($resource);
            }
        }

        return $this->normalizeResourceName(
            strtolower(trim($title))
        );
    }

    protected function normalizeResourceName(string $value): string
    {
        $value = strtolower(trim($value));

        if ($value === '') {
            return '';
        }

        $map = [

            // Core admin resources
            'dashboard'     => 'dashboard',

            'modules'       => 'module',
            'module'        => 'module',

            'menus'         => 'menu',
            'menu'          => 'menu',

            'permissions'   => 'permission',
            'permission'    => 'permission',

            'roles'         => 'role',
            'role'          => 'role',

            'employees'     => 'employee',
            'employee'      => 'employee',

            'organizations' => 'organization',
            'organization'  => 'organization',

            'branches'      => 'branch',
            'branch'        => 'branch',

            'departments'   => 'department',
            'department'    => 'department',

            'designations'  => 'designation',
            'designation'   => 'designation',

            // Common irregular resources
            'people'        => 'person',
            'men'           => 'man',
            'women'         => 'woman',
            'children'      => 'child',

            'statuses'      => 'status',
            'status'        => 'status',

            'classes'       => 'class',
            'class'         => 'class',

            'addresses'     => 'address',
            'address'       => 'address',
        ];

        if (isset($map[$value])) {
            return $map[$value];
        }
        return $value;
    }

    public function remove(int $moduleId): bool
    {
        $this->menuModel
            ->where('module_id', $moduleId)
            ->delete();

        return true;
    }
}
