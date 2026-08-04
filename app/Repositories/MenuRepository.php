<?php

namespace App\Repositories;

use App\Models\Admin\MenuModel;

class MenuRepository
{
    protected MenuModel $model;

    public function __construct()
    {
        $this->model = new MenuModel();
    }

    public function getSidebarMenus(): array
    {
        return $this->model
            ->where('status', 'active')
            ->where('is_sidebar', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    public function getAll(): array
    {
        return $this->model
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    public function find(int $id): ?array
    {
        return $this->model->find($id);
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