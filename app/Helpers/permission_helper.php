<?php

if (!function_exists('can')) {
    function can(string $permission): bool
    {
        $permission = trim($permission);

        if ($permission === '') {
            return false;
        }

        if ((bool) session('is_super')) {
            return true;
        }

        $permissions = session('permissions') ?? [];

        return in_array('*', $permissions, true)
            || in_array($permission, $permissions, true);
    }
}

if (!function_exists('cannot')) {
    function cannot(string $permission): bool
    {
        return !can($permission);
    }
}

if (!function_exists('canAny')) {
    function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (can((string) $permission)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('canAll')) {
    function canAll(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!can((string) $permission)) {
                return false;
            }
        }
        return true;
    }
}
