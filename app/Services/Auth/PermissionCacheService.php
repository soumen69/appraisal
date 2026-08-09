<?php

namespace App\Services\Auth;

use App\Repositories\RolePermissionRepository;

class PermissionCacheService
{
    protected RolePermissionRepository $repository;

    public function __construct()
    {
        $this->repository = new RolePermissionRepository();
    }

    public function remember(int $userId, int $roleId): void
    {
        $permissions = $this->repository->getPermissionSlugs($roleId);

        session()->set('permissions', $permissions);

        session()->set('role_id', $roleId);

        session()->set('permission_loaded', true);
    }

    public function clear(): void
    {
        session()->remove([
            'permissions',
            'role_id',
            'permission_loaded'
        ]);
    }
}
