<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use App\Repositories\RolePermissionRepository;

class RoleService
{
    protected RoleRepository $repository;
    protected RolePermissionRepository $rolePermissionRepository;
    public function __construct()
    {
        $this->repository = new RoleRepository();
        $this->rolePermissionRepository = new RolePermissionRepository();
    }

    public function getPaginated(array $filters): array
    {
        return $this->repository->paginate($filters);
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getActive(): array
    {
        return $this->repository->getActive();
    }

    public function getParents(?int $ignoreId = null): array
    {
        return $this->repository->getParents($ignoreId);
    }

    public function getById(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function create(array $data): int
    {
        if ($this->repository->exists($data['slug'])) {
            throw new \RuntimeException('Role slug already exists.');
        }
        if (empty($data['parent_role_id'])) {
            $data['parent_role_id'] = null;
        }

        if (empty($data['icon'])) {
            $data['icon'] = null;
        }

        if (empty($data['description'])) {
            $data['description'] = null;
        }

        if (empty($data['color'])) {
            $data['color'] = null;
        }
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        if ($this->repository->exists($data['slug'], $id)) {
            throw new \RuntimeException('Role slug already exists.');
        }

        if (empty($data['parent_role_id'])) {
            $data['parent_role_id'] = null;
        }

        if (empty($data['icon'])) {
            $data['icon'] = null;
        }

        if (empty($data['description'])) {
            $data['description'] = null;
        }

        if (empty($data['color'])) {
            $data['color'] = null;
        }
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $role = $this->repository->find($id);

        if (!$role) {
            throw new \RuntimeException('Role not found.');
        }

        if ((int)$role['is_system'] === 1) {
            throw new \RuntimeException('System roles cannot be deleted.');
        }

        return $this->repository->delete($id);
    }

    public function savePermissions(int $roleId, array $permissions): void
    {

        $db = db_connect();

        $db->transStart();

        $existing = $this->rolePermissionRepository
            ->getPermissionIds($roleId);

        $permissions = array_map(
            'intval',
            $permissions
        );

        $toInsert = array_diff(
            $permissions,
            $existing
        );

        $toDelete = array_diff(
            $existing,
            $permissions
        );

        $this->rolePermissionRepository
            ->insertBatch(
                $roleId,
                $toInsert
            );

        $this->rolePermissionRepository
            ->deleteBatch(
                $roleId,
                $toDelete
            );

        $db->transComplete();

        if (!$db->transStatus()) {

            throw new \RuntimeException(
                'Unable to update permissions.'
            );
        }
    }
}
