<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalReviewerAssignmentModel extends Model
{
    protected $table = 'appraisal_reviewer_assignments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields = true;

    protected $allowedFields = [
        'appraisal_cycle_id',
        'employee_id',
        'reviewer_id',
        'reviewer_role_id',
        'review_matrix_id',
        'status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get reviewer assignments for a cycle.
     */
    public function getAssignments(int $cycleId): array
    {
        return $this->builder()
            ->select([
                'appraisal_reviewer_assignments.*',

                "CONCAT(
                    COALESCE(employee.first_name, ''),
                    CASE
                        WHEN employee.last_name IS NOT NULL
                        AND employee.last_name != ''
                        THEN CONCAT(' ', employee.last_name)
                        ELSE ''
                    END
                ) AS employee_name",

                'employee.employee_code AS employee_code',

                "CONCAT(
                    COALESCE(reviewer.first_name, ''),
                    CASE
                        WHEN reviewer.last_name IS NOT NULL
                        AND reviewer.last_name != ''
                        THEN CONCAT(' ', reviewer.last_name)
                        ELSE ''
                    END
                ) AS reviewer_name",

                'reviewer.employee_code AS reviewer_code',

                'roles.name AS reviewer_role_name',
                'roles.display_name AS reviewer_role_display_name'
            ])
            ->join(
                'users employee',
                'employee.id = appraisal_reviewer_assignments.employee_id',
                'left'
            )
            ->join(
                'users reviewer',
                'reviewer.id = appraisal_reviewer_assignments.reviewer_id',
                'left'
            )
            ->join(
                'roles',
                'roles.id = appraisal_reviewer_assignments.reviewer_role_id',
                'left'
            )
            ->where(
                'appraisal_reviewer_assignments.appraisal_cycle_id',
                $cycleId
            )
            ->orderBy(
                'appraisal_reviewer_assignments.id',
                'DESC'
            )
            ->get()
            ->getResultArray();
    }

    /**
     * Get all reviewers assigned to an employee.
     */
    public function getEmployeeReviewers(
        int $cycleId,
        int $employeeId
    ): array {
        return $this->builder()
            ->select([
                'appraisal_reviewer_assignments.*',
                'roles.name AS reviewer_role_name',
                'roles.display_name AS reviewer_role_display_name',

                "CONCAT(
                    COALESCE(users.first_name, ''),
                    CASE
                        WHEN users.last_name IS NOT NULL
                        AND users.last_name != ''
                        THEN CONCAT(' ', users.last_name)
                        ELSE ''
                    END
                ) AS reviewer_name",

                'users.employee_code AS reviewer_code',
                'users.email AS reviewer_email'
            ])
            ->join(
                'users',
                'users.id = appraisal_reviewer_assignments.reviewer_id',
                'left'
            )
            ->join(
                'roles',
                'roles.id = appraisal_reviewer_assignments.reviewer_role_id',
                'left'
            )
            ->where(
                'appraisal_reviewer_assignments.appraisal_cycle_id',
                $cycleId
            )
            ->where(
                'appraisal_reviewer_assignments.employee_id',
                $employeeId
            )
            ->where(
                'appraisal_reviewer_assignments.status',
                'active'
            )
            ->get()
            ->getResultArray();
    }

    /**
     * Get employees assigned to a reviewer.
     */
    public function getReviewerAssignments(
        int $cycleId,
        int $reviewerId
    ): array {
        return $this->builder()
            ->select([
                'appraisal_reviewer_assignments.*',

                "CONCAT(
                    COALESCE(employee.first_name, ''),
                    CASE
                        WHEN employee.last_name IS NOT NULL
                        AND employee.last_name != ''
                        THEN CONCAT(' ', employee.last_name)
                        ELSE ''
                    END
                ) AS employee_name",

                'employee.employee_code',
                'employee.email',

                'departments.name AS department_name',
                'designations.title AS designation_name',

                'roles.name AS reviewer_role_name',
                'roles.display_name AS reviewer_role_display_name'
            ])
            ->join(
                'users employee',
                'employee.id = appraisal_reviewer_assignments.employee_id',
                'left'
            )
            ->join(
                'departments',
                'departments.id = employee.department_id',
                'left'
            )
            ->join(
                'designations',
                'designations.id = employee.designation_id',
                'left'
            )
            ->join(
                'roles',
                'roles.id = appraisal_reviewer_assignments.reviewer_role_id',
                'left'
            )
            ->where(
                'appraisal_reviewer_assignments.appraisal_cycle_id',
                $cycleId
            )
            ->where(
                'appraisal_reviewer_assignments.reviewer_id',
                $reviewerId
            )
            ->where(
                'appraisal_reviewer_assignments.status',
                'active'
            )
            ->get()
            ->getResultArray();
    }

    /**
     * Check whether reviewer assignment exists.
     */
    public function assignmentExists(
        int $cycleId,
        int $employeeId,
        int $reviewerId,
        int $reviewerRoleId,
        ?int $excludeId = null
    ): bool {
        $builder = $this->builder()
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->where('reviewer_id', $reviewerId)
            ->where('reviewer_role_id', $reviewerRoleId);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Check whether this reviewer can review this employee.
     */
    public function isAssignedReviewer(
        int $cycleId,
        int $employeeId,
        int $reviewerId,
        int $reviewerRoleId
    ): bool {
        return $this->builder()
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->where('reviewer_id', $reviewerId)
            ->where('reviewer_role_id', $reviewerRoleId)
            ->where('status', 'active')
            ->countAllResults() > 0;
    }

    /**
     * Get reviewer assignment.
     */
    public function getAssignment(int $id): ?array
    {
        return $this
            ->where('id', $id)
            ->first();
    }

    /**
     * Get assignments for one employee grouped by reviewer role.
     */
    public function getEmployeeReviewerRoles(
        int $cycleId,
        int $employeeId
    ): array {
        return $this->builder()
            ->select([
                'appraisal_reviewer_assignments.reviewer_role_id',
                'roles.name AS reviewer_role_name',
                'roles.display_name AS reviewer_role_display_name'
            ])
            ->join(
                'roles',
                'roles.id = appraisal_reviewer_assignments.reviewer_role_id',
                'left'
            )
            ->where(
                'appraisal_reviewer_assignments.appraisal_cycle_id',
                $cycleId
            )
            ->where(
                'appraisal_reviewer_assignments.employee_id',
                $employeeId
            )
            ->where(
                'appraisal_reviewer_assignments.status',
                'active'
            )
            ->groupBy(
                'appraisal_reviewer_assignments.reviewer_role_id'
            )
            ->get()
            ->getResultArray();
    }
}
