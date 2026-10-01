<?php

namespace App\Services;

use App\Models\UserModel;

class AuthService
{
    protected UserModel $users;

    protected const OTP_EXPIRY_MINUTES = 5;
    protected const MAX_OTP_ATTEMPTS = 5;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    /**
     * Generate and email a login OTP.
     */
    public function requestOtp(string $email): void
    {
        $email = trim($email);

        if ($email === '') {
            throw new \RuntimeException('Please enter your email address.');
        }

        $user = $this->users->findByEmail($email);

        // Use a generic message so the endpoint does not reveal
        // whether an email address belongs to an account.
        if (!$user || $user['status'] !== 'active') {
            throw new \RuntimeException(
                'If an active account exists for this email, a login code will be sent.'
            );
        }

        $otp = (string) random_int(100000, 999999);

        $expiresAt = date(
            'Y-m-d H:i:s',
            time() + (self::OTP_EXPIRY_MINUTES * 60)
        );

        $updated = $this->users->update((int) $user['id'], [
            'login_otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
            'login_otp_expires_at' => $expiresAt,
            'login_otp_attempts' => 0,
        ]);

        if (!$updated) {
            throw new \RuntimeException('Unable to generate a login code. Please try again.');
        }

        $emailService = \Config\Services::email();

        $emailService->setTo($user['email']);
        $emailService->setSubject('Your login verification code');
        $emailService->setMailType('html');
        $emailService->setMessage(
            '<p>Your login verification code is:</p>' .
                '<h2>' . esc($otp) . '</h2>' .
                '<p>This code expires in ' . self::OTP_EXPIRY_MINUTES . ' minutes.</p>' .
                '<p>If you did not request this code, you can ignore this email.</p>'
        );

        if (!$emailService->send()) {
            // Clear the OTP if delivery failed.
            $this->users->update((int) $user['id'], [
                'login_otp_hash' => null,
                'login_otp_expires_at' => null,
                'login_otp_attempts' => 0,
            ]);

            log_message('error', 'Login OTP email failed to send.');

            throw new \RuntimeException(
                'We could not send your login code. Please try again later.'
            );
        }
    }

    /**
     * Verify the OTP and sign the user in.
     */
    public function verifyOtp(string $email, string $otp, string $ip): void
    {
        $email = trim($email);
        $otp = trim($otp);

        if ($email === '' || $otp === '') {
            throw new \RuntimeException('Please enter your email address and verification code.');
        }

        $user = $this->users->findByEmail($email);

        if (!$user || $user['status'] !== 'active') {
            throw new \RuntimeException('Invalid or expired verification code.');
        }

        if (
            empty($user['login_otp_hash']) ||
            empty($user['login_otp_expires_at']) ||
            strtotime($user['login_otp_expires_at']) < time()
        ) {
            throw new \RuntimeException('Your verification code has expired. Please request a new one.');
        }

        if ((int) $user['login_otp_attempts'] >= self::MAX_OTP_ATTEMPTS) {
            throw new \RuntimeException('Too many incorrect attempts. Please request a new code.');
        }

        // Count each verification attempt, including a successful attempt.
        $attempts = (int) $user['login_otp_attempts'] + 1;

        $this->users->update((int) $user['id'], [
            'login_otp_attempts' => $attempts,
        ]);

        if (!password_verify($otp, $user['login_otp_hash'])) {
            throw new \RuntimeException('Invalid or expired verification code.');
        }

        // Consume the OTP so it cannot be reused.
        $this->users->update((int) $user['id'], [
            'login_otp_hash' => null,
            'login_otp_expires_at' => null,
            'login_otp_attempts' => 0,
        ]);

        $this->establishSession($user, $ip);
    }

    /**
     * Load roles/permissions and establish the authenticated session.
     */
    protected function establishSession(array $user, string $ip): void
    {
        $roles = $this->loadRoles((int) $user['id']);

        $isSuper = in_array(
            1,
            array_map('intval', array_column($roles, 'is_super')),
            true
        );

        $permissions = $isSuper
            ? ['*']
            : $this->loadPermissions((int) $user['id']);

        $primaryRole = $roles[0] ?? null;

        session()->regenerate();

        session()->set([
            'is_logged_in' => true,
            'user_id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'employee_code' => $user['employee_code'],
            'organization_id' => (int) $user['organization_id'],
            'roles' => $roles,
            'primary_role' => $primaryRole['display_name'] ?? 'User',
            'primary_role_slug' => $primaryRole['slug'] ?? null,
            'permissions' => $permissions,
            'is_super' => $isSuper,
        ]);

        $this->users->updateLastLogin((int) $user['id'], $ip);
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
                'r.is_super',
            ])
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('r.status', 'active')
            ->get()
            ->getResultArray();
    }

    protected function loadPermissions(int $userId): array
    {
        $db = db_connect();

        $rolePermissions = $db
            ->table('user_roles ur')
            ->select('p.slug')
            ->join('role_permissions rp', 'rp.role_id = ur.role_id')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('ur.user_id', $userId)
            ->get()
            ->getResultArray();

        $permissions = array_unique(array_column($rolePermissions, 'slug'));

        $overrides = $db
            ->table('user_permissions up')
            ->select(['p.slug', 'up.is_allowed'])
            ->join('permissions p', 'p.id = up.permission_id')
            ->where('up.user_id', $userId)
            ->get()
            ->getResultArray();

        foreach ($overrides as $override) {
            if ($override['is_allowed']) {
                $permissions[] = $override['slug'];
            } else {
                $permissions = array_diff($permissions, [$override['slug']]);
            }
        }

        return array_values(array_unique($permissions));
    }
}
