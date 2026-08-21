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

    protected $useTimestamps = true;

    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    protected $validationRules = [];

    protected $validationMessages = [];

    protected $skipValidation = false;

    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;

    protected $beforeInsert = [
        'hashPassword',
        'generateFullName'
    ];

    protected $beforeUpdate = [

        'hashPassword',
        'generateFullName'

    ];

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

    protected function generateFullName(array $data)
    {
        $first = $data['data']['first_name'] ?? '';

        $last = $data['data']['last_name'] ?? '';

        if ($first !== '') {
            $data['data']['full_name'] = trim($first . ' ' . $last);
        }
        return $data;
    }

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

    public function saveResetToken(int $userId, string $token, string $expiry)
    {
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

    // public function getEmployees(
    //     int $page = 1,
    //     int $pageSize = 10,
    //     string $search = '',
    //     string $status = '',
    //     string $orderBy = 'id',
    //     string $direction = 'desc'
    // ): array {
    //     $builder = $this->builder();

    //     $builder
    //         ->select([
    //             'users.id',
    //             'users.organization_id',
    //             'users.branch_id',
    //             'users.department_id',
    //             'users.designation_id',
    //             'users.employee_code',
    //             'users.first_name',
    //             'users.last_name',
    //             'users.full_name',
    //             'users.email',
    //             'users.phone',
    //             'users.gender',
    //             'users.dob',
    //             'users.joining_date',
    //             'users.reporting_manager_id',
    //             'users.profile_photo',
    //             'users.status',
    //             'users.created_at',

    //             'organizations.name AS organization_name',
    //             'branches.name AS branch_name',
    //             'departments.name AS department_name',
    //             'designations.title AS designation_name',

    //             'manager.full_name AS reporting_manager_name'
    //         ])
    //         ->join(
    //             'organizations',
    //             'organizations.id = users.organization_id',
    //             'left'
    //         )
    //         ->join(
    //             'branches',
    //             'branches.id = users.branch_id',
    //             'left'
    //         )
    //         ->join(
    //             'departments',
    //             'departments.id = users.department_id',
    //             'left'
    //         )
    //         ->join(
    //             'designations',
    //             'designations.id = users.designation_id',
    //             'left'
    //         )
    //         ->join(
    //             'users manager',
    //             'manager.id = users.reporting_manager_id',
    //             'left'
    //         );

    //     if ($search !== '') {
    //         $builder->groupStart()
    //             ->like('users.employee_code', $search)
    //             ->orLike('users.first_name', $search)
    //             ->orLike('users.last_name', $search)
    //             ->orLike('users.full_name', $search)
    //             ->orLike('users.email', $search)
    //             ->orLike('users.phone', $search)
    //             ->groupEnd();
    //     }

    //     if ($status !== '') {
    //         $builder->where('users.status', $status);
    //     }

    //     $allowedOrderBy = [
    //         'id'              => 'users.id',
    //         'employee_code'   => 'users.employee_code',
    //         'full_name'       => 'users.full_name',
    //         'email'           => 'users.email',
    //         'joining_date'    => 'users.joining_date',
    //         'status'          => 'users.status',
    //         'created_at'      => 'users.created_at'
    //     ];

    //     $orderColumn = $allowedOrderBy[$orderBy]
    //         ?? 'users.id';

    //     $direction = strtolower($direction) === 'asc'
    //         ? 'ASC'
    //         : 'DESC';

    //     $builder->orderBy($orderColumn, $direction);

    //     $totalBuilder = clone $builder;

    //     $total = $totalBuilder->countAllResults();

    //     $offset = max(0, ($page - 1) * $pageSize);

    //     $data = $builder
    //         ->limit($pageSize, $offset)
    //         ->get()
    //         ->getResultArray();

    //     return [
    //         'data' => $data,
    //         'total' => $total,
    //         'page' => $page,
    //         'pageSize' => $pageSize,
    //         'lastPage' => $total > 0
    //             ? (int) ceil($total / $pageSize)
    //             : 1
    //     ];
    // }

    public function getEmployees(
        int $page = 1,
        int $pageSize = 10,
        string $search = '',
        string $status = '',
        string $orderBy = 'id',
        string $direction = 'desc'
    ): array {

        $builder = $this->builder();

        $builder
            ->select([
                'users.id',

                'users.organization_id',
                'users.branch_id',
                'users.department_id',
                'users.designation_id',

                'users.employee_code',

                'users.first_name',
                'users.last_name',
                'users.full_name',

                'users.email',
                'users.phone',

                'users.gender',
                'users.dob',
                'users.joining_date',

                'users.reporting_manager_id',

                'users.profile_photo',
                'users.status',
                'users.created_at',

                'organizations.name AS organization_name',
                'branches.name AS branch_name',
                'departments.name AS department_name',
                'designations.title AS designation_name',

                'manager.full_name AS reporting_manager_name',

                /*
             * Role
             */
                "(SELECT
                COALESCE(
                    NULLIF(r.display_name, ''),
                    r.name
                )
              FROM user_roles ur
              INNER JOIN roles r
                  ON r.id = ur.role_id
              WHERE ur.user_id = users.id
                AND r.status = 'active'
              ORDER BY
                  ur.assigned_at DESC,
                  ur.id DESC
              LIMIT 1
            ) AS role_name"
            ])
            ->join(
                'organizations',
                'organizations.id = users.organization_id',
                'left'
            )
            ->join(
                'branches',
                'branches.id = users.branch_id',
                'left'
            )
            ->join(
                'departments',
                'departments.id = users.department_id',
                'left'
            )
            ->join(
                'designations',
                'designations.id = users.designation_id',
                'left'
            )
            ->join(
                'users manager',
                'manager.id = users.reporting_manager_id',
                'left'
            );

        if ($search !== '') {

            $builder
                ->groupStart()
                ->like('users.employee_code', $search)
                ->orLike('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.full_name', $search)
                ->orLike('users.email', $search)
                ->orLike('users.phone', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where(
                'users.status',
                $status
            );
        }

        $allowedOrderBy = [
            'id'            => 'users.id',
            'employee_code' => 'users.employee_code',
            'full_name'     => 'users.full_name',
            'email'         => 'users.email',
            'joining_date'  => 'users.joining_date',
            'status'        => 'users.status',
            'created_at'    => 'users.created_at'
        ];

        $orderColumn =
            $allowedOrderBy[$orderBy]
            ?? 'users.id';

        $direction =
            strtolower($direction) === 'asc'
            ? 'ASC'
            : 'DESC';

        $builder->orderBy(
            $orderColumn,
            $direction
        );

        $totalBuilder = clone $builder;

        $total =
            $totalBuilder->countAllResults();

        $offset =
            max(
                0,
                ($page - 1) * $pageSize
            );

        $data =
            $builder
            ->limit(
                $pageSize,
                $offset
            )
            ->get()
            ->getResultArray();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'lastPage' => $total > 0
                ? (int) ceil(
                    $total / $pageSize
                )
                : 1
        ];
    }

    public function getEmployee(int $id): ?array
    {
        return $this->builder()
            ->select([
                'users.id',

                'users.organization_id',
                'users.branch_id',
                'users.department_id',
                'users.designation_id',

                'users.employee_code',

                'users.first_name',
                'users.last_name',
                'users.full_name',

                'users.email',
                'users.phone',

                'users.gender',
                'users.dob',
                'users.joining_date',

                'users.reporting_manager_id',

                'users.avatar',
                'users.profile_photo',

                'users.status',

                'users.created_at',
                'users.updated_at',

                'organizations.name AS organization_name',
                'branches.name AS branch_name',
                'departments.name AS department_name',
                'designations.title AS designation_name',

                'manager.full_name AS reporting_manager_name',
                "(SELECT
                        COALESCE(
                            NULLIF(r.display_name, ''),
                            r.name
                        )
                    FROM user_roles ur
                    INNER JOIN roles r
                        ON r.id = ur.role_id
                    WHERE ur.user_id = users.id
                        AND r.status = 'active'
                    ORDER BY
                        ur.assigned_at DESC,
                        ur.id DESC
                    LIMIT 1
                    ) AS role_name"
            ])
            ->join(
                'organizations',
                'organizations.id = users.organization_id',
                'left'
            )
            ->join(
                'branches',
                'branches.id = users.branch_id',
                'left'
            )
            ->join(
                'departments',
                'departments.id = users.department_id',
                'left'
            )
            ->join(
                'designations',
                'designations.id = users.designation_id',
                'left'
            )
            ->join(
                'users manager',
                'manager.id = users.reporting_manager_id',
                'left'
            )
            ->where(
                'users.id',
                $id
            )
            ->get()
            ->getRowArray();
    }
}
