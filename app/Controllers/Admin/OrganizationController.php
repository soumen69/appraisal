<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrganizationModel;
use CodeIgniter\HTTP\ResponseInterface;

class OrganizationController extends BaseController
{
    protected OrganizationModel $organizationModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
    }


    /**
     * Organization listing page.
     */
    public function index()
    {
        return view('organizations/index', [
            'title' => 'Organizations',
            'page_title'     => 'Organizations',
            'page_subtitle'  => 'Manage organizations profile and access foundation.'
        ]);
    }


    /**
     * AJAX organization listing.
     */
    public function list(): ResponseInterface
    {
        try {

            $page = max(
                1,
                (int) $this->request->getGet('page')
            );

            $pageSize = (int) $this->request->getGet('pageSize');

            if (!in_array($pageSize, [10, 25, 50, 100], true)) {
                $pageSize = 10;
            }

            $search = trim(
                (string) $this->request->getGet('search')
            );

            $status = trim(
                (string) $this->request->getGet('status')
            );

            $orderBy = trim(
                (string) $this->request->getGet('orderBy')
            );

            $direction = trim(
                (string) $this->request->getGet('direction')
            );

            $result = $this->organizationModel->getOrganizations(
                $page,
                $pageSize,
                $search,
                $status,
                $orderBy ?: 'id',
                $direction ?: 'desc'
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Organization list failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load organizations.'
                ]);
        }
    }


    /**
     * Get organization for edit/view drawer.
     */
    public function edit($id): ResponseInterface
    {
        try {

            $organization = $this->organizationModel->find(
                (int) $id
            );

            if (!$organization) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Organization not found.'
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $organization
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Organization edit failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load organization.'
                ]);
        }
    }


    /**
     * Create organization.
     */
    public function store(): ResponseInterface
    {
        $rules = [
            'organization_code' => [
                'rules' => 'permit_empty|max_length[30]',
            ],
            'name' => [
                'rules' => 'required|max_length[150]',
                'errors' => [
                    'required' => 'Organization name is required.'
                ]
            ],
            'legal_name' => 'permit_empty|max_length[200]',
            'email' => 'permit_empty|valid_email|max_length[150]',
            'phone' => 'permit_empty|max_length[30]',
            'website' => 'permit_empty|max_length[150]',
            'postal_code' => 'permit_empty|max_length[20]',
            'timezone' => 'permit_empty|max_length[100]',
            'currency' => 'permit_empty|max_length[20]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Please correct the highlighted fields.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {

            $code = trim(
                (string) $this->request->getPost('organization_code')
            );

            if ($code !== '' && $this->organizationModel->findByCode($code)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Organization code already exists.',
                    'errors' => [
                        'organization_code' =>
                        'This organization code is already in use.'
                    ]
                ]);
            }

            $data = $this->getOrganizationData();

            $this->organizationModel->insert($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Organization created successfully.'
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Organization store failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to create organization.'
                ]);
        }
    }


    /**
     * Update organization.
     */
    public function update($id): ResponseInterface
    {
        $id = (int) $id;

        $organization = $this->organizationModel->find($id);

        if (!$organization) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Organization not found.'
                ]);
        }

        $rules = [
            'organization_code' => 'permit_empty|max_length[30]',
            'name' => 'required|max_length[150]',
            'legal_name' => 'permit_empty|max_length[200]',
            'email' => 'permit_empty|valid_email|max_length[150]',
            'phone' => 'permit_empty|max_length[30]',
            'website' => 'permit_empty|max_length[150]',
            'postal_code' => 'permit_empty|max_length[20]',
            'timezone' => 'permit_empty|max_length[100]',
            'currency' => 'permit_empty|max_length[20]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Please correct the highlighted fields.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {

            $code = trim(
                (string) $this->request->getPost('organization_code')
            );

            if (
                $code !== '' &&
                $this->organizationModel->findByCode($code, $id)
            ) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Organization code already exists.',
                    'errors' => [
                        'organization_code' =>
                        'This organization code is already in use.'
                    ]
                ]);
            }

            $this->organizationModel->update(
                $id,
                $this->getOrganizationData()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Organization updated successfully.'
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Organization update failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to update organization.'
                ]);
        }
    }


    /**
     * Delete organization.
     */
    public function delete($id): ResponseInterface
    {
        $id = (int) $id;

        $organization = $this->organizationModel->find($id);

        if (!$organization) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Organization not found.'
                ]);
        }

        try {

            $this->organizationModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Organization deleted successfully.'
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Organization delete failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to delete organization. It may have related records.'
                ]);
        }
    }


    /**
     * Toggle status.
     */
    public function toggleStatus($id): ResponseInterface
    {
        $id = (int) $id;

        $organization = $this->organizationModel->find($id);

        if (!$organization) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Organization not found.'
                ]);
        }

        try {

            $status = $organization['status'] === 'active'
                ? 'inactive'
                : 'active';

            $this->organizationModel->update(
                $id,
                ['status' => $status]
            );

            return $this->response->setJSON([
                'success' => true,
                'message' =>
                $status === 'active'
                    ? 'Organization activated successfully.'
                    : 'Organization deactivated successfully.',
                'status' => $status
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Organization status toggle failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to update organization status.'
                ]);
        }
    }


    private function getOrganizationData(): array
    {
        return [
            'organization_code' =>
            trim((string) $this->request->getPost('organization_code')) ?: null,

            'name' =>
            trim((string) $this->request->getPost('name')),

            'legal_name' =>
            trim((string) $this->request->getPost('legal_name')) ?: null,

            'email' =>
            trim((string) $this->request->getPost('email')) ?: null,

            'phone' =>
            trim((string) $this->request->getPost('phone')) ?: null,

            'website' =>
            trim((string) $this->request->getPost('website')) ?: null,

            'address' =>
            trim((string) $this->request->getPost('address')) ?: null,

            'city' =>
            trim((string) $this->request->getPost('city')) ?: null,

            'state' =>
            trim((string) $this->request->getPost('state')) ?: null,

            'country' =>
            trim((string) $this->request->getPost('country')) ?: null,

            'postal_code' =>
            trim((string) $this->request->getPost('postal_code')) ?: null,

            'timezone' =>
            trim((string) $this->request->getPost('timezone'))
                ?: 'Asia/Kolkata',

            'currency' =>
            trim((string) $this->request->getPost('currency'))
                ?: 'INR',

            'status' =>
            $this->request->getPost('status') ?: 'active'
        ];
    }

    public function options(): ResponseInterface
    {
        try {
            $organizations = $this->organizationModel
                ->select([
                    'id',
                    'name',
                    'organization_code'
                ])
                ->where('status', 'active')
                ->orderBy('name', 'ASC')
                ->findAll();

            $data = array_map(
                static function (array $organization): array {
                    return [
                        'id'   => (int) $organization['id'],
                        'name' => $organization['name'],
                        'code' => $organization['organization_code'],
                    ];
                },
                $organizations
            );

            return $this->response->setJSON([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Organization options failed: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load organizations.',
                ]);
        }
    }
}
