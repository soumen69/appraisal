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
        try {
            $page = max(1, (int)($this->request->getGet('page') ?? 1));
            $pageSize = (int)($this->request->getGet('pageSize') ?? 10);

            if (!in_array($pageSize, [10, 25, 50, 100], true)) {
                $pageSize = 10;
            }

            $search = trim($this->request->getGet('search') ?? '');
            $status = trim($this->request->getGet('status') ?? '');

            $result = $this->reviewMatrixModel->getReviewMatrixGroupsPaginated(
                $page,
                $pageSize,
                $search,
                $status
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $result
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Review matrix list error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Unable to load review matrix.'
            ]);
        }
    }

    public function edit(int $id)
    {
        try {
            $matrix = $this->reviewMatrixModel->getReviewMatrix($id);

            if (!$matrix) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Review matrix entry not found.'
                ]);
            }

            $organizationId = (int)$matrix['organization_id'];
            $revieweeRoleId = (int)$matrix['reviewee_role_id'];

            $group = $this->reviewMatrixModel->getReviewMatrixGroup(
                $organizationId,
                $revieweeRoleId
            );

            $selfReview = $this->reviewMatrixModel->getSelfReviewSetting($organizationId, $revieweeRoleId);

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'id' => (int)$id,
                    'organization_id' => $organizationId,
                    'reviewee_role_id' => $revieweeRoleId,
                    'organization_name' => $matrix['organization_name'],
                    'reviewee_role_name' => $matrix['reviewee_role_name'],
                    'allow_self_review' => $selfReview['allow_self_review'],
                    'self_review_weightage' => $selfReview['weightage'],
                    'reviewers' => $group
                ]
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Review matrix edit error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Unable to load review matrix configuration.'
            ]);
        }
    }

    public function store()
    {
        try {
            $data = $this->validateMatrixGroup($this->request->getPost());

            $this->reviewMatrixModel->saveGroup(
                $data['organization_id'],
                $data['reviewee_role_id'],
                $data['allow_self_review'],
                $data['self_review_weightage'],
                $data['reviewers']
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Review matrix configuration created successfully.'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Review matrix create error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Unable to create review matrix configuration.'
            ]);
        }
    }

    public function update(int $id)
    {
        try {
            $existing = $this->reviewMatrixModel->find($id);

            if (!$existing) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Review matrix configuration not found.'
                ]);
            }

            $data = $this->validateMatrixGroup($this->request->getPost());

            if ((int)$existing['organization_id'] !== $data['organization_id'] || (int)$existing['reviewee_role_id'] !== $data['reviewee_role_id']) {
                throw new InvalidArgumentException('Organization and reviewee role cannot be changed for an existing configuration.');
            }

            $this->reviewMatrixModel->saveGroup(
                $data['organization_id'],
                $data['reviewee_role_id'],
                $data['allow_self_review'],
                $data['self_review_weightage'],
                $data['reviewers']
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Review matrix configuration updated successfully.'
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Review matrix update error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Unable to update review matrix configuration.'
            ]);
        }
    }

    protected function validateMatrixGroup(array $input, ?array $existing = null): array
    {
        $organizationId = (int)($input['organization_id'] ?? 0);
        $revieweeRoleId = (int)($input['reviewee_role_id'] ?? 0);
        $reviewers = $input['reviewers'] ?? [];
        $allowSelfReview = array_key_exists('allow_self_review', $input) ? (int)!empty($input['allow_self_review']) : 1;
        $selfReviewWeightageRaw = trim((string)($input['self_review_weightage'] ?? ''));

        if ($organizationId <= 0) {
            throw new InvalidArgumentException('Organization is required.');
        }

        if ($revieweeRoleId <= 0) {
            throw new InvalidArgumentException('Reviewee role is required.');
        }

        if (!$this->organizationModel->find($organizationId)) {
            throw new InvalidArgumentException('Selected organization does not exist.');
        }

        if (!$this->roleModel->find($revieweeRoleId)) {
            throw new InvalidArgumentException('Selected reviewee role does not exist.');
        }

        $selfReviewWeightage = 0.00;

        if ($allowSelfReview) {
            if ($selfReviewWeightageRaw === '' || !is_numeric($selfReviewWeightageRaw)) {
                throw new InvalidArgumentException('Self Review weightage is required when Self Review is enabled.');
            }

            $selfReviewWeightage = (float)$selfReviewWeightageRaw;

            if ($selfReviewWeightage <= 0 || $selfReviewWeightage > 100) {
                throw new InvalidArgumentException('Self Review weightage must be greater than 0 and no more than 100.');
            }
        }

        if (!is_array($reviewers) || !$reviewers) {
            throw new InvalidArgumentException('At least one reviewer is required.');
        }

        $normalized = [];
        $reviewerRoleIds = [];
        $activeWeightage = 0.00;
        $activeReviewerCount = 0;

        foreach ($reviewers as $index => $reviewer) {
            $reviewerRoleId = (int)($reviewer['reviewer_role_id'] ?? 0);
            if ($reviewerRoleId === $revieweeRoleId) {
                throw new InvalidArgumentException(
                    'The reviewee role cannot also be a reviewer role.'
                );
            }
            $weightageRaw = trim((string)($reviewer['weightage'] ?? ''));
            $isActive = !empty($reviewer['is_active']) ? 1 : 0;
            $rowId = (int)($reviewer['id'] ?? 0);

            if ($reviewerRoleId <= 0) {
                throw new InvalidArgumentException(
                    'Reviewer role is required for reviewer #' . ($index + 1) . '.'
                );
            }

            if (!$this->roleModel->find($reviewerRoleId)) {
                throw new InvalidArgumentException(
                    'Selected reviewer role is invalid for reviewer #' . ($index + 1) . '.'
                );
            }

            if (in_array($reviewerRoleId, $reviewerRoleIds, true)) {
                throw new InvalidArgumentException(
                    'The same reviewer role cannot be added more than once.'
                );
            }

            if ($weightageRaw === '' || !is_numeric($weightageRaw)) {
                throw new InvalidArgumentException(
                    'Weightage is required for reviewer #' . ($index + 1) . '.'
                );
            }

            $weightage = (float)$weightageRaw;

            if ($weightage <= 0 || $weightage > 100) {
                throw new InvalidArgumentException(
                    'Weightage for reviewer #' . ($index + 1) . ' must be greater than 0 and no more than 100.'
                );
            }

            if ($isActive) {
                $activeWeightage += $weightage;
                $activeReviewerCount++;
            }

            $reviewerRoleIds[] = $reviewerRoleId;

            $normalized[] = [
                'id' => $rowId,
                'reviewer_role_id' => $reviewerRoleId,
                'weightage' => number_format($weightage, 2, '.', ''),
                'is_active' => $isActive
            ];
        }

        $activeWeightage = round($activeWeightage, 2);

        if ($activeReviewerCount === 0) {
            throw new InvalidArgumentException('At least one active reviewer is required.');
        }

        $totalWeightage = round($activeWeightage + $selfReviewWeightage, 2);

        if ($totalWeightage !== 100.00) {
            throw new InvalidArgumentException(
                'Total review weightage must equal exactly 100.00%. Current total is ' .
                    number_format($totalWeightage, 2) . '%.'
            );
        }

        return [
            'organization_id' => $organizationId,
            'reviewee_role_id' => $revieweeRoleId,
            'allow_self_review' => $allowSelfReview,
            'self_review_weightage' => number_format($selfReviewWeightage, 2, '.', ''),
            'reviewers' => $normalized
        ];
    }

    public function delete(int $id)
    {
        return $this->response->setStatusCode(422)->setJSON([
            'success' => false,
            'message' => 'Reviewers are managed as a complete reviewee configuration. Use Edit to remove or deactivate a reviewer and redistribute the weightage.'
        ]);
    }

    public function view(int $id)
    {
        return $this->edit($id);
    }
}
