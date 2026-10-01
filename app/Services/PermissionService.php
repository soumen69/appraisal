<?php

namespace App\Services;

use RuntimeException;

class PermissionService
{
    public function hasPermission(array $permissions, string $permission): bool
    {
        $permission = trim($permission);

        if ($permission === '') {
            return false;
        }

        return in_array('*', $permissions, true)
            || in_array($permission, $permissions, true);
    }

    public function hasAny(array $permissions, array $required): bool
    {
        foreach ($required as $permission) {
            if ($this->hasPermission($permissions, (string) $permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAll(array $permissions, array $required): bool
    {
        foreach ($required as $permission) {
            if (!$this->hasPermission($permissions, (string) $permission)) {
                return false;
            }
        }

        return true;
    }

    public function getGrouped(): array
    {
        $rows = db_connect()
            ->table('permissions')
            ->select(['id', 'name', 'slug', 'is_system'])
            ->orderBy('slug', 'ASC')
            ->get()
            ->getResultArray();

        $groups = [];

        foreach ($rows as $row) {
            $slug = trim((string) ($row['slug'] ?? ''));

            if ($slug === '' || !str_contains($slug, '.')) {
                continue;
            }

            $parts = explode('.', $slug);
            $action = trim((string) array_pop($parts));
            $resource = trim(implode('.', $parts));

            if ($resource === '' || $action === '') {
                continue;
            }

            $label = ucwords(str_replace(['_', '-'], ' ', $resource));

            if (!isset($groups[$resource])) {
                $groups[$resource] = [
                    'resource' => $resource,
                    'label' => $label,
                    'permissions' => []
                ];
            }

            $row['id'] = (int) $row['id'];
            $row['is_system'] = (int) $row['is_system'];
            $row['action'] = $action;
            $row['description'] = $this->getPermissionDescription($action);

            $groups[$resource]['permissions'][] = $row;
        }

        $actionOrder = [
            'view' => 1,
            'create' => 2,
            'edit' => 3,
            'delete' => 4,
            'import' => 5,
            'export' => 6,
            'permission' => 7
        ];

        foreach ($groups as &$group) {
            usort(
                $group['permissions'],
                static function (array $a, array $b) use ($actionOrder): int {
                    $aOrder = $actionOrder[$a['action']] ?? 99;
                    $bOrder = $actionOrder[$b['action']] ?? 99;

                    if ($aOrder !== $bOrder) {
                        return $aOrder <=> $bOrder;
                    }

                    return strcasecmp(
                        (string) ($a['name'] ?? ''),
                        (string) ($b['name'] ?? '')
                    );
                }
            );
        }
        unset($group);

        uasort(
            $groups,
            static fn(array $a, array $b): int => strcasecmp(
                (string) $a['label'],
                (string) $b['label']
            )
        );

        return array_values($groups);
    }

    public function getRolePermissionIds(int $roleId): array
    {
        if ($roleId <= 0) {
            return [];
        }

        $rows = db_connect()
            ->table('role_permissions')
            ->select('permission_id')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        return array_values(array_map(
            'intval',
            array_column($rows, 'permission_id')
        ));
    }

    public function saveRolePermissions(int $roleId, array $permissionIds): void
    {
        if ($roleId <= 0) {
            throw new RuntimeException('Invalid role.');
        }

        $db = db_connect();

        $role = $db->table('roles')
            ->select(['id', 'is_super'])
            ->where('id', $roleId)
            ->get()
            ->getRowArray();

        if (!$role) {
            throw new RuntimeException('Role not found.');
        }

        if ((int) $role['is_super'] === 1) {
            throw new RuntimeException('Super Admin does not require permission assignments.');
        }

        $permissionIds = $this->normalizePermissionIds($permissionIds);

        if ($permissionIds !== []) {
            $validPermissionIds = array_map(
                'intval',
                array_column(
                    $db->table('permissions')
                        ->select('id')
                        ->whereIn('id', $permissionIds)
                        ->get()
                        ->getResultArray(),
                    'id'
                )
            );

            sort($validPermissionIds);
            $submittedPermissionIds = $permissionIds;
            sort($submittedPermissionIds);

            if ($validPermissionIds !== $submittedPermissionIds) {
                throw new RuntimeException('One or more selected permissions are invalid.');
            }
        }

        $db->transBegin();

        try {
            $db->table('role_permissions')
                ->where('role_id', $roleId)
                ->delete();

            if ($permissionIds !== []) {
                $rows = [];

                foreach ($permissionIds as $permissionId) {
                    $rows[] = [
                        'role_id' => $roleId,
                        'permission_id' => $permissionId
                    ];
                }

                $db->table('role_permissions')->insertBatch($rows);
            }

            if (!$db->transStatus()) {
                throw new RuntimeException('Unable to update permissions.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    protected function normalizePermissionIds(array $permissionIds): array
    {
        $permissionIds = array_map('intval', $permissionIds);
        $permissionIds = array_filter(
            $permissionIds,
            static fn(int $id): bool => $id > 0
        );

        $permissionIds = array_values(array_unique($permissionIds));
        sort($permissionIds);

        return $permissionIds;
    }

    protected function getPermissionDescription(string $action): string
    {
        return match ($action) {
            'view' => 'Allows viewing records.',
            'create' => 'Allows creating new records.',
            'edit' => 'Allows editing existing records.',
            'delete' => 'Allows deleting existing records.',
            'import' => 'Allows importing records.',
            'export' => 'Allows exporting records.',
            'permission' => 'Allows managing access permissions.',
            default => 'Allows this application capability.'
        };
    }
}
