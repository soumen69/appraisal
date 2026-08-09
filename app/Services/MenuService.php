<?php

namespace App\Services;

use App\Repositories\MenuRepository;

class MenuService
{
    protected MenuRepository $repository;

    public function __construct()
    {
        $this->repository = new MenuRepository();
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getById(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function getParents(?int $ignoreId = null): array
    {
        return $this->repository->getParents($ignoreId);
    }

    public function getPaginated(array $filters): array
    {
        return $this->repository->paginate($filters);
    }

    public function create(array $data): int
    {
        $this->validateRoute($data);

        // $this->validateParent($data);

        // if (empty($data['parent_id'])) {
        //     $data['parent_id'] = null;
        // }

        if (empty($data['permission_id'])) {
            $data['permission_id'] = null;
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $this->validateRoute($data, $id);

        // $this->validateParent($data, $id);

        // if (empty($data['parent_id'])) {
        //     $data['parent_id'] = null;
        // }

        if (empty($data['permission_id'])) {
            $data['permission_id'] = null;
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $menu = $this->repository->find($id);

        if (! $menu) {
            throw new \RuntimeException('Menu not found.');
        }

        if (!empty($menu['is_system'])) {
            throw new \RuntimeException('System menus cannot be deleted.');
        }

        // if ($this->repository->hasChildren($id)) {
        //     throw new \RuntimeException(
        //         'This menu contains child menus. Remove or reassign them first.'
        //     );
        // }

        return $this->repository->delete($id);
    }

    protected function validateRoute(array $data, ?int $ignoreId = null): void
    {
        $route = trim($data['route'] ?? '');

        if ($route === '') {
            return;
        }

        if ($this->repository->existsRoute($route, $ignoreId)) {
            throw new \RuntimeException('Route already exists.');
        }
    }

    // protected function validateParent(array $data, ?int $id = null): void
    // {
    //     if (empty($data['parent_id'])) {
    //         return;
    //     }

    //     $parentId = (int) $data['parent_id'];

    //     if ($id !== null && $parentId === $id) {
    //         throw new \RuntimeException(
    //             'A menu cannot be its own parent.'
    //         );
    //     }

    //     $parent = $this->repository->find($parentId);

    //     if (!$parent) {
    //         throw new \RuntimeException(
    //             'Selected parent menu does not exist.'
    //         );
    //     }
    // }

    public function getSidebarMenus(): array
    {
        return $this->repository
            ->getSidebarMenus();
    }
}
