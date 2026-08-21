<?php

namespace App\Services;

use App\Models\Admin\PermissionModel;

class PermissionGeneratorService
{
    protected PermissionModel $permissionModel;

    /**
     * Default actions available for a resource.
     */
    protected array $defaultPermissions = [
        'view',
        'create',
        'edit',
        'delete',
        'import',
        'export',
        'permission',
    ];

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
    }

    /**
     * Generate permissions for a resource.
     *
     * Permissions are identified by:
     *
     *     resource.action
     *
     * Example:
     *
     *     employee.view
     *     employee.create
     *     employee.edit
     *     employee.delete
     */
    public function generateForResource(
        int $moduleId,
        string $resource,
        string $displayName,
        array $actions = []
    ): array {
        $resource = $this->normalizeResource($resource);

        if ($resource === '') {
            throw new \InvalidArgumentException(
                'Permission resource cannot be empty.'
            );
        }

        $actions = $this->normalizeActions($actions);

        if (empty($actions)) {
            $actions = $this->defaultPermissions;
        }

        $created = [];

        foreach ($actions as $action) {

            $slug = $resource . '.' . $action;

            $existing = $this->permissionModel
                ->where('slug', $slug)
                ->first();

            if ($existing) {
                $created[$action] = (int) $existing['id'];
                continue;
            }

            $inserted = $this->permissionModel->insert([
                'name'      => $displayName . ' ' . ucfirst($action),
                'slug'      => $slug,
                'module_id' => $moduleId,
                'is_system' => 1,
            ], true);

            $created[$action] = (int) $inserted;
        }

        return $created;
    }

    /**
     * Ensure a single permission exists.
     */
    public function ensure(
        int $moduleId,
        string $resource,
        string $action,
        string $displayName
    ): int {
        $resource = $this->normalizeResource($resource);
        $action   = $this->normalizeAction($action);

        $slug = $resource . '.' . $action;

        $existing = $this->permissionModel
            ->where('slug', $slug)
            ->first();

        if ($existing) {
            return (int) $existing['id'];
        }

        return (int) $this->permissionModel->insert([
            'name'      => $displayName . ' ' . ucfirst($action),
            'slug'      => $slug,
            'module_id' => $moduleId,
            'is_system' => 1,
        ], true);
    }

    /**
     * Remove all permissions belonging to a module.
     *
     * Use this only for explicit module destruction.
     */
    public function removeForModule(int $moduleId): bool
    {
        $this->permissionModel
            ->where('module_id', $moduleId)
            ->delete();

        return true;
    }

    protected function normalizeResource(string $resource): string
    {
        $resource = strtolower(trim($resource));

        $resource = preg_replace(
            '/[^a-z0-9]+/',
            '.',
            $resource
        );

        $resource = trim($resource, '.');

        return $resource;
    }

    protected function normalizeAction(string $action): string
    {
        return strtolower(
            preg_replace(
                '/[^a-z0-9_]+/',
                '',
                trim($action)
            )
        );
    }

    protected function normalizeActions(array $actions): array
    {
        $actions = array_map(
            fn($action) => $this->normalizeAction((string) $action),
            $actions
        );

        $actions = array_filter(
            $actions,
            fn($action) => $action !== ''
        );

        return array_values(
            array_unique($actions)
        );
    }
}
