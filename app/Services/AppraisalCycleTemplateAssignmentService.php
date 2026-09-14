<?php

namespace App\Services;

use App\Models\AppraisalCycleModel;
use App\Models\AppraisalCycleParticipantModel;
use App\Models\AppraisalCycleTemplateAssignmentModel;
use App\Models\AppraisalTemplateModel;
use InvalidArgumentException;
use RuntimeException;

class AppraisalCycleTemplateAssignmentService
{
    protected AppraisalCycleTemplateAssignmentModel $assignments;
    protected AppraisalCycleModel $cycles;
    protected AppraisalTemplateModel $templates;
    protected AppraisalCycleParticipantModel $participants;

    public function __construct()
    {
        $this->assignments = new AppraisalCycleTemplateAssignmentModel();
        $this->cycles = new AppraisalCycleModel();
        $this->templates = new AppraisalTemplateModel();
        $this->participants = new AppraisalCycleParticipantModel();
    }

    public function getAssignments(int $cycleId): array
    {
        $this->validateCycle($cycleId);
        return $this->assignments->getAssignments($cycleId);
    }

    public function createAssignment(int $cycleId, array $data): int
    {
        $cycle = $this->validateCycle($cycleId);

        $reviewType = trim($data['review_type'] ?? '');
        $assignmentType = trim($data['assignment_type'] ?? '');
        $templateId = (int)($data['template_id'] ?? 0);
        $reviewerRoleId = isset($data['reviewer_role_id']) && $data['reviewer_role_id'] !== ''
            ? (int)$data['reviewer_role_id']
            : null;

        $this->validateAssignmentInput(
            $reviewType,
            $assignmentType,
            $templateId,
            $reviewerRoleId
        );

        if ($reviewType === 'self') {
            $reviewerRoleId = null;
        }

        $organizationId = (int)$cycle['organization_id'];

        $this->validateTemplate($templateId, $organizationId);

        if ($reviewType === 'matrix') {
            $this->validateReviewerRole($reviewerRoleId);
            $this->validateMatrixConfiguration($organizationId, $reviewerRoleId);
        }

        $targetId = $this->getTargetId($assignmentType, $data);

        if ($targetId <= 0) {
            throw new InvalidArgumentException(ucfirst($assignmentType) . ' is required.');
        }

        $this->validateTarget($assignmentType, $targetId, $organizationId);

        if ($assignmentType === 'employee') {
            $this->validateParticipant($cycleId, $targetId);
        }

        if ($this->assignments->assignmentExists(
            $cycleId,
            $reviewType,
            $reviewerRoleId,
            $assignmentType,
            $targetId
        )) {
            throw new InvalidArgumentException(
                $reviewType === 'matrix'
                    ? 'A template assignment already exists for this reviewer role and target.'
                    : 'A template assignment already exists for this review type and target.'
            );
        }

        $insertData = [
            'appraisal_cycle_id' => $cycleId,
            'template_id' => $templateId,
            'review_type' => $reviewType,
            'reviewer_role_id' => $reviewerRoleId,
            'assignment_type' => $assignmentType,
            'department_id' => null,
            'designation_id' => null,
            'employee_id' => null,
            'priority' => $this->getPriority($assignmentType)
        ];

        $this->setAssignmentTarget($insertData, $assignmentType, $targetId);

        $db = db_connect();

        $db->transBegin();

        try {
            $assignmentId = $this->assignments->insert($insertData, true);

            if (!$assignmentId) {
                throw new RuntimeException('Unable to create template assignment.');
            }

            /*
             * A new assignment can immediately affect participants.
             * Materialize the resulting reviews only when the cycle is active.
             */
            if (($cycle['status'] ?? null) === 'active') {
                $employeeIds = $this->getAffectedEmployeeIds($cycleId, $insertData);

                foreach ($employeeIds as $employeeId) {
                    $this->synchronizeEmployeeReview(
                        $cycleId,
                        $employeeId,
                        $reviewType,
                        $reviewerRoleId
                    );
                }
            }

            if ($db->transStatus() === false) {
                throw new RuntimeException('Unable to create template assignment.');
            }

            $db->transCommit();

            return (int)$assignmentId;
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function updateAssignment(int $cycleId, int $assignmentId, array $data): void
    {
        $assignment = $this->assignments
            ->where('id', $assignmentId)
            ->where('appraisal_cycle_id', $cycleId)
            ->first();

        if (!$assignment) {
            throw new RuntimeException('Template assignment not found.');
        }

        if (!$this->canModifyAssignment($cycleId, $assignment)) {
            throw new RuntimeException(
                'This assignment cannot be modified because one or more related appraisals are already in progress or completed.'
            );
        }

        $cycle = $this->validateCycle($cycleId);

        $reviewType = trim($data['review_type'] ?? '');
        $assignmentType = trim($data['assignment_type'] ?? '');
        $templateId = (int)($data['template_id'] ?? 0);

        $reviewerRoleId = isset($data['reviewer_role_id']) && $data['reviewer_role_id'] !== ''
            ? (int)$data['reviewer_role_id']
            : null;

        $this->validateAssignmentInput(
            $reviewType,
            $assignmentType,
            $templateId,
            $reviewerRoleId
        );

        if ($reviewType === 'self') {
            $reviewerRoleId = null;
        }

        $organizationId = (int)$cycle['organization_id'];

        $this->validateTemplate($templateId, $organizationId);

        if ($reviewType === 'matrix') {
            $this->validateReviewerRole($reviewerRoleId);
            $this->validateMatrixConfiguration($organizationId, $reviewerRoleId);
        }

        $targetId = $this->getTargetId($assignmentType, $data);

        if ($targetId <= 0) {
            throw new InvalidArgumentException(ucfirst($assignmentType) . ' is required.');
        }

        $this->validateTarget($assignmentType, $targetId, $organizationId);

        if ($assignmentType === 'employee') {
            $this->validateParticipant($cycleId, $targetId);
        }

        if ($this->assignments->assignmentExists(
            $cycleId,
            $reviewType,
            $reviewerRoleId,
            $assignmentType,
            $targetId,
            $assignmentId
        )) {
            throw new InvalidArgumentException(
                $reviewType === 'matrix'
                    ? 'A template assignment already exists for this reviewer role and target.'
                    : 'A template assignment already exists for this review type and target.'
            );
        }

        $updateData = [
            'template_id' => $templateId,
            'review_type' => $reviewType,
            'reviewer_role_id' => $reviewerRoleId,
            'assignment_type' => $assignmentType,
            'department_id' => null,
            'designation_id' => null,
            'employee_id' => null,
            'priority' => $this->getPriority($assignmentType)
        ];

        $this->setAssignmentTarget($updateData, $assignmentType, $targetId);

        $db = db_connect();

        $db->transBegin();

        try {
            $affectedEmployeeIds = array_unique(array_merge(
                $this->getAffectedEmployeeIds($cycleId, $assignment),
                $this->getAffectedEmployeeIds($cycleId, $updateData)
            ));

            if (!$this->assignments->update($assignmentId, $updateData)) {
                throw new RuntimeException('Unable to update template assignment.');
            }

            /*
             * Only pending appraisals can be changed.
             * Existing submitted/in-progress/approved/rejected records are
             * intentionally left untouched because the assignment was editable
             * only when no locked appraisal existed.
             */
            if (!empty($affectedEmployeeIds) && ($cycle['status'] ?? null) === 'active') {
                foreach ($affectedEmployeeIds as $employeeId) {
                    $this->synchronizeEmployeeReview(
                        $cycleId,
                        (int)$employeeId,
                        $assignment['review_type'] ?? null,
                        !empty($assignment['reviewer_role_id'])
                            ? (int)$assignment['reviewer_role_id']
                            : null
                    );

                    $this->synchronizeEmployeeReview(
                        $cycleId,
                        (int)$employeeId,
                        $reviewType,
                        $reviewerRoleId
                    );
                }
            }

            if ($db->transStatus() === false) {
                throw new RuntimeException('Unable to update template assignment.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    protected function canModifyAssignment(int $cycleId, array $assignment): bool
    {
        $employeeIds = $this->getAffectedEmployeeIds($cycleId, $assignment);

        if (empty($employeeIds)) {
            return true;
        }

        $db = db_connect();

        $builder = $db->table('appraisals')
            ->where('appraisal_cycle_id', $cycleId)
            ->whereIn('employee_id', $employeeIds);

        $builder->groupStart();

        if (($assignment['review_type'] ?? null) === 'self') {
            $builder->where('review_type', 'self');
        }

        if (($assignment['review_type'] ?? null) === 'matrix' && !empty($assignment['reviewer_role_id'])) {
            $builder
                ->orGroupStart()
                ->where('review_type', 'matrix')
                ->where('reviewer_role_id', (int)$assignment['reviewer_role_id'])
                ->groupEnd();
        }

        $builder->groupEnd();

        /*
         * Anything other than pending is locked.
         *
         * pending      = assignment may still change
         * in_progress  = locked
         * submitted    = locked
         * approved     = locked
         * rejected     = locked
         */
        return $builder
            ->where('status !=', 'pending')
            ->countAllResults() === 0;
    }

    protected function getAffectedEmployeeIds(int $cycleId, array $assignment): array
    {
        $db = db_connect();

        $builder = $db
            ->table('appraisal_cycle_participants acp')
            ->select('acp.employee_id')
            ->join('users u', 'u.id = acp.employee_id')
            ->where('acp.appraisal_cycle_id', $cycleId)
            ->where('acp.status', 'active')
            ->where('u.status', 'active');

        switch ($assignment['assignment_type'] ?? null) {
            case 'department':
                $departmentId = (int)($assignment['department_id'] ?? 0);

                if ($departmentId <= 0) {
                    return [];
                }

                $builder->where('u.department_id', $departmentId);
                break;

            case 'designation':
                $designationId = (int)($assignment['designation_id'] ?? 0);

                if ($designationId <= 0) {
                    return [];
                }

                $builder->where('u.designation_id', $designationId);
                break;

            case 'employee':
                $employeeId = (int)($assignment['employee_id'] ?? 0);

                if ($employeeId <= 0) {
                    return [];
                }

                $builder->where('acp.employee_id', $employeeId);
                break;

            default:
                return [];
        }

        return array_values(array_unique(array_map(
            'intval',
            array_column($builder->get()->getResultArray(), 'employee_id')
        )));
    }

    protected function synchronizeEmployeeReview(
        int $cycleId,
        int $employeeId,
        ?string $reviewType,
        ?int $reviewerRoleId
    ): void {
        if ($employeeId <= 0 || !in_array($reviewType, ['self', 'matrix'], true)) {
            return;
        }

        if ($reviewType === 'self') {
            $this->synchronizeSelfReview($cycleId, $employeeId);
            return;
        }

        if ($reviewType === 'matrix' && $reviewerRoleId) {
            $this->synchronizeMatrixReviews(
                $cycleId,
                $employeeId,
                $reviewerRoleId
            );
        }
    }

    protected function synchronizeSelfReview(int $cycleId, int $employeeId): void
    {
        $db = db_connect();

        $employee = $db->table('users')
            ->select('id, organization_id')
            ->where('id', $employeeId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$employee) {
            return;
        }

        $template = $this->assignments->resolveTemplate(
            $cycleId,
            $employeeId,
            'self'
        );

        $appraisal = $db->table('appraisals')
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->where('reviewer_id', $employeeId)
            ->where('review_type', 'self')
            ->get()
            ->getRowArray();

        if (!$template) {
            return;
        }

        $templateId = (int)$template['template_id'];

        if ($appraisal) {
            if ($appraisal['status'] !== 'pending') {
                return;
            }

            if ((int)$appraisal['template_id'] === $templateId) {
                return;
            }

            $this->resetPendingAppraisal(
                (int)$appraisal['id'],
                $templateId
            );

            return;
        }

        $role = $db->table('user_roles')
            ->select('role_id')
            ->where('user_id', $employeeId)
            ->get()
            ->getRowArray();

        if (!$role) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        $db->table('appraisals')->insert([
            'appraisal_cycle_id' => $cycleId,
            'employee_id' => $employeeId,
            'reviewer_id' => $employeeId,
            'reviewer_role_id' => (int)$role['role_id'],
            'review_type' => 'self',
            'template_id' => $templateId,
            'status' => 'pending',
            'overall_score' => 0,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }

    protected function synchronizeMatrixReviews(
        int $cycleId,
        int $employeeId,
        int $reviewerRoleId
    ): void {
        $db = db_connect();

        $employee = $db->table('users')
            ->select('id, organization_id')
            ->where('id', $employeeId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$employee) {
            return;
        }

        $employeeRole = $db->table('user_roles')
            ->select('role_id')
            ->where('user_id', $employeeId)
            ->get()
            ->getRowArray();

        if (!$employeeRole) {
            return;
        }

        $matrix = $db->table('review_matrix')
            ->select('id')
            ->where('organization_id', $employee['organization_id'])
            ->where('reviewer_role_id', $reviewerRoleId)
            ->where('reviewee_role_id', (int)$employeeRole['role_id'])
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$matrix) {
            return;
        }

        $template = $this->assignments->resolveMatrixTemplate(
            $cycleId,
            $employeeId,
            $reviewerRoleId
        );

        if (!$template) {
            return;
        }

        $reviewers = $db->table('users u')
            ->select('u.id, ur.role_id')
            ->join('user_roles ur', 'ur.user_id = u.id')
            ->where('u.organization_id', $employee['organization_id'])
            ->where('u.status', 'active')
            ->where('ur.role_id', $reviewerRoleId)
            ->get()
            ->getResultArray();

        if (empty($reviewers)) {
            return;
        }

        foreach ($reviewers as $reviewer) {
            $reviewerId = (int)$reviewer['id'];

            /*
             * An employee cannot become their own Matrix reviewer unless the
             * matrix explicitly allows self-review. Normal Matrix assignments
             * therefore exclude the employee from the reviewer list.
             */
            if ($reviewerId === $employeeId) {
                $allowSelf = $db->table('review_matrix')
                    ->select('id')
                    ->where('id', $matrix['id'])
                    ->where('allow_self_review', 1)
                    ->get()
                    ->getRowArray();

                if (!$allowSelf) {
                    continue;
                }
            }

            $this->createOrUpdateReviewerAssignment(
                $cycleId,
                $employeeId,
                $reviewerId,
                $reviewerRoleId,
                (int)$matrix['id']
            );

            $this->createOrUpdateMatrixAppraisal(
                $cycleId,
                $employeeId,
                $reviewerId,
                $reviewerRoleId,
                (int)$matrix['id'],
                (int)$template['template_id']
            );
        }
    }

    protected function createOrUpdateReviewerAssignment(
        int $cycleId,
        int $employeeId,
        int $reviewerId,
        int $reviewerRoleId,
        int $reviewMatrixId
    ): void {
        $db = db_connect();

        $existing = $db->table('appraisal_reviewer_assignments')
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->where('reviewer_id', $reviewerId)
            ->where('reviewer_role_id', $reviewerRoleId)
            ->get()
            ->getRowArray();

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            if ($existing['status'] !== 'active') {
                $db->table('appraisal_reviewer_assignments')
                    ->where('id', (int)$existing['id'])
                    ->update([
                        'review_matrix_id' => $reviewMatrixId,
                        'status' => 'active',
                        'updated_at' => $now
                    ]);
            }

            return;
        }

        $db->table('appraisal_reviewer_assignments')->insert([
            'appraisal_cycle_id' => $cycleId,
            'employee_id' => $employeeId,
            'reviewer_id' => $reviewerId,
            'reviewer_role_id' => $reviewerRoleId,
            'review_matrix_id' => $reviewMatrixId,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }

    protected function createOrUpdateMatrixAppraisal(
        int $cycleId,
        int $employeeId,
        int $reviewerId,
        int $reviewerRoleId,
        int $reviewMatrixId,
        int $templateId
    ): void {
        $db = db_connect();

        $existing = $db->table('appraisals')
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->where('reviewer_id', $reviewerId)
            ->where('reviewer_role_id', $reviewerRoleId)
            ->where('review_type', 'matrix')
            ->get()
            ->getRowArray();

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            if ($existing['status'] !== 'pending') {
                return;
            }

            if ((int)$existing['template_id'] !== $templateId) {
                $this->resetPendingAppraisal(
                    (int)$existing['id'],
                    $templateId
                );
            }

            return;
        }

        $db->table('appraisals')->insert([
            'appraisal_cycle_id' => $cycleId,
            'employee_id' => $employeeId,
            'reviewer_id' => $reviewerId,
            'reviewer_role_id' => $reviewerRoleId,
            'review_type' => 'matrix',
            'template_id' => $templateId,
            'status' => 'pending',
            'overall_score' => 0,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }

    protected function resetPendingAppraisal(
        int $appraisalId,
        int $templateId
    ): void {
        $db = db_connect();

        $db->table('appraisals')
            ->where('id', $appraisalId)
            ->where('status', 'pending')
            ->update([
                'template_id' => $templateId,
                'overall_score' => 0,
                'overall_comment' => null,
                'submitted_at' => null,
                'approved_at' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        $db->table('appraisal_answers')
            ->where('appraisal_id', $appraisalId)
            ->delete();
    }

    public function deleteAssignment(int $cycleId, int $assignmentId): void
    {
        $this->validateCycle($cycleId);

        $assignment = $this->assignments
            ->where('id', $assignmentId)
            ->where('appraisal_cycle_id', $cycleId)
            ->first();

        if (!$assignment) {
            throw new RuntimeException('Template assignment not found.');
        }

        if (!$this->canModifyAssignment($cycleId, $assignment)) {
            throw new RuntimeException(
                'This assignment cannot be deleted because one or more related appraisals are already in progress or completed.'
            );
        }

        $db = db_connect();

        $db->transBegin();

        try {
            $employeeIds = $this->getAffectedEmployeeIds($cycleId, $assignment);

            if (!$this->assignments->delete($assignmentId)) {
                throw new RuntimeException('Unable to remove template assignment.');
            }

            /*
             * Remove only pending materialized records whose assignment
             * disappears. Historical/in-progress/completed records remain.
             */
            if (!empty($employeeIds)) {
                foreach ($employeeIds as $employeeId) {
                    $this->removeObsoletePendingReviews(
                        $cycleId,
                        (int)$employeeId,
                        $assignment
                    );
                }
            }

            if ($db->transStatus() === false) {
                throw new RuntimeException('Unable to remove template assignment.');
            }

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    protected function removeObsoletePendingReviews(
        int $cycleId,
        int $employeeId,
        array $assignment
    ): void {
        $db = db_connect();

        if (($assignment['review_type'] ?? null) === 'self') {
            $appraisals = $db->table('appraisals')
                ->select('id')
                ->where('appraisal_cycle_id', $cycleId)
                ->where('employee_id', $employeeId)
                ->where('reviewer_id', $employeeId)
                ->where('review_type', 'self')
                ->where('status', 'pending')
                ->get()
                ->getResultArray();

            foreach ($appraisals as $appraisal) {
                $db->table('appraisals')
                    ->where('id', (int)$appraisal['id'])
                    ->where('status', 'pending')
                    ->delete();
            }

            return;
        }

        if (($assignment['review_type'] ?? null) !== 'matrix' || empty($assignment['reviewer_role_id'])) {
            return;
        }

        $reviewerRoleId = (int)$assignment['reviewer_role_id'];

        $appraisals = $db->table('appraisals')
            ->select('id, reviewer_id')
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->where('review_type', 'matrix')
            ->where('reviewer_role_id', $reviewerRoleId)
            ->where('status', 'pending')
            ->get()
            ->getResultArray();

        foreach ($appraisals as $appraisal) {
            $db->table('appraisal_reviewer_assignments')
                ->where('appraisal_cycle_id', $cycleId)
                ->where('employee_id', $employeeId)
                ->where('reviewer_id', (int)$appraisal['reviewer_id'])
                ->where('reviewer_role_id', $reviewerRoleId)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            $db->table('appraisals')
                ->where('id', (int)$appraisal['id'])
                ->where('status', 'pending')
                ->delete();
        }
    }

    protected function validateAssignmentInput(
        string $reviewType,
        string $assignmentType,
        int $templateId,
        ?int $reviewerRoleId
    ): void {
        if (!in_array($reviewType, ['self', 'matrix'], true)) {
            throw new InvalidArgumentException('Invalid review type.');
        }

        if (!in_array($assignmentType, ['department', 'designation', 'employee'], true)) {
            throw new InvalidArgumentException('Invalid assignment type.');
        }

        if ($templateId <= 0) {
            throw new InvalidArgumentException('Appraisal template is required.');
        }

        if ($reviewType === 'matrix' && (!$reviewerRoleId || $reviewerRoleId <= 0)) {
            throw new InvalidArgumentException('Reviewer role is required for matrix review.');
        }
    }

    protected function validateMatrixConfiguration(
        int $organizationId,
        int $reviewerRoleId
    ): void {
        $db = db_connect();

        $exists = $db->table('review_matrix')
            ->select('id')
            ->where('organization_id', $organizationId)
            ->where('reviewer_role_id', $reviewerRoleId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$exists) {
            throw new InvalidArgumentException(
                'No active review matrix configuration exists for the selected reviewer role.'
            );
        }
    }

    protected function validateCycle(int $cycleId): array
    {
        $cycle = $this->cycles->find($cycleId);

        if (!$cycle) {
            throw new RuntimeException('Appraisal cycle not found.');
        }

        return $cycle;
    }

    protected function validateTemplate(int $templateId, int $organizationId): void
    {
        $template = $this->templates
            ->where('id', $templateId)
            ->where('organization_id', $organizationId)
            ->where('status', 'active')
            ->first();

        if (!$template) {
            throw new InvalidArgumentException('Selected appraisal template is invalid.');
        }
    }

    protected function validateReviewerRole(?int $reviewerRoleId): void
    {
        if (!$reviewerRoleId || $reviewerRoleId <= 0) {
            throw new InvalidArgumentException('Reviewer role is required.');
        }

        $role = db_connect()
            ->table('roles')
            ->select('id')
            ->where('id', $reviewerRoleId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$role) {
            throw new InvalidArgumentException('Selected reviewer role is invalid or inactive.');
        }
    }

    protected function validateParticipant(int $cycleId, int $employeeId): void
    {
        $participant = $this->participants
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->where('status', 'active')
            ->first();

        if (!$participant) {
            throw new InvalidArgumentException('Employee is not an active participant in this cycle.');
        }
    }

    protected function validateTarget(
        string $assignmentType,
        int $targetId,
        int $organizationId
    ): void {
        $table = match ($assignmentType) {
            'department' => 'departments',
            'designation' => 'designations',
            'employee' => 'users',
            default => null
        };

        if (!$table) {
            throw new InvalidArgumentException('Invalid assignment target.');
        }

        $target = db_connect()
            ->table($table)
            ->where('id', $targetId)
            ->where('organization_id', $organizationId)
            ->get()
            ->getRowArray();

        if (!$target) {
            throw new InvalidArgumentException(
                ucfirst($assignmentType) . ' does not belong to this organization.'
            );
        }
    }

    protected function getTargetId(
        string $assignmentType,
        array $data
    ): int {
        return match ($assignmentType) {
            'department' => (int)($data['department_id'] ?? 0),
            'designation' => (int)($data['designation_id'] ?? 0),
            'employee' => (int)($data['employee_id'] ?? 0),
            default => 0
        };
    }

    protected function setAssignmentTarget(
        array &$data,
        string $assignmentType,
        int $targetId
    ): void {
        switch ($assignmentType) {
            case 'department':
                $data['department_id'] = $targetId;
                break;

            case 'designation':
                $data['designation_id'] = $targetId;
                break;

            case 'employee':
                $data['employee_id'] = $targetId;
                break;
        }
    }

    protected function getPriority(string $assignmentType): int
    {
        return match ($assignmentType) {
            'department' => 100,
            'designation' => 200,
            'employee' => 300,
            default => 0
        };
    }

    public function getAssignmentOptions(int $cycleId): array
    {
        $cycle = $this->validateCycle($cycleId);
        $organizationId = (int)$cycle['organization_id'];
        $db = db_connect();

        $departments = $db
            ->table('departments')
            ->select('id, name')
            ->where('organization_id', $organizationId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $designations = $db
            ->table('designations')
            ->select('id, title AS name')
            ->where('organization_id', $organizationId)
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        $employees = $db
            ->table('appraisal_cycle_participants acp')
            ->select([
                'u.id',
                'u.first_name',
                'u.last_name',
                'u.employee_code'
            ])
            ->join('users u', 'u.id = acp.employee_id')
            ->where('acp.appraisal_cycle_id', $cycleId)
            ->where('acp.status', 'active')
            ->where('u.status', 'active')
            ->orderBy('u.first_name', 'ASC')
            ->orderBy('u.last_name', 'ASC')
            ->get()
            ->getResultArray();

        $templates = $this->templates
            ->select('id, template_name AS name')
            ->where('organization_id', $organizationId)
            ->where('status', 'active')
            ->orderBy('template_name', 'ASC')
            ->findAll();

        $reviewerRoles = $db
            ->table('roles')
            ->select([
                'id',
                'name',
                'display_name'
            ])
            ->where('status', 'active')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('display_name', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'departments' => $departments,
            'designations' => $designations,
            'employees' => $employees,
            'templates' => $templates,
            'reviewer_roles' => $reviewerRoles
        ];
    }
}
