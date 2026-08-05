<?php

namespace App\Services;

use App\Repositories\ModuleRepository;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ModuleService
{
    protected ModuleRepository $repository;
    protected PermissionGeneratorService $permissionGenerator;
    protected MenuGeneratorService $menuGenerator;

    public function __construct()
    {
        $this->repository = new ModuleRepository();
        $this->permissionGenerator = new PermissionGeneratorService();
        $this->menuGenerator = new MenuGeneratorService();
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getActive(): array
    {
        return $this->repository->getActive();
    }

    public function getById(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function create(array $data): int
    {
        if ($this->repository->exists($data['slug'])) {
            throw new \RuntimeException('Module slug already exists.');
        }

        $db = db_connect();

        $db->transBegin();

        try {

            $moduleId = $this->repository->create($data);

            $this->permissionGenerator->generate(
                $moduleId,
                $data['slug'],
                $data['name']
            );

            $this->menuGenerator->generate(
                $moduleId,
                $data['name'],
                $data['slug'],
                $data['icon'] ?? null,
                $data['sort_order'] ?? 1
            );

            $db->transCommit();

            return $moduleId;
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getPaginated(array $filters): array
    {
        return $this->repository->paginate($filters);
    }
}
