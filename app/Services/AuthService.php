<?php

namespace App\Services;

use Config\Database;
use App\Models\UserModel;
use App\Models\RoleModel;

class AuthService
{
    protected UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function login(string $email, string $password, string $ip): void
    {
        $user = $this->users->findByEmail(trim($email));
        if (!$user) {
            throw new \RuntimeException(
                'Invalid email or password.'
            );
        }

        if (!password_verify(
            $password,
            $user['password']
        )) {
            throw new \RuntimeException(
                'Invalid email or password.'
            );
        }

        if ($user['status'] !== 'active') {
            throw new \RuntimeException(
                'Your account is inactive.'
            );
        }

        $roles = $this->loadRoles(
            (int)$user['id']
        );

        $isSuper = in_array(
            1,
            array_column($roles, 'is_super')
        );

        $permissions = $isSuper
            ? ['*']
            : $this->loadPermissions(
                (int)$user['id']
            );

        $primaryRole = $roles[0] ?? null;

        session()->regenerate();

        session()->set([

            'is_logged_in' => true,

            'user_id' => (int) $user['id'],

            'full_name' => $user['full_name'],

            'email' => $user['email'],

            'avatar' => $user['profile_photo'],

            'employee_code' => $user['employee_code'],

            'organization_id' => $user['organization_id'],

            'roles' => $roles,

            'primary_role' => $primaryRole['display_name'] ?? 'User',

            'primary_role_slug' => $primaryRole['slug'] ?? null,

            'permissions' => $permissions,

            'is_super' => $isSuper

        ]);

        $this->users->updateLastLogin(
            (int)$user['id'],
            $ip
        );
    }

    public function logout(): void
    {
        session()->destroy();
    }

    protected function loadRoles(int $userId): array
    {
        return db_connect()

            ->table('user_roles ur')

            ->select([
                'r.id',
                'r.name',
                'r.slug',
                'r.display_name',
                'r.icon',
                'r.color',
                'r.is_super'
            ])

            ->join(
                'roles r',
                'r.id = ur.role_id'
            )

            ->where(
                'ur.user_id',
                $userId
            )

            ->where(
                'r.status',
                'active'
            )

            // ->orderBy(
            //     'ur.is_primary',
            //     'DESC'
            // )

            ->get()

            ->getResultArray();
    }

    protected function loadPermissions(int $userId): array
    {
        $db = db_connect();

        $rolePermissions = $db

            ->table('user_roles ur')

            ->select('p.slug')

            ->join(
                'role_permissions rp',
                'rp.role_id=ur.role_id'
            )

            ->join(
                'permissions p',
                'p.id=rp.permission_id'
            )

            ->where(
                'ur.user_id',
                $userId
            )

            ->get()

            ->getResultArray();

        $permissions = array_unique(
            array_column(
                $rolePermissions,
                'slug'
            )
        );

        $overrides = $db

            ->table('user_permissions up')

            ->select([
                'p.slug',
                'up.is_allowed'
            ])

            ->join(
                'permissions p',
                'p.id=up.permission_id'
            )

            ->where(
                'up.user_id',
                $userId
            )

            ->get()

            ->getResultArray();

        foreach ($overrides as $override) {
            if ($override['is_allowed']) {
                $permissions[] = $override['slug'];
            } else {
                $permissions = array_diff(
                    $permissions,
                    [$override['slug']]
                );
            }
        }

        return array_values(
            array_unique($permissions)
        );
    }
}
