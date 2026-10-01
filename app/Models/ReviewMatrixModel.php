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
        'weightage',
        'is_active'
    ];


    public function getReviewMatrixGroupsPaginated(int $page = 1, int $pageSize = 10, string $search = '', string $status = '', string $orderBy = 'id', string $direction = 'desc'): array
    {
        $builder = $this->db->table($this->table . ' rm');

        $builder->select("
        MIN(rm.id) AS id,
        rm.organization_id,
        rm.reviewee_role_id,
        o.name AS organization_name,
        rr.name AS reviewee_role_name,
        rr.display_name AS reviewee_role_display_name,
        COALESCE(rmsr.allow_self_review, 1) AS allow_self_review,
        COALESCE(rmsr.weightage, 0) AS self_review_weightage,
        COUNT(rm.id) AS reviewer_count,
        SUM(CASE WHEN rm.is_active = 1 THEN 1 ELSE 0 END) AS active_reviewer_count,
        COALESCE(SUM(CASE WHEN rm.is_active = 1 THEN rm.weightage ELSE 0 END), 0) AS total_weightage,
        COALESCE(SUM(CASE WHEN rm.is_active = 1 THEN rm.weightage ELSE 0 END), 0) + COALESCE(rmsr.weightage, 0) AS grand_total_weightage
    ");

        $builder->join('organizations o', 'o.id = rm.organization_id');
        $builder->join('roles rr', 'rr.id = rm.reviewee_role_id');
        $builder->join(
            'review_matrix_self_review rmsr',
            'rmsr.organization_id = rm.organization_id AND rmsr.reviewee_role_id = rm.reviewee_role_id',
            'left'
        );

        $builder->groupBy([
            'rm.organization_id',
            'rm.reviewee_role_id',
            'o.name',
            'rr.name',
            'rr.display_name',
            'rmsr.allow_self_review',
            'rmsr.weightage'
        ]);

        if ($search !== '') {
            $builder->groupStart()
                ->like('o.name', $search)
                ->orLike('rr.name', $search)
                ->orLike('rr.display_name', $search)
                ->groupEnd();
        }

        if ($status === 'active') {
            $builder->having('active_reviewer_count >', 0);
        } elseif ($status === 'inactive') {
            $builder->having('active_reviewer_count', 0);
        } elseif ($status === 'complete') {
            $builder->having('grand_total_weightage', 100);
        } elseif ($status === 'incomplete') {
            $builder->having('grand_total_weightage !=', 100);
        }

        $allowedOrder = [
            'id',
            'organization_name',
            'reviewee_role_name',
            'reviewer_count',
            'total_weightage',
            'grand_total_weightage'
        ];

        $orderBy = in_array($orderBy, $allowedOrder, true) ? $orderBy : 'id';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';

        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $pageSize;

        $rows = $builder->orderBy($orderBy, $direction)
            ->limit($pageSize, $offset)
            ->get()
            ->getResultArray();

        foreach ($rows as &$row) {
            $row['reviewers'] = $this->getReviewMatrixGroup(
                (int)$row['organization_id'],
                (int)$row['reviewee_role_id']
            );
        }

        unset($row);

        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'lastPage' => $pageSize > 0 ? (int)ceil($total / $pageSize) : 1
        ];
    }



    public function getSelfReviewSetting(int $organizationId, int $revieweeRoleId): array
    {
        $row = $this->db->table('review_matrix_self_review')
            ->select('allow_self_review, weightage')
            ->where('organization_id', $organizationId)
            ->where('reviewee_role_id', $revieweeRoleId)
            ->get()
            ->getRowArray();

        return [
            'allow_self_review' => $row ? (int)$row['allow_self_review'] : 1,
            'weightage' => $row ? (float)$row['weightage'] : 0.00
        ];
    }

    public function saveSelfReviewSetting(int $organizationId, int $revieweeRoleId, int $allowSelfReview, float $weightage): bool
    {
        $builder = $this->db->table('review_matrix_self_review');

        $existing = $builder
            ->select('id')
            ->where('organization_id', $organizationId)
            ->where('reviewee_role_id', $revieweeRoleId)
            ->get()
            ->getRowArray();

        $data = [
            'organization_id' => $organizationId,
            'reviewee_role_id' => $revieweeRoleId,
            'allow_self_review' => $allowSelfReview ? 1 : 0,
            'weightage' => number_format($allowSelfReview ? $weightage : 0, 2, '.', ''),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            return $builder->where('id', (int)$existing['id'])->update($data);
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        return $builder->insert($data);
    }

    public function getReviewMatrixGroup(int $organizationId, int $revieweeRoleId): array
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
            ->where('review_matrix.organization_id', $organizationId)
            ->where('review_matrix.reviewee_role_id', $revieweeRoleId)
            ->orderBy('review_matrix.is_active', 'DESC')
            ->orderBy('review_matrix.weightage', 'DESC')
            ->orderBy('reviewer_role.display_name', 'ASC')
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

    public function saveGroup(int $organizationId, int $revieweeRoleId, int $allowSelfReview, float $selfReviewWeightage, array $reviewers): bool
    {
        $db = $this->db;

        $db->transStart();

        $this->saveSelfReviewSetting($organizationId, $revieweeRoleId, $allowSelfReview, $selfReviewWeightage);

        $existing = $this->builder()
            ->where('organization_id', $organizationId)
            ->where('reviewee_role_id', $revieweeRoleId)
            ->get()
            ->getResultArray();

        $existingByRole = [];

        foreach ($existing as $row) {
            $existingByRole[(int)$row['reviewer_role_id']] = $row;
        }

        $submittedIds = [];

        foreach ($reviewers as $reviewer) {
            $reviewerRoleId = (int)$reviewer['reviewer_role_id'];

            $data = [
                'organization_id' => $organizationId,
                'reviewer_role_id' => $reviewerRoleId,
                'reviewee_role_id' => $revieweeRoleId,
                'weightage' => number_format((float)$reviewer['weightage'], 2, '.', ''),
                'is_active' => !empty($reviewer['is_active']) ? 1 : 0
            ];

            if (isset($existingByRole[$reviewerRoleId])) {
                $id = (int)$existingByRole[$reviewerRoleId]['id'];
                $this->update($id, $data);
                $submittedIds[] = $id;
            } else {
                $id = (int)$this->insert($data, true);
                $submittedIds[] = $id;
            }
        }

        $existingIds = array_map(
            static fn(array $row): int => (int)$row['id'],
            $existing
        );

        $idsToDeactivate = array_diff($existingIds, $submittedIds);

        if ($idsToDeactivate) {
            $this->builder()
                ->whereIn('id', $idsToDeactivate)
                ->update([
                    'is_active' => 0,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        }

        $db->transComplete();

        return $db->transStatus();
    }

    public function getActiveRulesForRevieweeRole(int $organizationId, int $revieweeRoleId): array
    {
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
            ->orderBy('review_matrix.weightage', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function canReview(int $organizationId, int $reviewerRoleId, int $revieweeRoleId): bool
    {
        return $this->builder()
            ->where('organization_id', $organizationId)
            ->where('reviewer_role_id', $reviewerRoleId)
            ->where('reviewee_role_id', $revieweeRoleId)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }
}
