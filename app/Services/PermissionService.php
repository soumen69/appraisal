<?php

namespace App\Services;

use App\Repositories\PermissionRepository;

class PermissionService
{
    protected PermissionRepository $repository;

    public function __construct()
    {
        $this->repository = new PermissionRepository();
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getByModule(int $moduleId): array
    {
        return $this->repository->getByModule($moduleId);
    }

    public function hasPermission(array $permissions, string $permission): bool
    {
        return in_array($permission, $permissions);
    }

    public function hasAny(array $permissions, array $required): bool
    {
        foreach ($required as $permission) {
            if (in_array($permission, $permissions)) {
                return true;
            }
        }

        return false;
    }

    public function hasAll(array $permissions, array $required): bool
    {
        foreach ($required as $permission) {
            if (!in_array($permission, $permissions)) {
                return false;
            }
        }

        return true;
    }


    public function grouped(): array
    {
        $permissions = $this->repository->getAll();
        $groups = [];
        foreach ($permissions as $permission) {
            $module = $permission['module_name'];
            if (!isset($groups[$module])) {
                $groups[$module] = [
                    'module' => $module,
                    'permissions' => []
                ];
            }
            $groups[$module]['permissions'][] = $permission;
        }
        return array_values($groups);
    }

    public function getById(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function create(array $data): int
    {
        if ($this->repository->exists($data['slug'])) {
            throw new \RuntimeException('Permission slug already exists.');
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        if ($this->repository->exists($data['slug'], $id)) {
            throw new \RuntimeException('Permission slug already exists.');
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getGrouped(): array
    {
        return $this->repository->getGroupedByModule();
    }

    public function getRolePermissionIds(int $roleId): array
    {
        return array_column(
            $this->repository->getRolePermissions($roleId),
            'permission_id'
        );
    }
}
