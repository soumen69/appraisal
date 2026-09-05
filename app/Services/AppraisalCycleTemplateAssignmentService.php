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
        $reviewerRoleId = isset($data['reviewer_role_id']) && $data['reviewer_role_id'] !== '' ? (int)$data['reviewer_role_id'] : null;

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

        if ($reviewType === 'self') {
            $reviewerRoleId = null;
        }

        $organizationId = (int)$cycle['organization_id'];

        $this->validateTemplate($templateId, $organizationId);

        if ($reviewType === 'matrix') {
            $this->validateReviewerRole($reviewerRoleId);
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

        switch ($assignmentType) {
            case 'department':
                $insertData['department_id'] = $targetId;
                break;

            case 'designation':
                $insertData['designation_id'] = $targetId;
                break;

            case 'employee':
                $insertData['employee_id'] = $targetId;
                break;
        }

        $assignmentId = $this->assignments->insert($insertData, true);

        if (!$assignmentId) {
            throw new RuntimeException('Unable to create template assignment.');
        }

        return (int)$assignmentId;
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

        $cycle = $this->validateCycle($cycleId);

        $reviewType = trim($data['review_type'] ?? '');
        $assignmentType = trim($data['assignment_type'] ?? '');
        $templateId = (int)($data['template_id'] ?? 0);

        $reviewerRoleId = isset($data['reviewer_role_id']) && $data['reviewer_role_id'] !== ''
            ? (int)$data['reviewer_role_id']
            : null;

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

        if ($reviewType === 'self') {
            $reviewerRoleId = null;
        }

        $organizationId = (int)$cycle['organization_id'];

        $this->validateTemplate($templateId, $organizationId);

        if ($reviewType === 'matrix') {
            $this->validateReviewerRole($reviewerRoleId);
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

        switch ($assignmentType) {
            case 'department':
                $updateData['department_id'] = $targetId;
                break;

            case 'designation':
                $updateData['designation_id'] = $targetId;
                break;

            case 'employee':
                $updateData['employee_id'] = $targetId;
                break;
        }

        $db = db_connect();

        $db->transBegin();

        try {
            $affectedEmployeeIds = array_unique(array_merge(
                $this->getAffectedEmployeeIds($cycleId, $assignment),
                $this->getAffectedEmployeeIds($cycleId, array_merge($updateData, [
                    'appraisal_cycle_id' => $cycleId
                ]))
            ));

            if (!$this->assignments->update($assignmentId, $updateData)) {
                throw new RuntimeException('Unable to update template assignment.');
            }

            if (!empty($affectedEmployeeIds)) {
                $this->syncAffectedPendingAppraisals(
                    $cycleId,
                    $affectedEmployeeIds,
                    $assignment,
                    array_merge($updateData, [
                        'appraisal_cycle_id' => $cycleId
                    ])
                );
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

    protected function getAffectedEmployeeIds(int $cycleId, array $assignment): array
    {
        $db = db_connect();

        $builder = $db
            ->table('appraisal_cycle_participants acp')
            ->select('acp.employee_id')
            ->join('users u', 'u.id = acp.employee_id')
            ->where('acp.appraisal_cycle_id', $cycleId)
            ->where('acp.status', 'active');

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

        return array_map(
            'intval',
            array_column($builder->get()->getResultArray(), 'employee_id')
        );
    }


    protected function syncAffectedPendingAppraisals(
        int $cycleId,
        array $employeeIds,
        array $oldAssignment,
        array $newAssignment
    ): void {
        if (empty($employeeIds)) {
            return;
        }

        $db = db_connect();

        $oldReviewType = $oldAssignment['review_type'] ?? null;
        $oldReviewerRoleId = !empty($oldAssignment['reviewer_role_id'])
            ? (int)$oldAssignment['reviewer_role_id']
            : null;

        $newReviewType = $newAssignment['review_type'] ?? null;
        $newReviewerRoleId = !empty($newAssignment['reviewer_role_id'])
            ? (int)$newAssignment['reviewer_role_id']
            : null;

        $appraisalBuilder = $db
            ->table('appraisals')
            ->select([
                'id',
                'employee_id',
                'reviewer_id',
                'reviewer_role_id',
                'review_type',
                'template_id',
                'status'
            ])
            ->where('appraisal_cycle_id', $cycleId)
            ->whereIn('employee_id', $employeeIds)
            ->where('status', 'pending');

        $appraisalBuilder->groupStart();

        if ($oldReviewType === 'self') {
            $appraisalBuilder->orWhere('review_type', 'self');
        }

        if ($oldReviewType === 'matrix' && $oldReviewerRoleId) {
            $appraisalBuilder
                ->orGroupStart()
                ->where('review_type', 'matrix')
                ->where('reviewer_role_id', $oldReviewerRoleId)
                ->groupEnd();
        }

        if ($newReviewType === 'self') {
            $appraisalBuilder->orWhere('review_type', 'self');
        }

        if ($newReviewType === 'matrix' && $newReviewerRoleId) {
            $appraisalBuilder
                ->orGroupStart()
                ->where('review_type', 'matrix')
                ->where('reviewer_role_id', $newReviewerRoleId)
                ->groupEnd();
        }

        $appraisalBuilder->groupEnd();

        $appraisals = $appraisalBuilder
            ->get()
            ->getResultArray();

        foreach ($appraisals as $appraisal) {
            $this->syncSinglePendingAppraisal($cycleId, $appraisal);
        }
    }

    protected function syncSinglePendingAppraisal(
        int $cycleId,
        array $appraisal
    ): void {
        $db = db_connect();

        $employeeId = (int)$appraisal['employee_id'];
        $reviewType = $appraisal['review_type'];
        $reviewerRoleId = !empty($appraisal['reviewer_role_id'])
            ? (int)$appraisal['reviewer_role_id']
            : null;

        $resolvedTemplate = null;

        if ($reviewType === 'self') {
            $resolvedTemplate = $this->assignments->resolveTemplate(
                $cycleId,
                $employeeId,
                'self'
            );
        }

        if ($reviewType === 'matrix' && $reviewerRoleId) {
            $resolvedTemplate = $this->assignments->resolveMatrixTemplate(
                $cycleId,
                $employeeId,
                $reviewerRoleId
            );
        }

        if (!$resolvedTemplate || empty($resolvedTemplate['template_id'])) {
            return;
        }

        $resolvedTemplateId = (int)$resolvedTemplate['template_id'];

        if ($resolvedTemplateId === (int)$appraisal['template_id']) {
            return;
        }

        $db->table('appraisals')
            ->where('id', (int)$appraisal['id'])
            ->where('status', 'pending')
            ->update([
                'template_id' => $resolvedTemplateId,
                'overall_score' => 0,
                'overall_comment' => null,
                'submitted_at' => null,
                'approved_at' => null,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        $db->table('appraisal_answers')
            ->where('appraisal_id', (int)$appraisal['id'])
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

        if (!$this->assignments->delete($assignmentId)) {
            throw new RuntimeException(
                'Unable to remove template assignment.'
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

    protected function validateTarget(string $assignmentType, int $targetId, int $organizationId): void
    {
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
            throw new InvalidArgumentException(ucfirst($assignmentType) . ' does not belong to this organization.');
        }
    }

    protected function getTargetId(string $assignmentType, array $data): int
    {
        return match ($assignmentType) {
            'department' => (int)($data['department_id'] ?? 0),
            'designation' => (int)($data['designation_id'] ?? 0),
            'employee' => (int)($data['employee_id'] ?? 0),
            default => 0
        };
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
            ->orderBy('u.first_name', 'ASC')
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







// class AppraisalCycleTemplateAssignmentService
// {
//     protected AppraisalCycleTemplateAssignmentModel
//         $assignments;

//     protected AppraisalCycleModel
//         $cycles;

//     protected AppraisalTemplateModel
//         $templates;

//     protected AppraisalCycleParticipantModel
//         $participants;


//     public function __construct()
//     {
//         $this->assignments =
//             new AppraisalCycleTemplateAssignmentModel();

//         $this->cycles =
//             new AppraisalCycleModel();

//         $this->templates =
//             new AppraisalTemplateModel();

//         $this->participants =
//             new AppraisalCycleParticipantModel();
//     }


//     public function getAssignments(int $cycleId): array
//     {
//         $this->validateCycle($cycleId);
//         return $this->assignments->getAssignments($cycleId);
//     }


//     public function createAssignment(int $cycleId, array $data): int
//     {
//         $cycle = $this->validateCycle($cycleId);
//         $assignmentType = trim($data['assignment_type'] ?? '');
//         $templateId = (int) ($data['template_id'] ?? 0);
//         if (!in_array($assignmentType, ['department', 'designation', 'employee'], true)) {
//             throw new InvalidArgumentException(
//                 'Invalid assignment type.'
//             );
//         }

//         if ($templateId <= 0) {
//             throw new InvalidArgumentException(
//                 'Appraisal template is required.'
//             );
//         }
//         $this->validateTemplate($templateId, (int)$cycle['organization_id']);
//         $targetId = $this->getTargetId($assignmentType, $data);

//         if ($targetId <= 0) {
//             throw new InvalidArgumentException(
//                 ucfirst($assignmentType)
//                     . ' is required.'
//             );
//         }


//         $this->validateTarget(
//             $assignmentType,
//             $targetId,
//             (int)
//             $cycle['organization_id']
//         );


//         if (
//             $this
//             ->assignments
//             ->assignmentExists(
//                 $cycleId,
//                 $assignmentType,
//                 $targetId
//             )
//         ) {

//             throw new InvalidArgumentException(
//                 'A template assignment already exists for this target.'
//             );
//         }


//         $insertData = [
//             'appraisal_cycle_id' => $cycleId,
//             'template_id' => $templateId,
//             'assignment_type' => $assignmentType,
//             'priority' => $this->getPriority($assignmentType),
//             'department_id' => null,
//             'designation_id' => null,
//             'employee_id' => null
//         ];


//         switch ($assignmentType) {
//             case 'department':
//                 $insertData['department_id'] = $targetId;
//                 break;

//             case 'designation':
//                 $insertData['designation_id'] = $targetId;
//                 break;

//             case 'employee':
//                 $this->validateParticipant($cycleId, $targetId);
//                 $insertData['employee_id'] = $targetId;
//                 break;
//         }

//         $assignmentId = $this->assignments->insert($insertData, true);
//         if (!$assignmentId) {
//             throw new RuntimeException('Unable to create template assignment.');
//         }
//         return (int)$assignmentId;
//     }


//     public function updateAssignment(
//         int $assignmentId,
//         array $data
//     ): void {

//         $assignment =
//             $this
//             ->assignments
//             ->find(
//                 $assignmentId
//             );


//         if (
//             !$assignment
//         ) {

//             throw new RuntimeException(
//                 'Template assignment not found.'
//             );
//         }


//         $cycle =
//             $this->validateCycle(
//                 (int)
//                 $assignment['appraisal_cycle_id']
//             );


//         $templateId =
//             (int) (
//                 $data['template_id']
//                 ?? 0
//             );


//         if (
//             $templateId <= 0
//         ) {

//             throw new InvalidArgumentException(
//                 'Appraisal template is required.'
//             );
//         }


//         $this->validateTemplate(
//             $templateId,
//             (int)
//             $cycle['organization_id']
//         );


//         if (
//             !$this
//                 ->assignments
//                 ->update(
//                     $assignmentId,
//                     [
//                         'template_id' =>
//                         $templateId
//                     ]
//                 )
//         ) {

//             throw new RuntimeException(
//                 'Unable to update template assignment.'
//             );
//         }
//         // $this->resolveTemplates(
//         //     (int)
//         //     $assignment['appraisal_cycle_id']
//         // );
//     }


//     public function deleteAssignment(
//         int $assignmentId
//     ): void {

//         $assignment =
//             $this
//             ->assignments
//             ->find(
//                 $assignmentId
//             );


//         if (
//             !$assignment
//         ) {

//             throw new RuntimeException(
//                 'Template assignment not found.'
//             );
//         }


//         $cycleId =
//             (int)
//             $assignment['appraisal_cycle_id'];


//         if (
//             !$this
//                 ->assignments
//                 ->delete(
//                     $assignmentId
//                 )
//         ) {

//             throw new RuntimeException(
//                 'Unable to delete template assignment.'
//             );
//         }
//     }

//     protected function validateCycle(
//         int $cycleId
//     ): array {

//         $cycle =
//             $this
//             ->cycles
//             ->find(
//                 $cycleId
//             );


//         if (
//             !$cycle
//         ) {

//             throw new RuntimeException(
//                 'Appraisal cycle not found.'
//             );
//         }


//         return $cycle;
//     }


//     protected function validateTemplate(
//         int $templateId,
//         int $organizationId
//     ): void {

//         $template =
//             $this
//             ->templates

//             ->where(
//                 'id',
//                 $templateId
//             )

//             ->where(
//                 'organization_id',
//                 $organizationId
//             )

//             ->where(
//                 'status',
//                 'active'
//             )

//             ->first();


//         if (
//             !$template
//         ) {

//             throw new InvalidArgumentException(
//                 'Selected appraisal template is invalid.'
//             );
//         }
//     }


//     protected function validateParticipant(
//         int $cycleId,
//         int $employeeId
//     ): void {

//         $participant =
//             $this
//             ->participants

//             ->where(
//                 'appraisal_cycle_id',
//                 $cycleId
//             )

//             ->where(
//                 'employee_id',
//                 $employeeId
//             )

//             ->where(
//                 'status',
//                 'active'
//             )

//             ->first();


//         if (
//             !$participant
//         ) {

//             throw new InvalidArgumentException(
//                 'Employee is not an active participant in this cycle.'
//             );
//         }
//     }


//     protected function validateTarget(
//         string $assignmentType,
//         int $targetId,
//         int $organizationId
//     ): void {

//         $table =
//             match ($assignmentType) {

//                 'department' =>
//                 'departments',

//                 'designation' =>
//                 'designations',

//                 'employee' =>
//                 'users',

//                 default =>
//                 null
//             };


//         if (
//             !$table
//         ) {

//             throw new InvalidArgumentException(
//                 'Invalid assignment target.'
//             );
//         }


//         $target =
//             db_connect()

//             ->table(
//                 $table
//             )

//             ->where(
//                 'id',
//                 $targetId
//             )

//             ->where(
//                 'organization_id',
//                 $organizationId
//             )

//             ->get()

//             ->getRowArray();


//         if (
//             !$target
//         ) {

//             throw new InvalidArgumentException(
//                 ucfirst(
//                     $assignmentType
//                 )
//                     . ' does not belong to this organization.'
//             );
//         }
//     }


//     protected function getTargetId(string $assignmentType, array $data): int
//     {

//         return match ($assignmentType) {

//             'department' =>
//             (int) (
//                 $data['department_id']
//                 ?? 0
//             ),

//             'designation' =>
//             (int) (
//                 $data['designation_id']
//                 ?? 0
//             ),

//             'employee' =>
//             (int) (
//                 $data['employee_id']
//                 ?? 0
//             ),

//             default =>
//             0
//         };
//     }


//     protected function getPriority(string $assignmentType): int
//     {
//         return match ($assignmentType) {
//             'department' => 100,
//             'designation' => 200,
//             'employee' => 300,
//             default => 0
//         };
//     }

//     public function getAssignmentOptions(int $cycleId): array
//     {
//         $cycle =
//             $this->validateCycle(
//                 $cycleId
//             );

//         $organizationId =
//             (int)
//             $cycle['organization_id'];

//         $db = db_connect();

//         $departments =
//             $db
//             ->table('departments')
//             ->select('id, name')
//             ->where(
//                 'organization_id',
//                 $organizationId
//             )
//             ->orderBy('name', 'ASC')
//             ->get()
//             ->getResultArray();

//         $designations =
//             $db
//             ->table('designations')
//             ->select('id, title AS name')
//             ->where(
//                 'organization_id',
//                 $organizationId
//             )
//             ->orderBy('title', 'ASC')
//             ->get()
//             ->getResultArray();

//         $employees =
//             $db
//             ->table('appraisal_cycle_participants acp')
//             ->select([
//                 'u.id',
//                 'u.first_name',
//                 'u.last_name',
//                 'u.employee_code'
//             ])
//             ->join(
//                 'users u',
//                 'u.id = acp.employee_id'
//             )
//             ->where(
//                 'acp.appraisal_cycle_id',
//                 $cycleId
//             )
//             ->where(
//                 'acp.status',
//                 'active'
//             )
//             ->orderBy(
//                 'u.first_name',
//                 'ASC'
//             )
//             ->get()
//             ->getResultArray();

//         $templates =
//             $this
//             ->templates
//             ->select('id, template_name AS name')
//             ->where(
//                 'organization_id',
//                 $organizationId
//             )
//             ->where(
//                 'status',
//                 'active'
//             )
//             ->orderBy(
//                 'template_name',
//                 'ASC'
//             )
//             ->findAll();

//         return [
//             'departments' => $departments,
//             'designations' => $designations,
//             'employees' => $employees,
//             'templates' => $templates
//         ];
//     }
// }
