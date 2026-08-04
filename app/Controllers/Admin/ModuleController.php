<?php

namespace App\Controllers\Admin;

use App\Services\ModuleService;
use App\Validation\Requests\ModuleRequest;

class ModuleController extends BaseCrudController
{
    protected ModuleService $moduleService;

    public function __construct()
    {
        $this->moduleService = new ModuleService();
    }

    public function index()
    {
        return view('modules/index', [

            'title' => 'Modules'

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

    return $this->success(

        '',

        $this->moduleService->getPaginated($filters)

    );
}

    public function store()
{
    if (! $this->validate(ModuleRequest::rules())) {

        return $this->validationFailed();

    }

    try {

        $id = $this->moduleService->create(

            $this->request->getPost()

        );

        return $this->success(

            'Module created successfully.',

            ['id' => $id],

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
    $module = $this->moduleService->getById($id);

    if (! $module) {

        return $this->error(

            'Module not found.',

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
    if (! $this->validate(ModuleRequest::rules($id))) {

        return $this->validationFailed();

    }

    try {

        $this->moduleService->update(

            $id,

            $this->request->getPost()

        );

        return $this->success(

            'Module updated successfully.'

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

        $this->moduleService->delete($id);

        return $this->success(

            'Module deleted successfully.'

        );

    } catch (\Throwable $e) {

        return $this->error(

            $e->getMessage()

        );

    }
}
}