<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReviewMatrixModel;
use App\Models\Admin\RoleModel;
use App\Models\OrganizationModel;
use InvalidArgumentException;
use Throwable;

class ReviewMatrixController extends BaseController
{
    protected ReviewMatrixModel $reviewMatrixModel;
    protected RoleModel $roleModel;
    protected OrganizationModel $organizationModel;

    public function __construct()
    {
        $this->reviewMatrixModel = new ReviewMatrixModel();
        $this->roleModel = new RoleModel();
        $this->organizationModel = new OrganizationModel();
    }

    public function index()
    {
        return view('appraisal/review_matrix/index', [
            'title' => 'Review Matrix',
            'page_title' => 'Review Matrix',
            'page_subtitle' => 'Configure which roles can review other roles.'
        ]);
    }

    public function list()
    {
        $page = max(1, (int)($this->request->getGet('page') ?? 1));
        $pageSize = max(1, (int)($this->request->getGet('pageSize') ?? 10));
        $search = trim($this->request->getGet('search') ?? '');
        $status = $this->request->getGet('status') ?? '';
        $orderBy = $this->request->getGet('orderBy') ?? 'id';
        $direction = $this->request->getGet('direction') ?? 'desc';

        $result = $this->reviewMatrixModel->getReviewMatricesPaginated(
            $page,
            $pageSize,
            $search,
            $status,
            $orderBy,
            $direction
        );

        return $this->response->setJSON([
            'success' => true,
            'data' => $result
        ]);
    }


    public function edit(int $id)
    {
        try {
            $matrix = $this->reviewMatrixModel->getReviewMatrix($id);

            if (!$matrix) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Review matrix entry not found.'
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $matrix
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Review matrix edit error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load review matrix entry.'
                ]);
        }
    }

    public function store()
    {
        try {
            $data = $this->validateMatrixData($this->request->getPost());

            if ($this->reviewMatrixModel->existsCombination(
                $data['organization_id'],
                $data['reviewer_role_id'],
                $data['reviewee_role_id']
            )) {
                throw new InvalidArgumentException(
                    'This reviewer and reviewee role combination already exists.'
                );
            }

            $id = $this->reviewMatrixModel->insert($data, true);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Review matrix entry created successfully.',
                'data' => ['id' => $id]
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
        } catch (Throwable $e) {
            log_message('error', 'Review matrix create error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to create review matrix entry.'
                ]);
        }
    }

    public function update(int $id)
    {
        try {
            $matrix = $this->reviewMatrixModel->find($id);

            if (!$matrix) {
                throw new InvalidArgumentException('Review matrix entry not found.');
            }

            $data = $this->validateMatrixData($this->request->getPost());

            if ($this->reviewMatrixModel->existsCombination(
                $data['organization_id'],
                $data['reviewer_role_id'],
                $data['reviewee_role_id'],
                $id
            )) {
                throw new InvalidArgumentException(
                    'This reviewer and reviewee role combination already exists.'
                );
            }

            $this->reviewMatrixModel->update($id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Review matrix entry updated successfully.'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
        } catch (Throwable $e) {
            log_message('error', 'Review matrix update error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to update review matrix entry.'
                ]);
        }
    }

    public function delete(int $id)
    {
        try {
            $matrix = $this->reviewMatrixModel->find($id);

            if (!$matrix) {
                throw new InvalidArgumentException('Review matrix entry not found.');
            }

            $this->reviewMatrixModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Review matrix entry deleted successfully.'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
        } catch (Throwable $e) {
            log_message('error', 'Review matrix delete error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to delete review matrix entry.'
                ]);
        }
    }

    public function view(int $id)
    {
        return $this->edit($id);
    }

    protected function validateMatrixData(array $input): array
    {
        $organizationId = (int)($input['organization_id'] ?? 0);
        $reviewerRoleId = (int)($input['reviewer_role_id'] ?? 0);
        $revieweeRoleId = (int)($input['reviewee_role_id'] ?? 0);

        if ($organizationId <= 0) {
            throw new InvalidArgumentException('Organization is required.');
        }

        if ($reviewerRoleId <= 0) {
            throw new InvalidArgumentException('Reviewer role is required.');
        }

        if ($revieweeRoleId <= 0) {
            throw new InvalidArgumentException('Reviewee role is required.');
        }

        if (!$this->organizationModel->find($organizationId)) {
            throw new InvalidArgumentException('Selected organization does not exist.');
        }

        if (!$this->roleModel->find($reviewerRoleId)) {
            throw new InvalidArgumentException('Selected reviewer role does not exist.');
        }

        if (!$this->roleModel->find($revieweeRoleId)) {
            throw new InvalidArgumentException('Selected reviewee role does not exist.');
        }

        $allowSelfReview = !empty($input['allow_self_review']) ? 1 : 0;

        if ($reviewerRoleId !== $revieweeRoleId && $allowSelfReview) {
            $allowSelfReview = 0;
        }

        return [
            'organization_id' => $organizationId,
            'reviewer_role_id' => $reviewerRoleId,
            'reviewee_role_id' => $revieweeRoleId,
            'allow_self_review' => $allowSelfReview,
            'is_active' => isset($input['is_active']) ? (int)!empty($input['is_active']) : 1
        ];
    }
}
