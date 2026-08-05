<?php

namespace App\Repositories;

use App\Models\Admin\ModuleModel;

class ModuleRepository
{
    protected ModuleModel $model;

    public function __construct()
    {
        $this->model = new ModuleModel();
    }

    public function getAll(): array
    {
        return $this->model
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getActive(): array
    {
        return $this->model
            ->where('status', 'active')
            ->orderBy('sort_order', 'ASC')
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

    public function exists(string $slug, ?int $ignoreId = null): bool
    {
        $builder = $this->model
            ->where('slug', $slug);

        if ($ignoreId !== null) {

            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    public function paginate(array $filters): array
    {
        $builder = $this->model;

        if (!empty($filters['search'])) {

            $builder->groupStart()

                ->like('name', $filters['search'])

                ->orLike('slug', $filters['search'])

                ->orLike('route', $filters['search'])

                ->groupEnd();
        }

        if (!empty($filters['status'])) {

            $builder->where('status', $filters['status']);
        }

        $sortBy = $filters['sortBy'] ?? 'sort_order';

        $direction = $filters['direction'] ?? 'ASC';

        $builder->orderBy($sortBy, $direction);

        $page = (int) ($filters['page'] ?? 1);

        $perPage = (int) ($filters['perPage'] ?? 10);

        $total = $builder->countAllResults(false);

        $rows = $builder
            ->findAll($perPage, ($page - 1) * $perPage);

        return [

            'data' => $rows,

            'total' => $total,

            'page' => $page,

            'perPage' => $perPage,

            'lastPage' => (int) ceil($total / $perPage)

        ];
    }
}
