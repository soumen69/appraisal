<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\OrganizationModel;
use CodeIgniter\HTTP\ResponseInterface;

class DepartmentController extends BaseController
{
    protected DepartmentModel $departmentModel;

    protected OrganizationModel $organizationModel;


    public function __construct()
    {
        $this->departmentModel =
            new DepartmentModel();

        $this->organizationModel =
            new OrganizationModel();
    }


    public function index()
    {
        return view(
            'departments/index',
            [
                'title' => 'Departments',
                'page_title' => 'Departments',
                'page_subtitle' => 'Manage your departments and access foundation.',
                'organizations' =>
                $this->organizationModel
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy(
                        'name',
                        'ASC'
                    )
                    ->findAll(),
            ]
        );
    }


    public function list(): ResponseInterface
    {
        try {

            $page = max(
                1,
                (int) $this->request
                    ->getGet('page')
            );

            $pageSize =
                (int) $this->request
                    ->getGet('pageSize');

            if (!in_array(
                $pageSize,
                [10, 25, 50, 100],
                true
            )) {
                $pageSize = 10;
            }


            $result =
                $this->departmentModel
                ->getDepartments(
                    $page,
                    $pageSize,
                    trim(
                        (string) $this->request
                            ->getGet('search')
                    ),
                    trim(
                        (string) $this->request
                            ->getGet('status')
                    ),
                    trim(
                        (string) $this->request
                            ->getGet('orderBy')
                    ) ?: 'id',
                    trim(
                        (string) $this->request
                            ->getGet('direction')
                    ) ?: 'desc'
                );


            return $this->response
                ->setJSON([
                    'success' => true,
                    'data' => $result,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Department list failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load departments.',
                ]);
        }
    }


    /**
     * Get grouped department for edit/drawer.
     */
    public function edit($id): ResponseInterface
    {
        $id = (int) $id;

        $department =
            $this->departmentModel
            ->getDepartmentGroupById($id);


        if (!$department) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Department not found.',
                ]);
        }


        return $this->response
            ->setJSON([
                'success' => true,
                'data' => $department,
            ]);
    }


    /**
     * Create department for one or multiple organizations.
     */
    public function store(): ResponseInterface
    {
        $organizationIds =
            $this->request
            ->getPost('organization_ids');


        if (!is_array($organizationIds)) {

            $organizationIds = [];
        }


        $organizationIds =
            array_values(
                array_unique(
                    array_filter(
                        array_map(
                            'intval',
                            $organizationIds
                        ),
                        fn($id) => $id > 0
                    )
                )
            );


        $rules = [

            'department_code' =>
            'permit_empty|max_length[30]',

            'name' =>
            'required|max_length[120]',

            'description' =>
            'permit_empty',

            'status' =>
            'required|in_list[active,inactive]',
        ];


        if (!$this->validate($rules)) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Please correct the highlighted fields.',
                    'errors' =>
                    $this->validator
                        ->getErrors(),
                ]);
        }


        if (!$organizationIds) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Please select at least one organization.',
                    'errors' => [
                        'organization_ids' =>
                        'Select at least one organization.',
                    ],
                ]);
        }


        try {

            /*
             * Verify organizations.
             */
            foreach ($organizationIds as $organizationId) {

                if (
                    !$this->organizationModel
                        ->find($organizationId)
                ) {

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'success' => false,
                            'message' =>
                            'One or more selected organizations do not exist.',
                            'errors' => [
                                'organization_ids' =>
                                'Please select valid organizations.',
                            ],
                        ]);
                }
            }


            $code =
                trim(
                    (string) $this->request
                        ->getPost('department_code')
                );

            $name =
                trim(
                    (string) $this->request
                        ->getPost('name')
                );

            $description =
                trim(
                    (string) $this->request
                        ->getPost('description')
                ) ?: null;

            $status =
                $this->request
                ->getPost('status')
                ?: 'active';


            /*
             * Prevent duplicate department within
             * the selected organizations.
             */
            foreach ($organizationIds as $organizationId) {

                if ($code !== '') {

                    if (
                        $this->departmentModel
                        ->findByCode(
                            $code,
                            $organizationId
                        )
                    ) {

                        return $this->response
                            ->setStatusCode(422)
                            ->setJSON([
                                'success' => false,
                                'message' =>
                                'Department code already exists in one of the selected organizations.',
                                'errors' => [
                                    'department_code' =>
                                    'This code is already used in one of the selected organizations.',
                                ],
                            ]);
                    }
                }
            }


            /*
             * Transaction.
             */
            $db =
                $this->departmentModel
                ->db;

            $db->transStart();


            foreach ($organizationIds as $organizationId) {

                $this->departmentModel->insert([
                    'organization_id' =>
                    $organizationId,

                    'department_code' =>
                    $code !== ''
                        ? $code
                        : null,

                    'name' =>
                    $name,

                    'description' =>
                    $description,

                    'status' =>
                    $status,
                ]);
            }


            $db->transComplete();


            if ($db->transStatus() === false) {

                throw new \RuntimeException(
                    'Department transaction failed.'
                );
            }


            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Department created successfully.',
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Department store failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to create department.',
                ]);
        }
    }


    /**
     * Update complete department group.
     */
    public function update($id): ResponseInterface
    {
        $id = (int) $id;


        $existing =
            $this->departmentModel
            ->find($id);


        if (!$existing) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Department not found.',
                ]);
        }


        $organizationIds =
            $this->request
            ->getPost('organization_ids');


        if (!is_array($organizationIds)) {

            $organizationIds = [];
        }


        $organizationIds =
            array_values(
                array_unique(
                    array_filter(
                        array_map(
                            'intval',
                            $organizationIds
                        ),
                        fn($value) => $value > 0
                    )
                )
            );


        $rules = [

            'department_code' =>
            'permit_empty|max_length[30]',

            'name' =>
            'required|max_length[120]',

            'description' =>
            'permit_empty',

            'status' =>
            'required|in_list[active,inactive]',
        ];


        if (!$this->validate($rules)) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Please correct the highlighted fields.',
                    'errors' =>
                    $this->validator
                        ->getErrors(),
                ]);
        }


        if (!$organizationIds) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Please select at least one organization.',
                    'errors' => [
                        'organization_ids' =>
                        'Select at least one organization.',
                    ],
                ]);
        }


        try {

            foreach ($organizationIds as $organizationId) {

                if (
                    !$this->organizationModel
                        ->find($organizationId)
                ) {

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'success' => false,
                            'message' =>
                            'One or more selected organizations do not exist.',
                            'errors' => [
                                'organization_ids' =>
                                'Please select valid organizations.',
                            ],
                        ]);
                }
            }


            $code =
                trim(
                    (string) $this->request
                        ->getPost('department_code')
                );

            $name =
                trim(
                    (string) $this->request
                        ->getPost('name')
                );

            $description =
                trim(
                    (string) $this->request
                        ->getPost('description')
                ) ?: null;

            $status =
                $this->request
                ->getPost('status')
                ?: 'active';


            /*
             * Identify the complete existing group.
             */
            $existingRows =
                $this->departmentModel
                ->getGroupRows(
                    $existing['department_code'],
                    $existing['name']
                );


            $existingOrganizationIds =
                array_map(
                    'intval',
                    array_column(
                        $existingRows,
                        'organization_id'
                    )
                );


            /*
             * Check duplicate code against other
             * departments.
             */
            foreach ($organizationIds as $organizationId) {

                $duplicate =
                    $this->departmentModel
                    ->findByCode(
                        $code,
                        $organizationId
                    );


                if (
                    $code !== '' &&
                    $duplicate &&
                    !in_array(
                        (int) $duplicate['id'],
                        array_column(
                            $existingRows,
                            'id'
                        ),
                        true
                    )
                ) {

                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON([
                            'success' => false,
                            'message' =>
                            'Department code already exists.',
                            'errors' => [
                                'department_code' =>
                                'This code is already used in one of the selected organizations.',
                            ],
                        ]);
                }
            }


            $db =
                $this->departmentModel
                ->db;

            $db->transStart();


            /*
             * Delete old group.
             */
            foreach ($existingRows as $row) {

                $this->departmentModel
                    ->delete(
                        (int) $row['id']
                    );
            }


            /*
             * Recreate group using new
             * organization selection.
             */
            foreach ($organizationIds as $organizationId) {

                $this->departmentModel->insert([
                    'organization_id' =>
                    $organizationId,

                    'department_code' =>
                    $code !== ''
                        ? $code
                        : null,

                    'name' =>
                    $name,

                    'description' =>
                    $description,

                    'status' =>
                    $status,
                ]);
            }


            $db->transComplete();


            if ($db->transStatus() === false) {

                throw new \RuntimeException(
                    'Department update transaction failed.'
                );
            }


            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Department updated successfully.',
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Department update failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to update department.',
                ]);
        }
    }


    /**
     * Delete complete department group.
     */
    public function delete($id): ResponseInterface
    {
        $id = (int) $id;


        $existing =
            $this->departmentModel
            ->find($id);


        if (!$existing) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Department not found.',
                ]);
        }


        try {

            $rows =
                $this->departmentModel
                ->getGroupRows(
                    $existing['department_code'],
                    $existing['name']
                );


            $db =
                $this->departmentModel
                ->db;

            $db->transStart();


            foreach ($rows as $row) {

                $this->departmentModel
                    ->delete(
                        (int) $row['id']
                    );
            }


            $db->transComplete();


            if ($db->transStatus() === false) {

                throw new \RuntimeException(
                    'Department delete transaction failed.'
                );
            }


            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Department deleted successfully.',
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Department delete failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to delete department.',
                ]);
        }
    }


    /**
     * Toggle complete department group.
     */
    public function toggleStatus($id): ResponseInterface
    {
        $id = (int) $id;


        $department =
            $this->departmentModel
            ->find($id);


        if (!$department) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Department not found.',
                ]);
        }


        try {

            $newStatus =
                $department['status'] === 'active'
                ? 'inactive'
                : 'active';


            $rows =
                $this->departmentModel
                ->getGroupRows(
                    $department['department_code'],
                    $department['name']
                );


            $db =
                $this->departmentModel
                ->db;

            $db->transStart();


            foreach ($rows as $row) {

                $this->departmentModel
                    ->update(
                        (int) $row['id'],
                        [
                            'status' =>
                            $newStatus,
                        ]
                    );
            }


            $db->transComplete();


            if ($db->transStatus() === false) {

                throw new \RuntimeException(
                    'Department status transaction failed.'
                );
            }


            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    $newStatus === 'active'
                        ? 'Department activated successfully.'
                        : 'Department deactivated successfully.',
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Department status failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to update department status.',
                ]);
        }
    }


    public function group(
        string $code
    ): ResponseInterface {

        $code =
            trim($code);


        if ($code === '') {

            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Invalid department code.',
                ]);
        }


        $department =
            $this->departmentModel
            ->where(
                'department_code',
                $code
            )
            ->first();


        if (!$department) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Department not found.',
                ]);
        }


        $group =
            $this->departmentModel
            ->getDepartmentGroupById(
                (int) $department['id']
            );


        return $this->response
            ->setJSON([
                'success' => true,
                'data' => $group,
            ]);
    }

    public function options(): ResponseInterface
    {
        try {
            $organizationId = (int) $this->request->getGet('organization_id');

            $builder = $this->departmentModel
                ->select([
                    'id',
                    'organization_id',
                    'department_code',
                    'name',
                    'status'
                ])
                ->where('status', 'active');

            if ($organizationId > 0) {
                $builder->where(
                    'organization_id',
                    $organizationId
                );
            }

            $departments = $builder
                ->orderBy('name', 'ASC')
                ->findAll();

            $data = array_map(
                static function (array $department): array {
                    return [
                        'id'              => (int) $department['id'],
                        'organization_id' => (int) $department['organization_id'],
                        'name'            => $department['name'],
                        'code'            => $department['department_code'],
                    ];
                },
                $departments
            );

            return $this->response->setJSON([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Department options failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load departments.',
                ]);
        }
    }
}
