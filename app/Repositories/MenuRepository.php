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

    public function getAll(): array
    {
        return $this->baseQuery()
            ->orderBy('m.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getParents(?int $ignoreId = null): array
    {
        $builder = $this->model
            ->select('id,title')
            ->where('parent_id', null)
            ->orderBy('title', 'ASC');

        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->findAll();
    }

    public function find(int $id): ?array
    {
        return $this->baseQuery()
            ->where('m.id', $id)
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

    // public function hasChildren(int $id): bool
    // {
    //     return $this->model
    //         ->where('parent_id', $id)
    //         ->countAllResults() > 0;
    // }

    public function existsRoute(string $route, ?int $ignoreId = null): bool
    {
        if ($route === '') {
            return false;
        }

        $builder = $this->model
            ->where('route', $route);

        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    public function paginate(array $filters): array
    {
        $builder = $this->baseQuery();

        if (!empty($filters['search'])) {

            $builder->groupStart()
                ->like('m.title', $filters['search'])
                ->orLike('m.route', $filters['search'])
                ->orLike('mod.name', $filters['search'])
                ->orLike('p.name', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where('m.status', $filters['status']);
        }

        if (!empty($filters['module_id'])) {
            $builder->where('m.module_id', $filters['module_id']);
        }

        $sortBy = $filters['sortBy'] ?? 'm.sort_order';
        $direction = $filters['direction'] ?? 'ASC';

        $builder->orderBy($sortBy, $direction);

        $page = (int) ($filters['page'] ?? 1);
        $perPage = (int) ($filters['perPage'] ?? 10);

        $total = $builder->countAllResults(false);

        $rows = $builder
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return [

            'data' => $rows,

            'total' => $total,

            'page' => $page,

            'perPage' => $perPage,

            'lastPage' => (int) ceil($total / $perPage)

        ];
    }

    protected function baseQuery()
    {
        $builder = db_connect()->table('menus m');

        $builder->select([
            'm.*',
            'mod.name AS module_name',
            // 'parent.title AS parent_name',
            'p.name AS permission_name'
        ]);

        $builder->join('modules mod', 'mod.id = m.module_id');
        // $builder->join('menus parent', 'parent.id = m.parent_id', 'left');
        $builder->join('permissions p', 'p.id = m.permission_id', 'left');

        return $builder;
    }

    public function getSidebarMenus(): array
    {
        return db_connect()
            ->table('menus m')
            ->select([
                'm.id',
                'm.title',
                'm.icon',
                'm.route',
                'm.sort_order',

                'mod.name AS module_name',
                'mod.sort_order AS module_sort_order',

                'p.slug AS permission_slug'
            ])
            ->join('modules mod', 'mod.id = m.module_id')
            ->join('permissions p', 'p.id = m.permission_id', 'left')

            ->where('m.status', 'active')
            ->where('m.is_sidebar', 1)
            ->where('m.is_visible', 1)

            ->orderBy('mod.sort_order', 'ASC')
            ->orderBy('m.sort_order', 'ASC')

            ->get()
            ->getResultArray();
    }

    // public function getSidebarMenus(): array
    // {
    //     return db_connect()
    //         ->table('menus m')
    //         ->select([
    //             'm.id',
    //             'm.parent_id',
    //             'm.title',
    //             'm.icon',
    //             'm.route',
    //             'm.sort_order',
    //             'p.slug AS permission_slug'
    //         ])

    //         ->join(
    //             'permissions p',
    //             'p.id=m.permission_id',
    //             'left'
    //         )
    //         ->where('m.status', 'active')
    //         ->where('m.is_sidebar', 1)
    //         ->where('m.is_visible', 1)
    //         ->orderBy('m.sort_order', 'ASC')
    //         ->get()
    //         ->getResultArray();
    // }
}
