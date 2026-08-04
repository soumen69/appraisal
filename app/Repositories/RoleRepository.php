<?php

namespace App\Repositories;

use App\Models\Admin\RoleModel;

class RoleRepository
{
    protected RoleModel $model;

    public function __construct()
    {
        $this->model = new RoleModel();
    }

    public function getAll(): array
    {
        return $this->model
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getSystemRoles(): array
    {
        return $this->model
            ->where('is_system', 1)
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

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
}