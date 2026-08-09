<?php

namespace App\Services;

use App\Models\UserModel;
use RuntimeException;
use InvalidArgumentException;

class EmployeeService
{
    protected UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    /*
    |--------------------------------------------------------------------------
    | Employee Listing
    |--------------------------------------------------------------------------
    */

    public function getEmployees(array $filters = []): array
    {
        $page = max(
            1,
            (int) ($filters['page'] ?? 1)
        );

        $pageSize = (int) ($filters['pageSize'] ?? 10);

        if (!in_array($pageSize, [10, 25, 50, 100], true)) {
            $pageSize = 10;
        }

        return $this->users->getEmployees(
            $page,
            $pageSize,
            trim($filters['search'] ?? ''),
            trim($filters['status'] ?? ''),
            trim($filters['orderBy'] ?? 'id'),
            trim($filters['direction'] ?? 'desc')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Employee Details
    |--------------------------------------------------------------------------
    */

    public function getEmployee(int $id): ?array
    {
        return $this->users->getEmployee($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Employee Roles
    |--------------------------------------------------------------------------
    */

    public function getEmployeeRoles(int $userId): array
    {
        return db_connect()
            ->table('user_roles')
            ->select([
                'role_id',
                'is_primary'
            ])
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Create Employee
    |--------------------------------------------------------------------------
    */

    public function createEmployee(array $data): int
    {
        $errors = $this->validateEmployeeData($data);

        if (!empty($errors)) {
            throw new InvalidArgumentException(
                json_encode($errors)
            );
        }

        $email = strtolower(
            trim($data['email'])
        );

        /*
        |--------------------------------------------------------------------------
        | Duplicate Email
        |--------------------------------------------------------------------------
        */

        $existingEmail = $this->users
            ->where('email', $email)
            ->first();

        if ($existingEmail) {
            throw new InvalidArgumentException(
                json_encode([
                    'email' => 'This email address is already in use.'
                ])
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Employee Code
        |--------------------------------------------------------------------------
        */

        if (!empty($data['employee_code'])) {

            $employeeCode = trim(
                $data['employee_code']
            );

            $existingCode = $this->users
                ->where(
                    'employee_code',
                    $employeeCode
                )
                ->first();

            if ($existingCode) {
                throw new InvalidArgumentException(
                    json_encode([
                        'employee_code' =>
                        'This employee code is already in use.'
                    ])
                );
            }
        }

        $roleIds = $this->normalizeRoleIds(
            $data['role_ids'] ?? []
        );

        $primaryRoleId = (int) (
            $data['primary_role_id'] ?? 0
        );

        $this->validateRoles(
            $roleIds,
            $primaryRoleId
        );

        /*
        |--------------------------------------------------------------------------
        | Reporting Manager
        |--------------------------------------------------------------------------
        */

        $reportingManagerId = !empty($data['reporting_manager_id'])
            ? (int) $data['reporting_manager_id']
            : null;

        if ($reportingManagerId !== null) {
            $this->validateReportingManager(
                $reportingManagerId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        $db = db_connect();

        $db->transBegin();

        try {

            $employeeData = $this->buildEmployeeData(
                $data
            );

            $userId = $this->users->insert(
                $employeeData,
                true
            );

            if (!$userId) {
                throw new RuntimeException(
                    'Unable to create employee.'
                );
            }

            $this->syncRoles(
                (int) $userId,
                $roleIds,
                $primaryRoleId
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException(
                    'Employee creation failed.'
                );
            }

            $db->transCommit();

            return (int) $userId;
        } catch (\Throwable $e) {

            $db->transRollback();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Employee
    |--------------------------------------------------------------------------
    */

    public function updateEmployee(
        int $id,
        array $data
    ): void {

        $employee = $this->users->find($id);

        if (!$employee) {
            throw new RuntimeException(
                'Employee not found.'
            );
        }

        $errors = $this->validateEmployeeData(
            $data,
            $id
        );

        if (!empty($errors)) {
            throw new InvalidArgumentException(
                json_encode($errors)
            );
        }

        $email = strtolower(
            trim($data['email'])
        );

        /*
        |--------------------------------------------------------------------------
        | Duplicate Email
        |--------------------------------------------------------------------------
        */

        $existingEmail = $this->users
            ->where('email', $email)
            ->where('id !=', $id)
            ->first();

        if ($existingEmail) {
            throw new InvalidArgumentException(
                json_encode([
                    'email' =>
                    'This email address is already in use.'
                ])
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Employee Code
        |--------------------------------------------------------------------------
        */

        if (!empty($data['employee_code'])) {

            $employeeCode = trim(
                $data['employee_code']
            );

            $existingCode = $this->users
                ->where(
                    'employee_code',
                    $employeeCode
                )
                ->where('id !=', $id)
                ->first();

            if ($existingCode) {
                throw new InvalidArgumentException(
                    json_encode([
                        'employee_code' =>
                        'This employee code is already in use.'
                    ])
                );
            }
        }

        $roleIds = $this->normalizeRoleIds(
            $data['role_ids'] ?? []
        );

        $primaryRoleId = (int) (
            $data['primary_role_id'] ?? 0
        );

        $this->validateRoles(
            $roleIds,
            $primaryRoleId
        );

        /*
        |--------------------------------------------------------------------------
        | Reporting Manager
        |--------------------------------------------------------------------------
        */

        $reportingManagerId = !empty($data['reporting_manager_id'])
            ? (int) $data['reporting_manager_id']
            : null;

        if ($reportingManagerId !== null) {

            if ($reportingManagerId === $id) {
                throw new InvalidArgumentException(
                    json_encode([
                        'reporting_manager_id' =>
                        'An employee cannot report to themselves.'
                    ])
                );
            }

            $this->validateReportingManager(
                $reportingManagerId
            );
        }

        $db = db_connect();

        $db->transBegin();

        try {

            $employeeData = $this->buildEmployeeData(
                $data,
                true
            );

            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            |
            | Only update password when explicitly supplied.
            |
            */

            if (
                isset($data['password']) &&
                trim($data['password']) !== ''
            ) {
                $employeeData['password'] =
                    $data['password'];
            }

            $updated = $this->users->update(
                $id,
                $employeeData
            );

            if (!$updated) {
                throw new RuntimeException(
                    'Unable to update employee.'
                );
            }

            $this->syncRoles(
                $id,
                $roleIds,
                $primaryRoleId
            );

            if ($db->transStatus() === false) {
                throw new RuntimeException(
                    'Employee update failed.'
                );
            }

            $db->transCommit();
        } catch (\Throwable $e) {

            $db->transRollback();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Employee
    |--------------------------------------------------------------------------
    */

    public function deleteEmployee(int $id): void
    {
        $employee = $this->users->find($id);

        if (!$employee) {
            throw new RuntimeException(
                'Employee not found.'
            );
        }

        if (
            (int) session()->get('user_id') === $id
        ) {
            throw new RuntimeException(
                'You cannot delete your own account.'
            );
        }

        $db = db_connect();

        $db->transBegin();

        try {

            $db->table('user_permissions')
                ->where('user_id', $id)
                ->delete();

            $db->table('user_roles')
                ->where('user_id', $id)
                ->delete();

            if (!$this->users->delete($id)) {
                throw new RuntimeException(
                    'Unable to delete employee.'
                );
            }

            if ($db->transStatus() === false) {
                throw new RuntimeException(
                    'Employee deletion failed.'
                );
            }

            $db->transCommit();
        } catch (\Throwable $e) {

            $db->transRollback();

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(int $id): string
    {
        $employee = $this->users->find($id);

        if (!$employee) {
            throw new RuntimeException(
                'Employee not found.'
            );
        }

        if (
            (int) session()->get('user_id') === $id
        ) {
            throw new RuntimeException(
                'You cannot change your own account status.'
            );
        }

        $status = $employee['status'] === 'active'
            ? 'inactive'
            : 'active';

        $this->users->update(
            $id,
            [
                'status' => $status
            ]
        );

        return $status;
    }

    /*
    |--------------------------------------------------------------------------
    | Build Employee Data
    |--------------------------------------------------------------------------
    */

    protected function buildEmployeeData(
        array $data,
        bool $isUpdate = false
    ): array {

        $employeeData = [

            'organization_id' =>
            (int) $data['organization_id'],

            'branch_id' =>
            !empty($data['branch_id'])
                ? (int) $data['branch_id']
                : null,

            'department_id' =>
            !empty($data['department_id'])
                ? (int) $data['department_id']
                : null,

            'designation_id' =>
            !empty($data['designation_id'])
                ? (int) $data['designation_id']
                : null,

            'employee_code' =>
            !empty($data['employee_code'])
                ? trim($data['employee_code'])
                : null,

            'first_name' =>
            trim($data['first_name']),

            'last_name' =>
            trim($data['last_name'] ?? ''),

            'email' =>
            strtolower(trim($data['email'])),

            'phone' =>
            !empty($data['phone'])
                ? trim($data['phone'])
                : null,

            'gender' =>
            !empty($data['gender'])
                ? trim($data['gender'])
                : null,

            'dob' =>
            !empty($data['dob'])
                ? $data['dob']
                : null,

            'joining_date' =>
            !empty($data['joining_date'])
                ? $data['joining_date']
                : null,

            'reporting_manager_id' =>
            !empty($data['reporting_manager_id'])
                ? (int) $data['reporting_manager_id']
                : null,

            'status' =>
            !empty($data['status'])
                ? $data['status']
                : 'active'
        ];

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if (
            !$isUpdate &&
            !empty($data['password'])
        ) {
            $employeeData['password'] =
                $data['password'];
        }

        /*
        |--------------------------------------------------------------------------
        | Profile Photo
        |--------------------------------------------------------------------------
        */

        if (
            isset($data['profile_photo']) &&
            $data['profile_photo'] !== ''
        ) {
            $employeeData['profile_photo'] =
                $data['profile_photo'];
        }

        return $employeeData;
    }

    /*
    |--------------------------------------------------------------------------
    | Role Synchronization
    |--------------------------------------------------------------------------
    */

    protected function syncRoles(
        int $userId,
        array $roleIds,
        int $primaryRoleId
    ): void {

        $db = db_connect();

        $db->table('user_roles')
            ->where('user_id', $userId)
            ->delete();

        $rows = [];

        foreach ($roleIds as $roleId) {

            $rows[] = [
                'user_id' => $userId,
                'role_id' => $roleId,
                'is_primary' =>
                $roleId === $primaryRoleId ? 1 : 0
            ];
        }

        if (!empty($rows)) {
            $db->table('user_roles')
                ->insertBatch($rows);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Role Validation
    |--------------------------------------------------------------------------
    */

    protected function validateRoles(
        array $roleIds,
        int $primaryRoleId
    ): void {

        if (empty($roleIds)) {
            throw new InvalidArgumentException(
                json_encode([
                    'role_ids' =>
                    'At least one role must be assigned.'
                ])
            );
        }

        if (
            !in_array(
                $primaryRoleId,
                $roleIds,
                true
            )
        ) {
            throw new InvalidArgumentException(
                json_encode([
                    'primary_role_id' =>
                    'Primary role must be one of the assigned roles.'
                ])
            );
        }

        $validRoles = db_connect()
            ->table('roles')
            ->select('id')
            ->whereIn('id', $roleIds)
            ->where('status', 'active')
            ->get()
            ->getResultArray();

        $validRoleIds = array_map(
            'intval',
            array_column($validRoles, 'id')
        );

        if (
            count($validRoleIds) !== count($roleIds)
        ) {
            throw new InvalidArgumentException(
                json_encode([
                    'role_ids' =>
                    'One or more selected roles are invalid.'
                ])
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Role IDs
    |--------------------------------------------------------------------------
    */

    protected function normalizeRoleIds(
        mixed $roleIds
    ): array {

        if (!is_array($roleIds)) {
            $roleIds = [$roleIds];
        }

        $roleIds = array_filter(
            array_map('intval', $roleIds),
            fn($id) => $id > 0
        );

        return array_values(
            array_unique($roleIds)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reporting Manager Validation
    |--------------------------------------------------------------------------
    */

    protected function validateReportingManager(
        int $managerId
    ): void {

        $manager = $this->users
            ->select('id')
            ->where('id', $managerId)
            ->where('status', 'active')
            ->first();

        if (!$manager) {
            throw new InvalidArgumentException(
                json_encode([
                    'reporting_manager_id' =>
                    'Selected reporting manager is invalid.'
                ])
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Employee Validation
    |--------------------------------------------------------------------------
    */

    protected function validateEmployeeData(
        array $data,
        ?int $employeeId = null
    ): array {

        $errors = [];

        if (
            empty($data['organization_id']) ||
            !is_numeric($data['organization_id'])
        ) {
            $errors['organization_id'] =
                'Organization is required.';
        }

        if (
            empty($data['first_name']) ||
            trim($data['first_name']) === ''
        ) {
            $errors['first_name'] =
                'First name is required.';
        }

        if (
            empty($data['email']) ||
            !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors['email'] =
                'A valid email address is required.';
        }

        if (
            empty($data['role_ids'])
        ) {
            $errors['role_ids'] =
                'At least one role is required.';
        }

        if (
            empty($data['primary_role_id'])
        ) {
            $errors['primary_role_id'] =
                'Primary role is required.';
        }

        if (
            !empty($data['status']) &&
            !in_array(
                $data['status'],
                ['active', 'inactive'],
                true
            )
        ) {
            $errors['status'] =
                'Invalid employee status.';
        }

        if (
            !empty($data['dob']) &&
            !strtotime($data['dob'])
        ) {
            $errors['dob'] =
                'Invalid date of birth.';
        }

        if (
            !empty($data['joining_date']) &&
            !strtotime($data['joining_date'])
        ) {
            $errors['joining_date'] =
                'Invalid joining date.';
        }

        if (
            isset($data['password']) &&
            trim($data['password']) !== '' &&
            strlen($data['password']) < 8
        ) {
            $errors['password'] =
                'Password must be at least 8 characters.';
        }

        if (
            !empty($data['reporting_manager_id']) &&
            $employeeId !== null &&
            (int) $data['reporting_manager_id'] === $employeeId
        ) {
            $errors['reporting_manager_id'] =
                'Employee cannot report to themselves.';
        }

        return $errors;
    }
}
