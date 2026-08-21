<?php

namespace App\Services;

use App\Repositories\MenuRepository;

class MenuService
{
    protected MenuRepository $repository;
    protected MenuGeneratorService $menuGenerator;

    public function __construct()
    {
        $this->repository = new MenuRepository();
        $this->menuGenerator = new MenuGeneratorService();
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

        if (empty($data['permission_id'])) {
            $data['permission_id'] = null;
        }

        $db = db_connect();

        $db->transBegin();

        try {

            $menuId = $this->repository->create($data);

            $actions = $this->normalizePermissionActions(
                $data['permission_actions'] ?? []
            );

            /*
             * Generate/sync permissions for this menu.
             */
            $this->menuGenerator->syncPermissions(
                $menuId,
                $actions
            );

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Menu creation transaction failed.'
                );
            }

            $db->transCommit();

            return $menuId;
        } catch (\Throwable $e) {

            $db->transRollback();

            throw $e;
        }
    }

    protected function normalizePermissionActions(
        mixed $actions
    ): array {
        if (!is_array($actions)) {
            return [];
        }

        $allowed = [
            'view',
            'create',
            'edit',
            'delete',
            'import',
            'export',
            'permission',
        ];

        $actions = array_map(
            static fn($action) =>
            strtolower(trim((string) $action)),
            $actions
        );

        return array_values(
            array_unique(
                array_intersect(
                    $actions,
                    $allowed
                )
            )
        );
    }

    public function update(int $id, array $data): bool
    {
        $this->validateRoute($data, $id);

        if (empty($data['permission_id'])) {
            $data['permission_id'] = null;
        }

        $updated = $this->repository->update(
            $id,
            $data
        );

        /*
         * Keep permissions synchronized with the menu.
         *
         * Existing permissions are preserved.
         * Missing standard permissions are created.
         */
        if ($updated) {

            $actions = $this->normalizePermissionActions(
                $data['permission_actions'] ?? []
            );

            $this->menuGenerator->syncPermissions(
                $id,
                $actions
            );
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $menu = $this->repository->find($id);

        if (!$menu) {
            throw new \RuntimeException(
                'Menu not found.'
            );
        }

        if (!empty($menu['is_system'])) {
            throw new \RuntimeException(
                'System menus cannot be deleted.'
            );
        }

        return $this->repository->delete($id);
    }

    protected function validateRoute(
        array $data,
        ?int $ignoreId = null
    ): void {
        $route = trim(
            (string) ($data['route'] ?? '')
        );

        if ($route === '') {
            return;
        }

        if (
            $this->repository->existsRoute(
                $route,
                $ignoreId
            )
        ) {
            throw new \RuntimeException(
                'Route already exists.'
            );
        }
    }

    public function getSidebarMenus(): array
    {
        return $this->repository
            ->getSidebarMenus();
    }
}
