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

    public function getAll(): array
    {
        return $this->model
            ->orderBy('module', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getByModule(string $module): array
    {
        return $this->model
            ->where('module', $module)
            ->findAll();
    }

    public function find(int $id): ?array
    {
        return $this->model->find($id);
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

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
}