<?php

namespace App\Services;

use App\Repositories\ModuleRepository;
use CodeIgniter\Database\Exceptions\DatabaseException;
class ModuleService
{
   protected ModuleRepository $repository;

    public function __construct()
{
    $this->repository = new ModuleRepository();
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

    return $this->repository->create($data);
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