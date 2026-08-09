<?php

namespace App\Repositories;

use App\Models\Admin\PermissionModel;

class PermissionRepository
{
    protected PermissionModel $model;

    public function __construct()
    {
        $this->model = new PermissionModel();
    }

    protected function baseQuery()
    {
        return db_connect()
            ->table('permissions p')
            ->select([
                'p.*',
                'm.name AS module_name',
                'm.slug AS module_slug'
            ])
            ->join('modules m', 'm.id = p.module_id');
    }

    public function getAll(): array
    {
        return $this->baseQuery()
            ->orderBy('m.sort_order', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getByModule(int $moduleId): array
    {
        return $this->baseQuery()
            ->where('p.module_id', $moduleId)
            ->orderBy('p.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function find(int $id): ?array
    {
        return $this->baseQuery()
            ->where('p.id', $id)
            ->get()
            ->getRowArray();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->model
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data): int
    {
        $this->model->insert($data);

        return (int) $this->model->getInsertID();
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function exists(string $slug, ?int $ignoreId = null): bool
    {
        $builder = $this->model
            ->where('slug', $slug);

        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    public function search(array $filters): array
    {
        $builder = $this->baseQuery();

        if (!empty($filters['search'])) {

            $builder->groupStart()
                ->like('p.name', $filters['search'])
                ->orLike('p.slug', $filters['search'])
                ->orLike('m.name', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['module_id'])) {
            $builder->where('p.module_id', $filters['module_id']);
        }

        return $builder
            ->orderBy('m.sort_order', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getGroupedByModule(): array
    {
        $rows = $this->baseQuery()
            ->orderBy('m.sort_order', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];

        foreach ($rows as $row) {

            $grouped[$row['module_name']][] = $row;
        }

        return $grouped;
    }

    public function getRolePermissions(int $roleId): array
    {
        return $this->baseQuery()
            ->select('rp.permission_id')
            ->join(
                'role_permissions rp',
                "rp.permission_id = p.id AND rp.role_id = {$roleId}",
                'inner'
            )
            ->orderBy('m.sort_order', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
