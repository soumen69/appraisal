<?php

namespace App\Repositories;

class RolePermissionRepository
{
    protected $builder;

    public function __construct()
    {
        $this->builder = db_connect()->table('role_permissions');
    }

    public function getPermissionIds(int $roleId): array
    {
        return array_column(

            $this->builder
                ->select('permission_id')
                ->where('role_id', $roleId)
                ->get()
                ->getResultArray(),

            'permission_id'

        );
    }

    public function insertBatch(int $roleId, array $permissionIds): void
    {
        if (empty($permissionIds)) {
            return;
        }

        $rows = [];

        foreach ($permissionIds as $permissionId) {
            $rows[] = [

                'role_id'       => $roleId,

                'permission_id' => $permissionId

            ];
        }

        $this->builder->insertBatch($rows);
    }

    public function deleteBatch(int $roleId, array $permissionIds): void
    {
        if (empty($permissionIds)) {
            return;
        }

        $this->builder
            ->where('role_id', $roleId)
            ->whereIn('permission_id', $permissionIds)
            ->delete();
    }

    public function getPermissionSlugs(int $roleId): array
    {
        return array_column(

            $this->builder
                ->select('p.slug')
                ->join(
                    'permissions p',
                    'p.id = role_permissions.permission_id'
                )
                ->where('role_id', $roleId)
                ->get()
                ->getResultArray(),

            'slug'

        );
    }
}
