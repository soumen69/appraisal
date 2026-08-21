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

    public function getEmployee(int $id): ?array
    {
        return $this->users->getEmployee($id);
    }

    public function getEmployeeRoleId(int $userId): ?int
    {
        $row = db_connect()
            ->table('user_roles')
            ->select('role_id')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        return $row
            ? (int) $row['role_id']
            : null;
    }

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

        $this->validateEmploymentReferences(
            $data
        );

        $roleId = (int) (
            $data['role_id'] ?? 0
        );

        $this->validateRole(
            $roleId
        );

        $reportingManagerId =
            !empty($data['reporting_manager_id'])
            ? (int) $data['reporting_manager_id']
            : null;

        if ($reportingManagerId !== null) {

            $this->validateReportingManager(
                $reportingManagerId
            );
        }

        $db = db_connect();

        $db->transBegin();

        try {

            $employeeData =
                $this->buildEmployeeData(
                    $data
                );


            $userId =
                $this->users->insert(
                    $employeeData,
                    true
                );


            if (!$userId) {

                throw new RuntimeException(
                    'Unable to create employee.'
                );
            }


            $this->syncRole(
                (int) $userId,
                $roleId
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

    public function updateEmployee(int $id, array $data): void
    {
        $employee =
            $this->users->find($id);


        if (!$employee) {

            throw new RuntimeException(
                'Employee not found.'
            );
        }


        $errors =
            $this->validateEmployeeData(
                $data,
                $id
            );


        if (!empty($errors)) {

            throw new InvalidArgumentException(
                json_encode($errors)
            );
        }


        $email =
            strtolower(
                trim($data['email'])
            );

        $existingEmail =
            $this->users
            ->where(
                'email',
                $email
            )
            ->where(
                'id !=',
                $id
            )
            ->first();


        if ($existingEmail) {

            throw new InvalidArgumentException(
                json_encode([
                    'email' =>
                    'This email address is already in use.'
                ])
            );
        }

        if (!empty($data['employee_code'])) {

            $employeeCode =
                trim(
                    $data['employee_code']
                );


            $existingCode =
                $this->users
                ->where(
                    'employee_code',
                    $employeeCode
                )
                ->where(
                    'id !=',
                    $id
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

        $this->validateEmploymentReferences(
            $data
        );

        $roleId =
            (int) (
                $data['role_id'] ?? 0
            );


        $this->validateRole(
            $roleId
        );

        $reportingManagerId =
            !empty($data['reporting_manager_id'])
            ? (int) $data['reporting_manager_id']
            : null;


        if ($reportingManagerId !== null) {

            if (
                $reportingManagerId === $id
            ) {

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

            unset(
                $data['password'],
                $data['password_confirmation']
            );


            $employeeData =
                $this->buildEmployeeData(
                    $data,
                    true
                );

            $updated =
                $this->users->update(
                    $id,
                    $employeeData
                );


            if (!$updated) {

                throw new RuntimeException(
                    'Unable to update employee.'
                );
            }

            $this->syncRole(
                $id,
                $roleId
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
            trim(
                $data['first_name']
            ),

            'last_name' =>
            trim(
                $data['last_name'] ?? ''
            ),

            'email' =>
            strtolower(
                trim($data['email'])
            ),

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

        if (
            !$isUpdate &&
            !empty($data['password'])
        ) {

            $employeeData['password'] =
                $data['password'];
        }

        if (
            isset($data['profile_photo']) &&
            $data['profile_photo'] !== ''
        ) {

            $employeeData['profile_photo'] =
                $data['profile_photo'];
        }


        return $employeeData;
    }

    protected function syncRole(
        int $userId,
        int $roleId
    ): void {

        $db = db_connect();

        $db->table('user_roles')
            ->where('user_id', $userId)
            ->delete();

        $db->table('user_roles')
            ->insert([
                'user_id' => $userId,
                'role_id' => $roleId
            ]);
    }

    protected function validateRole(
        int $roleId
    ): void {

        if ($roleId <= 0) {

            throw new InvalidArgumentException(
                json_encode([
                    'role_id' =>
                    'Role is required.'
                ])
            );
        }


        $role =
            db_connect()
            ->table('roles')
            ->select([
                'id',
                'status'
            ])
            ->where(
                'id',
                $roleId
            )
            ->get()
            ->getRowArray();


        if (!$role) {

            throw new InvalidArgumentException(
                json_encode([
                    'role_id' =>
                    'Selected role is invalid.'
                ])
            );
        }


        if (
            ($role['status'] ?? null) !==
            'active'
        ) {

            throw new InvalidArgumentException(
                json_encode([
                    'role_id' =>
                    'Selected role is inactive.'
                ])
            );
        }
    }

    protected function validateEmploymentReferences(
        array $data
    ): void {

        $db = db_connect();

        $organizationId =
            (int) (
                $data['organization_id'] ?? 0
            );


        if ($organizationId <= 0) {

            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Organization is required.'
                ])
            );
        }


        $organization =
            $db->table('organizations')
            ->select([
                'id',
                'status'
            ])
            ->where(
                'id',
                $organizationId
            )
            ->get()
            ->getRowArray();


        if (!$organization) {

            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Selected organization is invalid.'
                ])
            );
        }


        if (
            ($organization['status'] ?? null) !==
            'active'
        ) {

            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Selected organization is inactive.'
                ])
            );
        }

        if (!empty($data['branch_id'])) {

            $branchId =
                (int) $data['branch_id'];


            $branch =
                $db->table('branches')
                ->select([
                    'id',
                    'organization_id',
                    'status'
                ])
                ->where(
                    'id',
                    $branchId
                )
                ->get()
                ->getRowArray();


            if (!$branch) {

                throw new InvalidArgumentException(
                    json_encode([
                        'branch_id' =>
                        'Selected branch is invalid.'
                    ])
                );
            }


            if (
                (int) $branch['organization_id'] !==
                $organizationId
            ) {

                throw new InvalidArgumentException(
                    json_encode([
                        'branch_id' =>
                        'Selected branch does not belong to the selected organization.'
                    ])
                );
            }


            if (
                isset($branch['status']) &&
                $branch['status'] !== 'active'
            ) {

                throw new InvalidArgumentException(
                    json_encode([
                        'branch_id' =>
                        'Selected branch is inactive.'
                    ])
                );
            }
        }

        if (!empty($data['department_id'])) {

            $departmentId =
                (int) $data['department_id'];


            $department =
                $db->table('departments')
                ->select([
                    'id',
                    'organization_id',
                    'status'
                ])
                ->where(
                    'id',
                    $departmentId
                )
                ->get()
                ->getRowArray();


            if (!$department) {

                throw new InvalidArgumentException(
                    json_encode([
                        'department_id' =>
                        'Selected department is invalid.'
                    ])
                );
            }


            if (
                (int) $department['organization_id'] !==
                $organizationId
            ) {

                throw new InvalidArgumentException(
                    json_encode([
                        'department_id' =>
                        'Selected department does not belong to the selected organization.'
                    ])
                );
            }


            if (
                isset($department['status']) &&
                $department['status'] !== 'active'
            ) {

                throw new InvalidArgumentException(
                    json_encode([
                        'department_id' =>
                        'Selected department is inactive.'
                    ])
                );
            }
        }

        if (!empty($data['designation_id'])) {

            $designationId =
                (int) $data['designation_id'];


            $designation =
                $db->table('designations')
                ->select([
                    'id',
                    'organization_id',
                    'status'
                ])
                ->where(
                    'id',
                    $designationId
                )
                ->get()
                ->getRowArray();


            if (!$designation) {

                throw new InvalidArgumentException(
                    json_encode([
                        'designation_id' =>
                        'Selected designation is invalid.'
                    ])
                );
            }


            if (
                (int) $designation['organization_id'] !==
                $organizationId
            ) {

                throw new InvalidArgumentException(
                    json_encode([
                        'designation_id' =>
                        'Selected designation does not belong to the selected organization.'
                    ])
                );
            }


            if (
                isset($designation['status']) &&
                $designation['status'] !== 'active'
            ) {

                throw new InvalidArgumentException(
                    json_encode([
                        'designation_id' =>
                        'Selected designation is inactive.'
                    ])
                );
            }
        }
    }

    protected function validateReportingManager(
        int $managerId
    ): void {

        $manager =
            $this->users
            ->select([
                'id',
                'status'
            ])
            ->where(
                'id',
                $managerId
            )
            ->where(
                'status',
                'active'
            )
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
            empty($data['role_id'])
        ) {

            $errors['role_id'] =
                'Role is required.';
        }

        if (
            !empty($data['status']) &&
            !in_array(
                $data['status'],
                [
                    'active',
                    'inactive'
                ],
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
            $employeeId === null
        ) {

            $password =
                trim(
                    (string) (
                        $data['password'] ?? ''
                    )
                );

            $confirmation =
                trim(
                    (string) (
                        $data['password_confirmation'] ?? ''
                    )
                );


            if ($password === '') {

                $errors['password'] =
                    'Password is required.';
            } elseif (
                strlen($password) < 8
            ) {

                $errors['password'] =
                    'Password must be at least 8 characters.';
            }


            if ($confirmation === '') {

                $errors['password_confirmation'] =
                    'Please confirm the password.';
            } elseif (
                $password !== $confirmation
            ) {

                $errors['password_confirmation'] =
                    'Passwords do not match.';
            }
        }

        if (
            !empty($data['reporting_manager_id']) &&
            $employeeId !== null &&
            (int) $data['reporting_manager_id'] ===
            $employeeId
        ) {

            $errors['reporting_manager_id'] =
                'Employee cannot report to themselves.';
        }


        return $errors;
    }
}
