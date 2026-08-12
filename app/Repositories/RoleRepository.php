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

    protected function baseQuery()
    {
        return db_connect()
            ->table('roles r')
            ->select([
                'r.*',
                'creator.full_name AS created_by_name',
                'COUNT(DISTINCT rp.permission_id) AS permission_count'
            ])
            ->join('users creator', 'creator.id = r.created_by', 'left')
            ->join('role_permissions rp', 'rp.role_id = r.id', 'left')
            ->groupBy('r.id');
    }

    public function getAll(): array
    {
        return $this->baseQuery()
            ->orderBy('r.sort_order', 'ASC')
            ->orderBy('r.display_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getActive(): array
    {
        return $this->baseQuery()
            ->where('r.status', 'active')
            ->orderBy('r.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getOptions(): array
    {
        return $this->model
            ->select([
                'id',
                'display_name'
            ])
            ->where('status', 'active')
            ->orderBy('display_name', 'ASC')
            ->findAll();
    }

    public function find(int $id): ?array
    {
        return $this->baseQuery()
            ->where('r.id', $id)
            ->get()
            ->getRowArray();
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
        $builder = $this->baseQuery();

        if (!empty($filters['search'])) {

            $builder->groupStart()
                ->like('r.name', $filters['search'])
                ->orLike('r.display_name', $filters['search'])
                ->orLike('r.slug', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('r.status', $filters['status']);
        }

        $sortBy = $filters['sortBy'] ?? 'r.sort_order';
        $direction = $filters['direction'] ?? 'ASC';

        $builder->orderBy($sortBy, $direction);

        $page = (int)($filters['page'] ?? 1);
        $perPage = (int)($filters['perPage'] ?? 10);

        $total = count($builder->get()->getResultArray());

        $rows = $builder
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return [

            'data' => $rows,

            'total' => $total,

            'page' => $page,

            'perPage' => $perPage,

            'lastPage' => (int)ceil($total / $perPage)

        ];
    }
}
