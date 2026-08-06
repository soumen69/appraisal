<?php

namespace App\Controllers\Admin;

use App\Services\MenuService;
use App\Validation\Requests\MenuRequest;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\ModuleService;
use App\Services\PermissionService;

class MenuController extends BaseCrudController
{
    protected MenuService $menuService;
    protected ModuleService $moduleService;
    protected PermissionService $permissionService;

    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->moduleService = new ModuleService();
        $this->permissionService = new PermissionService();
    }

    public function index()
    {
        return view('menus/index', [
            'title'         => 'Menus',
            'page_title'    => 'Menu Management',
            'page_subtitle' => 'Manage application navigation and sidebar menus.'
        ]);
    }

    public function list()
    {
        $filters = [
            'page'       => $this->request->getGet('page'),
            'perPage'    => $this->request->getGet('pageSize'),
            'search'     => $this->request->getGet('search'),
            'status'     => $this->request->getGet('status'),
            'module_id'  => $this->request->getGet('module_id'),
            'sortBy'     => $this->request->getGet('orderBy'),
            'direction'  => $this->request->getGet('direction')
        ];
        // var_dump($this->menuService->getPaginated($filters));exit;
        return $this->success(
            '',
            $this->menuService->getPaginated($filters)
        );
    }

    public function store()
    {
        if (! $this->validate(MenuRequest::rules())) {
            return $this->validationFailed();
        }

        try {

            $id = $this->menuService->create(
                $this->request->getPost()
            );

            return $this->success(
                'Menu created successfully.',
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
        $menu = $this->menuService->getById((int) $id);

        if (! $menu) {

            return $this->error(
                'Menu not found.',
                [],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        return $this->success(
            '',
            $menu
        );
    }

    public function update($id)
    {
        if (! $this->validate(MenuRequest::rules((int) $id))) {
            return $this->validationFailed();
        }

        try {

            $this->menuService->update(
                (int) $id,
                $this->request->getPost()
            );

            return $this->success(
                'Menu updated successfully.',
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

    public function delete($id)
    {
        try {

            $this->menuService->delete((int) $id);

            return $this->success(
                'Menu deleted successfully.',
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
            'modules'    => $this->moduleService->getActive(),
            'parents'    => $this->menuService->getParents(),
            'permissions' => $this->permissionService->getAll()
        ]);
    }
}
