<?php

namespace App\Services;

class PermissionService
{
    public function hasPermission(
        array $permissions,
        string $permission
    ): bool
    {
        return in_array($permission, $permissions);
    }

    public function hasAny(
        array $permissions,
        array $required
    ): bool
    {
        foreach ($required as $permission) {

            if (in_array($permission, $permissions)) {

                return true;

            }

        }

        return false;
    }

    public function hasAll(
        array $permissions,
        array $required
    ): bool
    {
        foreach ($required as $permission) {

            if (!in_array($permission, $permissions)) {

                return false;

            }

        }

        return true;
    }
}