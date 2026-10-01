<?php

namespace App\Controllers\Admin;

use App\Models\RoleModel;
use App\Validation\Requests\RoleRequest;
use App\Services\PermissionService;
use CodeIgniter\HTTP\ResponseInterface;

class RoleController extends BaseCrudController
{
    protected RoleModel $roles;
    protected PermissionService $permissionService;

    public function __construct()
    {
        $this->roles = new RoleModel();
        $this->permissionService = new PermissionService();
    }

    public function index()
    {
        return view('roles/index', [
            'title' => 'Roles',
            'page_title' => 'Role Management',
            'page_subtitle' => 'Manage application roles and access.'
        ]);
    }

    public function list()
    {
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $perPage = min(100, max(1, (int) ($this->request->getGet('pageSize') ?: 10)));
        $search = trim((string) $this->request->getGet('search'));
        $status = trim((string) $this->request->getGet('status'));
        $sortBy = (string) $this->request->getGet('orderBy');
        $direction = strtolower((string) $this->request->getGet('direction')) === 'desc' ? 'DESC' : 'ASC';

        $allowedSorts = ['name', 'display_name', 'sort_order', 'status', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts, true) ? $sortBy : 'sort_order';

        $builder = $this->roles->builder();
        $builder->select('roles.*, (SELECT COUNT(*) FROM role_permissions WHERE role_permissions.role_id = roles.id) AS permission_count', false);

        if ($search !== '') {
            $builder->groupStart()
                ->like('roles.name', $search)
                ->orLike('roles.display_name', $search)
                ->orLike('roles.slug', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('roles.status', $status);
        }

        $total = $builder->countAllResults(false);

        $rows = $builder
            ->orderBy('roles.' . $sortBy, $direction)
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['id'] = (int) $row['id'];
            $row['permission_count'] = (int) $row['permission_count'];
            $row['is_system'] = (int) $row['is_system'];
            $row['is_super'] = (int) $row['is_super'];
        }
        unset($row);

        return $this->success('', [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'lastPage' => $perPage > 0 ? (int) ceil($total / $perPage) : 1
        ]);
    }

    public function store()
    {
        if (!$this->validate(RoleRequest::rules())) {
            return $this->validationFailed();
        }

        try {
            $data = $this->request->getPost();
            $slug = trim((string) ($data['slug'] ?? ''));

            if ($slug === '') {
                throw new \RuntimeException('Role slug is required.');
            }

            if ($this->roles->where('slug', $slug)->first()) {
                throw new \RuntimeException('Role slug already exists.');
            }

            $data['icon'] = trim((string) ($data['icon'] ?? '')) ?: null;
            $data['description'] = trim((string) ($data['description'] ?? '')) ?: null;
            $data['color'] = trim((string) ($data['color'] ?? '')) ?: null;
            $data['is_super'] = 0;

            $id = $this->roles->insert($data, true);

            if (!$id) {
                throw new \RuntimeException('Unable to create role.');
            }

            return $this->success(
                'Role created successfully.',
                ['id' => $id, 'reload' => true],
                ResponseInterface::HTTP_CREATED
            );
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    public function edit($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return $this->error(
                'Invalid role.',
                [],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $role = $this->roles->find($id);

        if (!$role) {
            return $this->error(
                'Role not found.',
                [],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        return $this->success('', $role);
    }

    public function update($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return $this->error(
                'Invalid role.',
                [],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $role = $this->roles->find($id);

        if (!$role) {
            return $this->error(
                'Role not found.',
                [],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        if ((int) $role['is_super'] === 1) {
            return $this->error('Super Admin cannot be modified.');
        }

        if (!$this->validate(RoleRequest::rules($id))) {
            return $this->validationFailed();
        }

        try {
            $data = $this->request->getPost();
            $slug = trim((string) ($data['slug'] ?? ''));

            if ($slug === '') {
                throw new \RuntimeException('Role slug is required.');
            }

            if ((int) $role['is_system'] === 1) {
                unset($data['slug'], $data['name'], $data['is_super']);
            } else {
                $existing = $this->roles
                    ->where('slug', $slug)
                    ->where('id !=', $id)
                    ->first();

                if ($existing) {
                    throw new \RuntimeException('Role slug already exists.');
                }
            }

            $data['icon'] = trim((string) ($data['icon'] ?? '')) ?: null;
            $data['description'] = trim((string) ($data['description'] ?? '')) ?: null;
            $data['color'] = trim((string) ($data['color'] ?? '')) ?: null;
            unset($data['is_super']);

            if (!$this->roles->update($id, $data)) {
                throw new \RuntimeException('Unable to update role.');
            }

            return $this->success(
                'Role updated successfully.',
                ['reload' => true]
            );
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $id = (int) $id;

            if ($id <= 0) {
                throw new \RuntimeException('Invalid role.');
            }

            $role = $this->roles->find($id);

            if (!$role) {
                throw new \RuntimeException('Role not found.');
            }

            if ((int) $role['is_super'] === 1) {
                throw new \RuntimeException('Super Admin cannot be deleted.');
            }

            if ((int) $role['is_system'] === 1) {
                throw new \RuntimeException('System roles cannot be deleted.');
            }

            if (!$this->roles->delete($id)) {
                throw new \RuntimeException('Unable to delete role.');
            }

            return $this->success(
                'Role deleted successfully.',
                ['reload' => true]
            );
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    public function options()
    {
        $roles = $this->roles
            ->select(['id', 'name', 'display_name', 'slug'])
            ->where('status', 'active')
            ->where('is_super', 0)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return $this->success('', ['roles' => $roles]);
    }

    public function permissions($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return redirect()->to('/roles')->with('error', 'Invalid role.');
        }

        $role = $this->roles->find($id);

        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found.');
        }

        if ((int) $role['is_super'] === 1) {
            return view('roles/permissions_unrestricted', [
                'title' => 'Super Admin Access',
                'page_title' => 'Super Admin Access',
                'page_subtitle' => 'This role has unrestricted access across the application.',
                'role' => $role
            ]);
        }

        return view('roles/permissions', [
            'title' => 'Role Permissions',
            'page_title' => 'Role Permissions',
            'page_subtitle' => 'Control what this role can see and do.',
            'role' => $role
        ]);
    }
    
    public function permissionData($id)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return $this->error(
                'Invalid role.',
                [],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $role = $this->roles->find($id);

        if (!$role) {
            return $this->error(
                'Role not found.',
                [],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        if ((int) $role['is_super'] === 1) {
            return $this->error('Super Admin has unrestricted access.');
        }

        return $this->success('', [
            'role' => $role,
            'permissions' => $this->permissionService->getGrouped(),
            'assigned' => $this->permissionService->getRolePermissionIds($id)
        ]);
    }

    public function updatePermissions($id)
    {
        try {
            $roleId = (int) $id;

            if ($roleId <= 0) {
                return $this->error(
                    'Invalid role.',
                    [],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
            }

            $role = $this->roles->find($roleId);

            if (!$role) {
                return $this->error(
                    'Role not found.',
                    [],
                    ResponseInterface::HTTP_NOT_FOUND
                );
            }

            if ((int) $role['is_super'] === 1) {
                return $this->error('Super Admin does not require permission assignments.');
            }

            $permissions = $this->request->getPost('permissions');

            if ($permissions === null) {
                $permissions = [];
            }

            if (!is_array($permissions)) {
                return $this->error(
                    'Invalid permission selection.',
                    [],
                    ResponseInterface::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $this->permissionService->saveRolePermissions($roleId, $permissions);

            return $this->success('Permissions updated successfully.');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}
