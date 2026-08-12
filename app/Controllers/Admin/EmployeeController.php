<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\EmployeeService;
use InvalidArgumentException;
use Throwable;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\UserModel;

class EmployeeController extends BaseController
{
    protected EmployeeService $employeeService;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->employeeService = new EmployeeService();
        $this->userModel = new UserModel();
    }

    //Employee Listing Page
    public function index()
    {
        return view('employees/index', [
            'title' => 'Employees',
            'page_title' => 'Employees',
            'page_subtitle' => 'Manage your employees and their actions.'
        ]);
    }

    //employee list
    public function list()
    {
        try {

            $data =
                $this->employeeService
                ->getEmployees(
                    $this->request->getGet()
                );

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Employee list error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load employees.'
                ]);
        }
    }

    //create page
    public function create()
    {
        return view(
            'employees/create',
            [
                'title' => 'Create Employee',
                'page_title' => 'Create Employee',
                'page_subtitle' => 'Add a new employee and configure their system access.'
            ]
        );
    }

    //store
    public function store()
    {
        try {

            $id =
                $this->employeeService
                ->createEmployee(
                    $this->request->getPost()
                );

            return $this->response->setJSON([
                'success' => true,
                'message' =>
                'Employee created successfully.',
                'data' => [
                    'id' => $id
                ]
            ]);
        } catch (InvalidArgumentException $e) {

            $errors =
                json_decode(
                    $e->getMessage(),
                    true
                );

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Please correct the highlighted fields.',
                    'errors' =>
                    is_array($errors)
                        ? $errors
                        : []
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Employee create error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to create employee.'
                ]);
        }
    }

    //edit page
    public function editPage(int $id)
    {
        $employee =
            $this->employeeService
            ->getEmployee($id);

        if (!$employee) {
            throw PageNotFoundException::forPageNotFound(
                'Employee not found.'
            );
        }

        return view(
            'employees/edit',
            [
                'title' => 'Edit Employee',
                'employee' => $employee
            ]
        );
    }

    public function edit(int $id)
    {
        try {

            $employee =
                $this->employeeService
                ->getEmployee($id);

            if (!$employee) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Employee not found.'
                    ]);
            }

            $employee['role_id'] =
                $this->employeeService
                ->getEmployeeRoleId($id);

            unset(
                $employee['password'],
                $employee['password_reset_token'],
                $employee['password_reset_expiry'],
                $employee['remember_token']
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $employee
            ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Employee details error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load employee.'
                ]);
        }
    }

    //update
    public function update(int $id)
    {
        try {

            $this->employeeService
                ->updateEmployee(
                    $id,
                    $this->request->getPost()
                );

            return $this->response->setJSON([
                'success' => true,
                'message' =>
                'Employee updated successfully.'
            ]);
        } catch (InvalidArgumentException $e) {

            $errors =
                json_decode(
                    $e->getMessage(),
                    true
                );

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Please correct the highlighted fields.',
                    'errors' =>
                    is_array($errors)
                        ? $errors
                        : []
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Employee update error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to update employee.'
                ]);
        }
    }

    //delete
    public function delete(int $id)
    {
        try {

            $this->employeeService
                ->deleteEmployee($id);

            return $this->response->setJSON([
                'success' => true,
                'message' =>
                'Employee deleted successfully.'
            ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Employee delete error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    $e->getMessage()
                ]);
        }
    }

    //status
    public function toggleStatus(int $id)
    {
        try {

            $status =
                $this->employeeService
                ->toggleStatus($id);

            return $this->response->setJSON([
                'success' => true,
                'message' =>
                $status === 'active'
                    ? 'Employee activated successfully.'
                    : 'Employee deactivated successfully.',
                'data' => [
                    'status' => $status
                ]
            ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Employee status error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    $e->getMessage()
                ]);
        }
    }

    public function view(int $id)
    {
        $employee = $this->employeeService->getEmployee($id);

        if (!$employee) {
            throw PageNotFoundException::forPageNotFound(
                'Employee not found.'
            );
        }

        $roles = $this->employeeService->getEmployeeRoleID($id);

        return view('employees/view', [
            'title' => 'Employee Details',
            'employee' => $employee,
            'roles' => $roles
        ]);
    }

    public function details($id)
    {
        try {

            $employee = $this->employeeService->getEmployee((int) $id);

            if (!$employee) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Employee not found.'
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data'    => $employee
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Employee details failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load employee details.'
                ]);
        }
    }

    public function options()
    {
        try {
            $organizationId = (int) $this->request->getGet('organization_id');
            $excludeId      = (int) $this->request->getGet('exclude_id');

            $builder = $this->userModel
                ->select([
                    'id',
                    'organization_id',
                    'employee_code',
                    'first_name',
                    'last_name',
                    'full_name',
                    'status'
                ])
                ->where('status', 'active');

            if ($organizationId > 0) {
                $builder->where(
                    'organization_id',
                    $organizationId
                );
            }

            if ($excludeId > 0) {
                $builder->where(
                    'id !=',
                    $excludeId
                );
            }

            $employees = $builder
                ->orderBy('full_name', 'ASC')
                ->findAll();

            $data = array_map(
                static function (array $employee): array {
                    return [
                        'id'              => (int) $employee['id'],
                        'organization_id' => (int) $employee['organization_id'],
                        'employee_code'   => $employee['employee_code'],
                        'name'            => $employee['full_name']
                            ?: trim(
                                ($employee['first_name'] ?? '') . ' ' .
                                    ($employee['last_name'] ?? '')
                            ),
                    ];
                },
                $employees
            );

            return $this->response->setJSON([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Employee options failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load employees.',
                ]);
        }
    }
}
