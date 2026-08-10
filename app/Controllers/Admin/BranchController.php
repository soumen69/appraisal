<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BranchModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class BranchController extends BaseController
{
    protected BranchModel $branchModel;

    public function __construct()
    {
        $this->branchModel = new BranchModel();
    }


    public function index()
    {
        return view(
            'branches/index',
            [
                'title' => 'Branches',
                'page_title' => 'Branches',
                'page_subtitle' => 'Manage your active branches and access foundation.',
                'organizations' => $this->branchModel->getOrganizationOptions()
            ]
        );
    }


    public function list()
    {
        try {

            $page =
                max(
                    1,
                    (int) $this->request->getGet(
                        'page'
                    )
                );

            $pageSize =
                min(
                    max(
                        1,
                        (int) $this->request->getGet(
                            'pageSize'
                        )
                    ),
                    100
                );

            $search =
                trim(
                    (string) $this->request->getGet(
                        'search'
                    )
                );

            $status =
                trim(
                    (string) $this->request->getGet(
                        'status'
                    )
                );

            $organizationId =
                trim(
                    (string) $this->request->getGet(
                        'organizationId'
                    )
                );

            $orderBy =
                trim(
                    (string) $this->request->getGet(
                        'orderBy'
                    )
                );

            $direction =
                trim(
                    (string) $this->request->getGet(
                        'direction'
                    )
                );


            $result =
                $this->branchModel->getBranches(
                    $page,
                    $pageSize,
                    $search,
                    $status,
                    $organizationId,
                    $orderBy ?: 'id',
                    $direction ?: 'desc'
                );


            return $this->response->setJSON([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Branch list error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load branches.',
                ]);
        }
    }

    public function create()
    {
        return view(
            'branches/create',
            [
                'title' => 'Create Branch',
                'organizations' =>
                $this->branchModel
                    ->getOrganizationOptions(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Page
    |--------------------------------------------------------------------------
    */

    public function editPage(int $id)
    {
        $branch =
            $this->branchModel
            ->getBranch($id);

        if (!$branch) {

            throw PageNotFoundException::forPageNotFound(
                'Branch not found.'
            );
        }

        return view(
            'branches/edit',
            [
                'title' => 'Edit Branch',
                'branch' => $branch,
                'organizations' =>
                $this->branchModel
                    ->getOrganizationOptions(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Data
    |--------------------------------------------------------------------------
    */

    public function edit(int $id)
    {
        $branch =
            $this->branchModel
            ->getBranch($id);

        if (!$branch) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Branch not found.',
                ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $branch,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store()
    {
        $data =
            $this->getBranchInput();


        if (
            !$this->branchModel
                ->validate($data)
        ) {

            return $this->validationError();
        }


        if (
            $this->branchModel->branchCodeExists(
                $data['branch_code'] ?? '',
                (int) $data['organization_id']
            )
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'A branch with this code already exists in this organization.',
                    'errors' => [
                        'branch_code' =>
                        'This branch code is already in use.',
                    ],
                ]);
        }


        try {

            $id =
                $this->branchModel->insert(
                    $data,
                    true
                );

            if (!$id) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Unable to create branch.',
                        'errors' =>
                        $this->branchModel
                            ->errors(),
                    ]);
            }


            return $this->response->setJSON([
                'success' => true,
                'message' =>
                'Branch created successfully.',
                'data' => [
                    'id' => $id,
                ],
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Branch store error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to create branch.',
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(int $id)
    {
        $branch =
            $this->branchModel
            ->find($id);

        if (!$branch) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Branch not found.',
                ]);
        }


        $data =
            $this->getBranchInput();


        if (
            !$this->branchModel
                ->validate($data)
        ) {

            return $this->validationError();
        }


        if (
            $this->branchModel->branchCodeExists(
                $data['branch_code'] ?? '',
                (int) $data['organization_id'],
                $id
            )
        ) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'A branch with this code already exists in this organization.',
                    'errors' => [
                        'branch_code' =>
                        'This branch code is already in use.',
                    ],
                ]);
        }


        try {

            $updated =
                $this->branchModel
                ->update(
                    $id,
                    $data
                );

            if (!$updated) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Unable to update branch.',
                        'errors' =>
                        $this->branchModel
                            ->errors(),
                    ]);
            }


            return $this->response->setJSON([
                'success' => true,
                'message' =>
                'Branch updated successfully.',
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Branch update error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to update branch.',
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(int $id)
    {
        $branch =
            $this->branchModel
            ->find($id);

        if (!$branch) {

            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Branch not found.',
                ]);
        }


        try {

            if (
                !$this->branchModel
                    ->delete($id)
            ) {

                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Unable to delete branch.',
                    ]);
            }


            return $this->response->setJSON([
                'success' => true,
                'message' =>
                'Branch deleted successfully.',
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Branch delete error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(409)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'This branch cannot be deleted because it is being used by another record.',
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | Used by Employee module.
    |
    */

    public function options()
    {
        $organizationId =
            $this->request
            ->getGet('organization_id');


        $organizationId =
            $organizationId !== null &&
            $organizationId !== ''
            ? (int) $organizationId
            : null;


        return $this->response->setJSON([
            'success' => true,
            'data' =>
            $this->branchModel
                ->getOptions(
                    $organizationId
                ),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Input
    |--------------------------------------------------------------------------
    */

    protected function getBranchInput(): array
    {
        return [
            'organization_id' =>
            (int) $this->request
                ->getPost('organization_id'),

            'branch_code' =>
            trim(
                (string) $this->request
                    ->getPost('branch_code')
            ),

            'name' =>
            trim(
                (string) $this->request
                    ->getPost('name')
            ),

            'email' =>
            trim(
                (string) $this->request
                    ->getPost('email')
            ),

            'phone' =>
            trim(
                (string) $this->request
                    ->getPost('phone')
            ),

            'address' =>
            trim(
                (string) $this->request
                    ->getPost('address')
            ),

            'city' =>
            trim(
                (string) $this->request
                    ->getPost('city')
            ),

            'state' =>
            trim(
                (string) $this->request
                    ->getPost('state')
            ),

            'country' =>
            trim(
                (string) $this->request
                    ->getPost('country')
            ),

            'postal_code' =>
            trim(
                (string) $this->request
                    ->getPost('postal_code')
            ),

            'status' =>
            $this->request
                ->getPost('status')
                ?: 'active',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Validation Response
    |--------------------------------------------------------------------------
    */

    protected function validationError()
    {
        return $this->response
            ->setStatusCode(422)
            ->setJSON([
                'success' => false,
                'message' =>
                'Please correct the highlighted fields.',
                'errors' =>
                $this->branchModel
                    ->errors(),
            ]);
    }
}
