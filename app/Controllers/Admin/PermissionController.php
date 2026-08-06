<?php

namespace App\Controllers\Admin;

use App\Services\PermissionService;
use App\Validation\Requests\PermissionRequest;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionController extends BaseCrudController
{
    protected PermissionService $permissionService;

    public function __construct()
    {
        $this->permissionService = new PermissionService();
    }

    public function index()
    {
        return view('permissions/index', [
            'title' => 'Permissions',
            'page_title' => 'Permission Registry',
            'page_subtitle' => 'Manage application capabilities.'
        ]);
    }

    public function list()
    {
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->permissionService->grouped()
        ]);
    }

    public function store()
    {
        if (!$this->validate(PermissionRequest::rules())) {
            return $this->validationFailed();
        }

        try {

            $id = $this->permissionService->create(
                $this->request->getPost()
            );

            return $this->success(
                'Permission created successfully.',
                [
                    'id' => $id,
                    'reload' => true
                ],
                ResponseInterface::HTTP_CREATED
            );
        } catch (\Throwable $e) {

            return $this->error($e->getMessage());
        }
    }

    public function edit($id)
    {
        $permission = $this->permissionService->getById((int)$id);

        if (!$permission) {

            return $this->error(
                'Permission not found.',
                [],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        return $this->success('', $permission);
    }

    public function update($id)
    {
        if (!$this->validate(PermissionRequest::rules((int)$id))) {
            return $this->validationFailed();
        }

        try {

            $this->permissionService->update(
                (int)$id,
                $this->request->getPost()
            );

            return $this->success(
                'Permission updated successfully.',
                [
                    'reload' => true
                ]
            );
        } catch (\Throwable $e) {

            return $this->error($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {

            $this->permissionService->delete((int)$id);

            return $this->success(
                'Permission deleted successfully.',
                [
                    'reload' => true
                ]
            );
        } catch (\Throwable $e) {

            return $this->error($e->getMessage());
        }
    }
}
