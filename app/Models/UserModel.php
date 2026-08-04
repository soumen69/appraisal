<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields    = true;

    protected $allowedFields = [

        'organization_id',
        'branch_id',
        'department_id',
        'designation_id',

        'employee_code',

        'first_name',
        'last_name',
        'full_name',

        'email',
        'phone',

        'password',

        'password_reset_token',
        'password_reset_expiry',

        'gender',
        'dob',
        'joining_date',

        'reporting_manager_id',

        'avatar',
        'profile_photo',

        'last_login',
        'last_login_ip',

        'remember_token',

        'status'

    ];

    /*
    |--------------------------------------------------------------------------
    | Timestamps
    |--------------------------------------------------------------------------
    */

    protected $useTimestamps = true;

    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected $validationRules = [];

    protected $validationMessages = [];

    protected $skipValidation = false;

    protected $cleanValidationRules = true;

    /*
    |--------------------------------------------------------------------------
    | Callbacks
    |--------------------------------------------------------------------------
    */

    protected $allowCallbacks = true;

    protected $beforeInsert = [

        'hashPassword',
        'generateFullName'

    ];

    protected $beforeUpdate = [

        'hashPassword',
        'generateFullName'

    ];

    /*
    |--------------------------------------------------------------------------
    | Password Hashing
    |--------------------------------------------------------------------------
    */

    protected function hashPassword(array $data)
    {
        if (
            isset($data['data']['password']) &&
            !empty($data['data']['password'])
        ) {

            if (
                password_get_info($data['data']['password'])['algo'] === null
            ) {

                $data['data']['password'] = password_hash(
                    $data['data']['password'],
                    PASSWORD_DEFAULT
                );
            }
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Full Name Generator
    |--------------------------------------------------------------------------
    */

    protected function generateFullName(array $data)
    {
        $first = $data['data']['first_name'] ?? '';

        $last = $data['data']['last_name'] ?? '';

        if ($first !== '') {
            $data['data']['full_name'] = trim($first . ' ' . $last);
        }
        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function findByEmployeeCode(string $employeeCode)
    {
        return $this->where('employee_code', $employeeCode)->first();
    }

    public function getActiveUser(int $id)
    {
        return $this->where('id', $id)
            ->where('status', 'active')
            ->first();
    }

    public function updateLastLogin(int $userId, string $ip)
    {
        return $this->update($userId, [
            'last_login'    => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip
        ]);
    }

    public function updateRememberToken(int $userId, string $token)
    {
        return $this->update($userId, [
            'remember_token' => $token
        ]);
    }

    public function clearRememberToken(int $userId)
    {
        return $this->update($userId, [
            'remember_token' => null
        ]);
    }

    public function saveResetToken(
        int $userId,
        string $token,
        string $expiry
    ) {
        return $this->update($userId, [
            'password_reset_token'  => $token,
            'password_reset_expiry' => $expiry
        ]);
    }

    public function clearResetToken(int $userId)
    {
        return $this->update($userId, [
            'password_reset_token'  => null,
            'password_reset_expiry' => null
        ]);
    }
}