<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DesignationModel;
use App\Models\OrganizationModel;
use CodeIgniter\HTTP\ResponseInterface;

class DesignationController extends BaseController
{
    protected DesignationModel $designationModel;

    protected OrganizationModel $organizationModel;


    public function __construct()
    {
        $this->designationModel = new DesignationModel();

        $this->organizationModel = new OrganizationModel();
    }


    /**
     * Designation listing page.
     */
    public function index()
    {
        return view(
            'designations/index',
            [
                'title' => 'Designations',
                'page_title' => 'Designations',
                'page_subtitle' => 'Manage your workforce and their designations.',
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


    /**
     * AJAX listing.
     */
    public function list(): ResponseInterface
    {
        try {

            $page = max(
                1,
                (int) $this->request->getGet('page')
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

            $search = trim(
                (string) $this->request
                    ->getGet('search')
            );

            $status = trim(
                (string) $this->request
                    ->getGet('status')
            );

            $orderBy = trim(
                (string) $this->request
                    ->getGet('orderBy')
            );

            $direction = trim(
                (string) $this->request
                    ->getGet('direction')
            );

            $result =
                $this->designationModel
                ->getDesignations(
                    $page,
                    $pageSize,
                    $search,
                    $status,
                    $orderBy ?: 'id',
                    $direction ?: 'desc'
                );

            return $this->response
                ->setJSON([
                    'success' => true,
                    'data' => $result,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Designation list failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load designations.',
                ]);
        }
    }


    public function edit($id): ResponseInterface
    {
        try {

            $id = (int) $id;

            if ($id <= 0) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Invalid designation ID.',
                    ]);
            }

            $designation =
                $this->designationModel
                ->getDesignationGroupById($id);

            if (!$designation) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Designation not found.',
                    ]);
            }

            return $this->response
                ->setJSON([
                    'success' => true,
                    'data' => $designation,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Designation edit failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load designation.',
                ]);
        }
    }


    public function view($id): ResponseInterface
    {
        try {

            $designation =
                $this->designationModel
                ->getDesignationGroupById(
                    (int) $id
                );

            if (!$designation) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Designation not found.',
                    ]);
            }

            return $this->response
                ->setJSON([
                    'success' => true,
                    'data' => $designation,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Designation view failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load designation.',
                ]);
        }
    }


    /**
     * Create designation.
     */
    public function store(): ResponseInterface
    {
        $rules = [
            'organization_ids' =>
            'required',

            'organization_ids.*' =>
            'is_natural_no_zero',

            'designation_code' =>
            'permit_empty|max_length[30]',

            'title' =>
            'required|max_length[120]',

            'level' =>
            'required|is_natural_no_zero',

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
                    $this->validator->getErrors(),
                ]);
        }

        try {
            $organizationIds =
                $this->request
                ->getPost('organization_ids');

            if (!is_array($organizationIds)) {

                $organizationIds =
                    [$organizationIds];
            }

            $organizationIds =
                array_values(
                    array_unique(
                        array_filter(
                            array_map(
                                'intval',
                                $organizationIds
                            ),
                            static function ($id) {
                                return $id > 0;
                            }
                        )
                    )
                );

            if (!$organizationIds) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Please select at least one organization.',
                        'errors' => [
                            'organization_ids' =>
                            'Please select at least one organization.',
                        ],
                    ]);
            }

            $code = trim(
                (string) $this->request
                    ->getPost('designation_code')
            );

            $title = trim(
                (string) $this->request
                    ->getPost('title')
            );

            $description = trim(
                (string) $this->request
                    ->getPost('description')
            );

            $level =
                (int) $this->request
                    ->getPost('level');

            $status =
                $this->request
                ->getPost('status')
                ?: 'active';

            $organizations =
                $this->organizationModel
                ->whereIn(
                    'id',
                    $organizationIds
                )
                ->findAll();

            $foundOrganizationIds =
                array_map(
                    'intval',
                    array_column(
                        $organizations,
                        'id'
                    )
                );

            $missingOrganizationIds =
                array_values(
                    array_diff(
                        $organizationIds,
                        $foundOrganizationIds
                    )
                );

            if ($missingOrganizationIds) {

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

            $inactiveOrganizations = [];

            foreach ($organizations as $organization) {

                if (
                    ($organization['status'] ?? null)
                    !== 'active'
                ) {

                    $inactiveOrganizations[] =
                        $organization['name']
                        ?? ('Organization #' . $organization['id']);
                }
            }

            if ($inactiveOrganizations) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'One or more selected organizations are inactive.',
                        'errors' => [
                            'organization_ids' =>
                            'Inactive organizations cannot be assigned a designation.',
                        ],
                    ]);
            }

            if ($code !== '') {

                foreach ($organizationIds as $organizationId) {

                    if (
                        $this->designationModel
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
                                'Designation code already exists.',
                                'errors' => [
                                    'designation_code' =>
                                    'The designation code "' .
                                        $code .
                                        '" already exists in one of the selected organizations.',
                                ],
                            ]);
                    }
                }
            }

            $db = db_connect();

            $db->transBegin();

            foreach ($organizationIds as $organizationId) {

                $inserted =
                    $this->designationModel->insert([
                        'organization_id' =>
                        $organizationId,

                        'designation_code' =>
                        $code ?: null,

                        'title' =>
                        $title,

                        'level' =>
                        $level,

                        'description' =>
                        $description ?: null,

                        'status' =>
                        $status,
                    ]);

                if ($inserted === false) {

                    throw new \RuntimeException(
                        'Failed to create designation for organization ID ' .
                            $organizationId
                    );
                }
            }

            if (!$db->transStatus()) {

                $db->transRollback();

                throw new \RuntimeException(
                    'Designation transaction failed.'
                );
            }

            $db->transCommit();


            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Designation created successfully.',
                ]);
        } catch (\Throwable $e) {

            if (isset($db)) {
                $db->transRollback();
            }

            log_message(
                'error',
                'Designation store failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to create designation.',
                ]);
        }
    }

    public function update($id): ResponseInterface
    {
        $id = (int) $id;

        if ($id <= 0) {

            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Invalid designation ID.',
                ]);
        }

        $designation =
            $this->designationModel
            ->find($id);

        if (!$designation) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Designation not found.',
                ]);
        }


        $rules = [
            'organization_ids' =>
            'required',

            'organization_ids.*' =>
            'is_natural_no_zero',

            'designation_code' =>
            'permit_empty|max_length[30]',

            'title' =>
            'required|max_length[120]',

            'level' =>
            'required|is_natural_no_zero',

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
                    $this->validator->getErrors(),
                ]);
        }


        try {

            $organizationIds =
                $this->request
                ->getPost('organization_ids');

            if (!is_array($organizationIds)) {

                $organizationIds =
                    [$organizationIds];
            }

            $organizationIds =
                array_values(
                    array_unique(
                        array_filter(
                            array_map(
                                'intval',
                                $organizationIds
                            ),
                            static function ($id) {
                                return $id > 0;
                            }
                        )
                    )
                );

            if (!$organizationIds) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Please select at least one organization.',
                        'errors' => [
                            'organization_ids' =>
                            'Please select at least one organization.',
                        ],
                    ]);
            }

            $code = trim(
                (string) $this->request
                    ->getPost('designation_code')
            );

            $title = trim(
                (string) $this->request
                    ->getPost('title')
            );

            $description = trim(
                (string) $this->request
                    ->getPost('description')
            );

            $level =
                (int) $this->request
                    ->getPost('level');

            $status =
                $this->request
                ->getPost('status')
                ?: 'active';

            $groupBuilder =
                $this->designationModel
                ->builder();

            $groupBuilder
                ->where(
                    'designation_code',
                    $designation['designation_code']
                )
                ->where(
                    'title',
                    $designation['title']
                )
                ->where(
                    'level',
                    $designation['level']
                );

            $existingRows =
                $groupBuilder
                ->get()
                ->getResultArray();

            if (!$existingRows) {

                $existingRows = [
                    $designation
                ];
            }


            $existingOrganizationIds =
                array_values(
                    array_unique(
                        array_map(
                            'intval',
                            array_column(
                                $existingRows,
                                'organization_id'
                            )
                        )
                    )
                );

            $organizations =
                $this->organizationModel
                ->whereIn(
                    'id',
                    $organizationIds
                )
                ->findAll();

            $foundOrganizationIds =
                array_map(
                    'intval',
                    array_column(
                        $organizations,
                        'id'
                    )
                );

            $missingOrganizationIds =
                array_values(
                    array_diff(
                        $organizationIds,
                        $foundOrganizationIds
                    )
                );

            if ($missingOrganizationIds) {

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

            $inactiveOrganizations = [];

            foreach ($organizations as $organization) {

                if (
                    ($organization['status'] ?? null)
                    !== 'active'
                ) {

                    $inactiveOrganizations[] =
                        $organization['name']
                        ?? ('Organization #' . $organization['id']);
                }
            }

            if ($inactiveOrganizations) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'One or more selected organizations are inactive.',
                        'errors' => [
                            'organization_ids' =>
                            'Inactive organizations cannot be assigned a designation.',
                        ],
                    ]);
            }

            if ($code !== '') {

                foreach ($organizationIds as $organizationId) {

                    $existingCodeRows =
                        $this->designationModel
                        ->builder()
                        ->where(
                            'designation_code',
                            $code
                        )
                        ->where(
                            'organization_id',
                            $organizationId
                        )
                        ->get()
                        ->getResultArray();

                    foreach ($existingCodeRows as $existingCodeRow) {

                        $belongsToCurrentGroup =
                            in_array(
                                (int) $existingCodeRow['id'],
                                array_map(
                                    'intval',
                                    array_column(
                                        $existingRows,
                                        'id'
                                    )
                                ),
                                true
                            );

                        if (!$belongsToCurrentGroup) {

                            return $this->response
                                ->setStatusCode(422)
                                ->setJSON([
                                    'success' => false,
                                    'message' =>
                                    'Designation code already exists.',
                                    'errors' => [
                                        'designation_code' =>
                                        'The designation code "' .
                                            $code .
                                            '" already exists in one of the selected organizations.',
                                    ],
                                ]);
                        }
                    }
                }
            }

            $db = db_connect();

            $db->transBegin();


            $organizationsToRemove =
                array_values(
                    array_diff(
                        $existingOrganizationIds,
                        $organizationIds
                    )
                );



            $organizationsToAdd =
                array_values(
                    array_diff(
                        $organizationIds,
                        $existingOrganizationIds
                    )
                );

            if ($organizationsToRemove) {

                $this->designationModel
                    ->whereIn(
                        'id',
                        array_column(
                            array_filter(
                                $existingRows,
                                static function ($row) use (
                                    $organizationsToRemove
                                ) {
                                    return in_array(
                                        (int) $row['organization_id'],
                                        $organizationsToRemove,
                                        true
                                    );
                                }
                            ),
                            'id'
                        )
                    )
                    ->delete();
            }

            foreach ($existingRows as $existingRow) {

                $organizationId =
                    (int) $existingRow['organization_id'];

                if (
                    !in_array(
                        $organizationId,
                        $organizationIds,
                        true
                    )
                ) {
                    continue;
                }

                $updated =
                    $this->designationModel
                    ->update(
                        (int) $existingRow['id'],
                        [
                            'organization_id' =>
                            $organizationId,

                            'designation_code' =>
                            $code ?: null,

                            'title' =>
                            $title,

                            'level' =>
                            $level,

                            'description' =>
                            $description ?: null,

                            'status' =>
                            $status,
                        ]
                    );

                if ($updated === false) {

                    throw new \RuntimeException(
                        'Failed to update designation ID ' .
                            $existingRow['id']
                    );
                }
            }

            foreach ($organizationsToAdd as $organizationId) {

                $inserted =
                    $this->designationModel
                    ->insert([
                        'organization_id' =>
                        $organizationId,

                        'designation_code' =>
                        $code ?: null,

                        'title' =>
                        $title,

                        'level' =>
                        $level,

                        'description' =>
                        $description ?: null,

                        'status' =>
                        $status,
                    ]);

                if ($inserted === false) {

                    throw new \RuntimeException(
                        'Failed to add designation to organization ID ' .
                            $organizationId
                    );
                }
            }

            if (!$db->transStatus()) {

                $db->transRollback();

                throw new \RuntimeException(
                    'Designation update transaction failed.'
                );
            }

            $db->transCommit();


            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Designation updated successfully.',
                ]);
        } catch (\Throwable $e) {

            if (isset($db)) {
                $db->transRollback();
            }

            log_message(
                'error',
                'Designation update failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to update designation.',
                ]);
        }
    }


    /**
     * Delete designation.
     */
    public function delete($id): ResponseInterface
    {
        $id = (int) $id;

        $designation =
            $this->designationModel->find($id);

        if (!$designation) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Designation not found.',
                ]);
        }

        try {

            $this->designationModel->delete($id);

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Designation deleted successfully.',
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Designation delete failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to delete designation.',
                ]);
        }
    }


    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus($id): ResponseInterface
    {
        $id = (int) $id;

        $designation =
            $this->designationModel->find($id);

        if (!$designation) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Designation not found.',
                ]);
        }

        try {

            $status =
                $designation['status'] === 'active'
                ? 'inactive'
                : 'active';

            $this->designationModel->update(
                $id,
                [
                    'status' => $status,
                ]
            );

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    $status === 'active'
                        ? 'Designation activated successfully.'
                        : 'Designation deactivated successfully.',
                    'status' => $status,
                ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Designation status update failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to update designation status.',
                ]);
        }
    }

    public function group(string $code): ResponseInterface
    {
        $code = trim($code);

        if ($code === '') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Invalid designation code.',
                ]);
        }

        $designation =
            $this->designationModel
            ->getDesignationGroup($code);

        if (!$designation) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Designation not found.',
                ]);
        }

        return $this->response
            ->setJSON([
                'success' => true,
                'data' => $designation,
            ]);
    }
}
