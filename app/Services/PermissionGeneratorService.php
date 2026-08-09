<?php

namespace App\Services;

use App\Models\Admin\PermissionModel;

class PermissionGeneratorService
{
    protected PermissionModel $permissionModel;

    protected array $defaultPermissions = [
        'view',
        'create',
        'edit',
        'delete',
        'import',
        'export',
        'permission'
    ];

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
    }

    public function generate(
        int $moduleId,
        string $moduleSlug,
        string $moduleName,
        array $permissions = []
    ): bool {
        if (empty($permissions)) {
            $permissions = $this->defaultPermissions;
        }

        foreach ($permissions as $permission) {
            $permissionSlug = strtolower($moduleSlug) . '.' . strtolower($permission);

            $exists = $this->permissionModel
                ->where('slug', $permissionSlug)
                ->first();

            if ($exists) {
                continue;
            }

            $this->permissionModel->insert([

                'name'      => $moduleName . ' ' . ucfirst($permission),

                'slug'      => $permissionSlug,

                'module_id' => $moduleId

            ]);
        }

        return true;
    }

    public function remove(int $moduleId): bool
    {
        $this->permissionModel
            ->where('module_id', $moduleId)
            ->delete();

        return true;
    }
}
