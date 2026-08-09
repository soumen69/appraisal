<?php

if (!function_exists('can')) {

    function can(string $permission): bool
    {
        if ((bool) session('is_super')) {
            return true;
        }

        return in_array(
            $permission,
            session('permissions') ?? [],
            true
        );
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
            if (can($permission)) {
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
            if (!can($permission)) {
                return false;
            }
        }

        return true;
    }
}
