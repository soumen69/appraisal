<?php

namespace App\Controllers\Admin;

use App\Services\RoleService;
use App\Validation\Requests\RoleRequest;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\PermissionService;

class RoleController extends BaseCrudController
{
    protected RoleService $roleService;
    protected PermissionService $permissionService;

    public function __construct()
    {
        $this->roleService = new RoleService();
        $this->permissionService = new PermissionService();
    }

    public function index()
    {
        return view('roles/index', [
            'title'         => 'Roles',
            'page_title'    => 'Role Management',
            'page_subtitle' => 'Manage application roles and access foundation.'
        ]);
    }

    public function list()
    {
        $filters = [
            'page'      => $this->request->getGet('page'),
            'perPage'   => $this->request->getGet('pageSize'),
            'search'    => $this->request->getGet('search'),
            'status'    => $this->request->getGet('status'),
            'sortBy'    => $this->request->getGet('orderBy'),
            'direction' => $this->request->getGet('direction')
        ];

        $data = $this->roleService->getPaginated($filters);
        return $this->success(
            '',
            $data
        );
    }

    public function store()
    {
        if (! $this->validate(RoleRequest::rules())) {
            return $this->validationFailed();
        }

        try {
            $id = $this->roleService->create(

                $this->request->getPost()

            );

            return $this->success(
                'Role created successfully.',
                [
                    'id'     => $id,
                    'reload' => true
                ],
                ResponseInterface::HTTP_CREATED
            );
        } catch (\Throwable $e) {
            return $this->error(

                $e->getMessage()

            );
        }
    }

    public function edit($id)
    {
        $module = $this->roleService->getById($id);
        if (! $module) {
            return $this->error(
                'Role not found.',
                [],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        return $this->success(

            '',

            $module

        );
    }

    public function update($id)
    {
        if (! $this->validate(RoleRequest::rules($id))) {
            return $this->validationFailed();
        }
        try {
            $this->roleService->update($id, $this->request->getPost());
            return $this->success(
                'Role updated successfully.',
                ['reload' => true]
            );
        } catch (\Throwable $e) {
            return $this->error(
                $e->getMessage()
            );
        }
    }

    public function delete($id)
    {
        try {
            $this->roleService->delete($id);
            return $this->success(
                'Role deleted successfully.',
                [
                    'reload' => true
                ]
            );
        } catch (\Throwable $e) {
            return $this->error(
                $e->getMessage()
            );
        }
    }

    public function options()
    {
        return $this->success('', [
            'roles' => $this->roleService->getOptions()
        ]);
    }

    public function permissions($id)
    {
        $role = $this->roleService->getById((int) $id);
        if (!$role) {
            return redirect()
                ->to('/roles')
                ->with('error', 'Role not found.');
        }

        return view('roles/permissions', [
            'title'         => 'Role Permissions',
            'page_title'    => 'Manage Role Permissions',
            'page_subtitle' => 'Configure access for ' . ($role['display_name'] ?: $role['name']),
            'role'          => $role
        ]);
    }

    public function permissionData($id)
    {
        $role = $this->roleService->getById((int) $id);
        if (!$role) {
            return $this->error('Role not found.');
        }

        return $this->success('', [
            'role'        => $role,
            'permissions' => $this->permissionService->getGrouped(),
            'assigned'    => $this->permissionService->getRolePermissionIds((int) $id)
        ]);
    }

    public function updatePermissions($id)
    {
        try {
            $this->roleService->savePermissions(

                (int)$id,

                $this->request->getPost('permissions') ?? []

            );

            return $this->success(

                'Permissions updated successfully.'

            );
        } catch (\Throwable $e) {
            return $this->error(

                $e->getMessage()

            );
        }
    }
}
