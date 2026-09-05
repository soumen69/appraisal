<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewMatrixModel extends Model
{
    protected $table = 'review_matrix';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'organization_id',
        'reviewer_role_id',
        'reviewee_role_id',
        'allow_self_review',
        'is_active'
    ];

    public function getReviewMatrices(array $filters = []): array
    {
        $builder = $this->builder();

        $builder
            ->select([
                'review_matrix.*',
                'reviewer_role.name AS reviewer_role_name',
                'reviewer_role.display_name AS reviewer_role_display_name',
                'reviewee_role.name AS reviewee_role_name',
                'reviewee_role.display_name AS reviewee_role_display_name',
                'organizations.name AS organization_name'
            ])
            ->join('roles reviewer_role', 'reviewer_role.id = review_matrix.reviewer_role_id', 'left')
            ->join('roles reviewee_role', 'reviewee_role.id = review_matrix.reviewee_role_id', 'left')
            ->join('organizations', 'organizations.id = review_matrix.organization_id', 'left');

        if (!empty($filters['organization_id'])) {
            $builder->where('review_matrix.organization_id', (int)$filters['organization_id']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('review_matrix.is_active', (int)$filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $builder
                ->groupStart()
                ->like('reviewer_role.name', $search)
                ->orLike('reviewer_role.display_name', $search)
                ->orLike('reviewee_role.name', $search)
                ->orLike('reviewee_role.display_name', $search)
                ->groupEnd();
        }

        return $builder
            ->orderBy('review_matrix.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getReviewMatrix(int $id): ?array
    {
        return $this->builder()
            ->select([
                'review_matrix.*',
                'reviewer_role.name AS reviewer_role_name',
                'reviewer_role.display_name AS reviewer_role_display_name',
                'reviewee_role.name AS reviewee_role_name',
                'reviewee_role.display_name AS reviewee_role_display_name',
                'organizations.name AS organization_name'
            ])
            ->join('roles reviewer_role', 'reviewer_role.id = review_matrix.reviewer_role_id', 'left')
            ->join('roles reviewee_role', 'reviewee_role.id = review_matrix.reviewee_role_id', 'left')
            ->join('organizations', 'organizations.id = review_matrix.organization_id', 'left')
            ->where('review_matrix.id', $id)
            ->get()
            ->getRowArray();
    }

    public function existsCombination(
        int $organizationId,
        int $reviewerRoleId,
        int $revieweeRoleId,
        ?int $excludeId = null
    ): bool {
        $builder = $this->builder()
            ->where('organization_id', $organizationId)
            ->where('reviewer_role_id', $reviewerRoleId)
            ->where('reviewee_role_id', $revieweeRoleId);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function getReviewMatricesPaginated(
        int $page = 1,
        int $pageSize = 10,
        string $search = '',
        string $status = '',
        string $orderBy = 'id',
        string $direction = 'desc'
    ): array {
        $builder = $this->builder();

        $builder
            ->select([
                'review_matrix.*',
                'organizations.name AS organization_name',
                'reviewer_role.name AS reviewer_role_name',
                'reviewer_role.display_name AS reviewer_role_display_name',
                'reviewee_role.name AS reviewee_role_name',
                'reviewee_role.display_name AS reviewee_role_display_name'
            ])
            ->join('organizations', 'organizations.id = review_matrix.organization_id', 'left')
            ->join('roles reviewer_role', 'reviewer_role.id = review_matrix.reviewer_role_id', 'left')
            ->join('roles reviewee_role', 'reviewee_role.id = review_matrix.reviewee_role_id', 'left');

        if ($search !== '') {
            $builder
                ->groupStart()
                ->like('organizations.name', $search)
                ->orLike('reviewer_role.name', $search)
                ->orLike('reviewer_role.display_name', $search)
                ->orLike('reviewee_role.name', $search)
                ->orLike('reviewee_role.display_name', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('review_matrix.is_active', (int)$status);
        }

        $allowedOrderBy = [
            'id' => 'review_matrix.id',
            'organization_name' => 'organizations.name',
            'reviewer_role_name' => 'reviewer_role.display_name',
            'reviewee_role_name' => 'reviewee_role.display_name',
            'is_active' => 'review_matrix.is_active',
            'created_at' => 'review_matrix.created_at'
        ];

        $orderColumn = $allowedOrderBy[$orderBy] ?? 'review_matrix.id';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';

        $builder->orderBy($orderColumn, $direction);

        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults();

        $offset = max(0, ($page - 1) * $pageSize);

        $data = $builder
            ->limit($pageSize, $offset)
            ->get()
            ->getResultArray();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'lastPage' => $total > 0
                ? (int)ceil($total / $pageSize)
                : 1
        ];
    }

    /**
     * Get active review matrix rules for a reviewee role.
     */
    public function getActiveRulesForRevieweeRole(
        int $organizationId,
        int $revieweeRoleId
    ): array {
        return $this->builder()
            ->select([
                'review_matrix.*',
                'reviewer_role.name AS reviewer_role_name',
                'reviewer_role.display_name AS reviewer_role_display_name'
            ])
            ->join(
                'roles reviewer_role',
                'reviewer_role.id = review_matrix.reviewer_role_id',
                'left'
            )
            ->where('review_matrix.organization_id', $organizationId)
            ->where('review_matrix.reviewee_role_id', $revieweeRoleId)
            ->where('review_matrix.is_active', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Check whether a reviewer role is allowed
     * to review a reviewee role.
     */
    public function canReview(
        int $organizationId,
        int $reviewerRoleId,
        int $revieweeRoleId
    ): bool {
        return $this->builder()
            ->where('organization_id', $organizationId)
            ->where('reviewer_role_id', $reviewerRoleId)
            ->where('reviewee_role_id', $revieweeRoleId)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }

    /**
     * Check whether self-review is allowed for a role.
     */
    public function allowsSelfReview(
        int $organizationId,
        int $roleId
    ): bool {
        return $this->builder()
            ->where('organization_id', $organizationId)
            ->where('reviewee_role_id', $roleId)
            ->where('allow_self_review', 1)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }
}
