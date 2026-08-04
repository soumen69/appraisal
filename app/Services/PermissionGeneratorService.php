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

        'export'

    ];

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
    }

    public function generate(
        string $module,
        array $permissions = []
    )
    {
        if (empty($permissions)) {

            $permissions = $this->defaultPermissions;

        }

        foreach ($permissions as $permission) {

            $slug = strtolower($module) . '.' . strtolower($permission);

            $exists = $this->permissionModel
                ->where('slug', $slug)
                ->first();

            if ($exists) {

                continue;

            }

            $this->permissionModel->insert([

                'name' => ucfirst($permission),

                'slug' => $slug,

                'module' => strtolower($module)

            ]);
        }

        return true;
    }
}