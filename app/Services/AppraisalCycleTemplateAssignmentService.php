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
    protected AppraisalCycleTemplateAssignmentModel
        $assignments;

    protected AppraisalCycleModel
        $cycles;

    protected AppraisalTemplateModel
        $templates;

    protected AppraisalCycleParticipantModel
        $participants;


    public function __construct()
    {
        $this->assignments =
            new AppraisalCycleTemplateAssignmentModel();

        $this->cycles =
            new AppraisalCycleModel();

        $this->templates =
            new AppraisalTemplateModel();

        $this->participants =
            new AppraisalCycleParticipantModel();
    }


    public function getAssignments(int $cycleId): array
    {
        $this->validateCycle($cycleId);
        return $this->assignments->getAssignments($cycleId);
    }


    public function createAssignment(int $cycleId, array $data): int
    {
        $cycle = $this->validateCycle($cycleId);
        $assignmentType = trim($data['assignment_type'] ?? '');
        $templateId = (int) ($data['template_id'] ?? 0);
        if (!in_array($assignmentType, ['department', 'designation', 'employee'], true)) {
            throw new InvalidArgumentException(
                'Invalid assignment type.'
            );
        }

        if ($templateId <= 0) {
            throw new InvalidArgumentException(
                'Appraisal template is required.'
            );
        }
        $this->validateTemplate($templateId, (int)$cycle['organization_id']);
        $targetId = $this->getTargetId($assignmentType, $data);

        if ($targetId <= 0) {
            throw new InvalidArgumentException(
                ucfirst($assignmentType)
                    . ' is required.'
            );
        }


        $this->validateTarget(
            $assignmentType,
            $targetId,
            (int)
            $cycle['organization_id']
        );


        if (
            $this
            ->assignments
            ->assignmentExists(
                $cycleId,
                $assignmentType,
                $targetId
            )
        ) {

            throw new InvalidArgumentException(
                'A template assignment already exists for this target.'
            );
        }


        $insertData = [
            'appraisal_cycle_id' => $cycleId,
            'template_id' => $templateId,
            'assignment_type' => $assignmentType,
            'priority' => $this->getPriority($assignmentType),
            'department_id' => null,
            'designation_id' => null,
            'employee_id' => null
        ];


        switch ($assignmentType) {
            case 'department':
                $insertData['department_id'] = $targetId;
                break;

            case 'designation':
                $insertData['designation_id'] = $targetId;
                break;

            case 'employee':
                $this->validateParticipant($cycleId, $targetId);
                $insertData['employee_id'] = $targetId;
                break;
        }

        $assignmentId = $this->assignments->insert($insertData, true);
        if (!$assignmentId) {
            throw new RuntimeException('Unable to create template assignment.');
        }
        return (int)$assignmentId;
    }


    public function updateAssignment(
        int $assignmentId,
        array $data
    ): void {

        $assignment =
            $this
            ->assignments
            ->find(
                $assignmentId
            );


        if (
            !$assignment
        ) {

            throw new RuntimeException(
                'Template assignment not found.'
            );
        }


        $cycle =
            $this->validateCycle(
                (int)
                $assignment['appraisal_cycle_id']
            );


        $templateId =
            (int) (
                $data['template_id']
                ?? 0
            );


        if (
            $templateId <= 0
        ) {

            throw new InvalidArgumentException(
                'Appraisal template is required.'
            );
        }


        $this->validateTemplate(
            $templateId,
            (int)
            $cycle['organization_id']
        );


        if (
            !$this
                ->assignments
                ->update(
                    $assignmentId,
                    [
                        'template_id' =>
                        $templateId
                    ]
                )
        ) {

            throw new RuntimeException(
                'Unable to update template assignment.'
            );
        }
        // $this->resolveTemplates(
        //     (int)
        //     $assignment['appraisal_cycle_id']
        // );
    }


    public function deleteAssignment(
        int $assignmentId
    ): void {

        $assignment =
            $this
            ->assignments
            ->find(
                $assignmentId
            );


        if (
            !$assignment
        ) {

            throw new RuntimeException(
                'Template assignment not found.'
            );
        }


        $cycleId =
            (int)
            $assignment['appraisal_cycle_id'];


        if (
            !$this
                ->assignments
                ->delete(
                    $assignmentId
                )
        ) {

            throw new RuntimeException(
                'Unable to delete template assignment.'
            );
        }
    }

    protected function validateCycle(
        int $cycleId
    ): array {

        $cycle =
            $this
            ->cycles
            ->find(
                $cycleId
            );


        if (
            !$cycle
        ) {

            throw new RuntimeException(
                'Appraisal cycle not found.'
            );
        }


        return $cycle;
    }


    protected function validateTemplate(
        int $templateId,
        int $organizationId
    ): void {

        $template =
            $this
            ->templates

            ->where(
                'id',
                $templateId
            )

            ->where(
                'organization_id',
                $organizationId
            )

            ->where(
                'status',
                'active'
            )

            ->first();


        if (
            !$template
        ) {

            throw new InvalidArgumentException(
                'Selected appraisal template is invalid.'
            );
        }
    }


    protected function validateParticipant(
        int $cycleId,
        int $employeeId
    ): void {

        $participant =
            $this
            ->participants

            ->where(
                'appraisal_cycle_id',
                $cycleId
            )

            ->where(
                'employee_id',
                $employeeId
            )

            ->where(
                'status',
                'active'
            )

            ->first();


        if (
            !$participant
        ) {

            throw new InvalidArgumentException(
                'Employee is not an active participant in this cycle.'
            );
        }
    }


    protected function validateTarget(
        string $assignmentType,
        int $targetId,
        int $organizationId
    ): void {

        $table =
            match ($assignmentType) {

                'department' =>
                'departments',

                'designation' =>
                'designations',

                'employee' =>
                'users',

                default =>
                null
            };


        if (
            !$table
        ) {

            throw new InvalidArgumentException(
                'Invalid assignment target.'
            );
        }


        $target =
            db_connect()

            ->table(
                $table
            )

            ->where(
                'id',
                $targetId
            )

            ->where(
                'organization_id',
                $organizationId
            )

            ->get()

            ->getRowArray();


        if (
            !$target
        ) {

            throw new InvalidArgumentException(
                ucfirst(
                    $assignmentType
                )
                    . ' does not belong to this organization.'
            );
        }
    }


    protected function getTargetId(string $assignmentType, array $data): int
    {

        return match ($assignmentType) {

            'department' =>
            (int) (
                $data['department_id']
                ?? 0
            ),

            'designation' =>
            (int) (
                $data['designation_id']
                ?? 0
            ),

            'employee' =>
            (int) (
                $data['employee_id']
                ?? 0
            ),

            default =>
            0
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
        $cycle =
            $this->validateCycle(
                $cycleId
            );

        $organizationId =
            (int)
            $cycle['organization_id'];

        $db = db_connect();

        $departments =
            $db
            ->table('departments')
            ->select('id, name')
            ->where(
                'organization_id',
                $organizationId
            )
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $designations =
            $db
            ->table('designations')
            ->select('id, title AS name')
            ->where(
                'organization_id',
                $organizationId
            )
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        $employees =
            $db
            ->table('appraisal_cycle_participants acp')
            ->select([
                'u.id',
                'u.first_name',
                'u.last_name',
                'u.employee_code'
            ])
            ->join(
                'users u',
                'u.id = acp.employee_id'
            )
            ->where(
                'acp.appraisal_cycle_id',
                $cycleId
            )
            ->where(
                'acp.status',
                'active'
            )
            ->orderBy(
                'u.first_name',
                'ASC'
            )
            ->get()
            ->getResultArray();

        $templates =
            $this
            ->templates
            ->select('id, template_name AS name')
            ->where(
                'organization_id',
                $organizationId
            )
            ->where(
                'status',
                'active'
            )
            ->orderBy(
                'template_name',
                'ASC'
            )
            ->findAll();

        return [
            'departments' => $departments,
            'designations' => $designations,
            'employees' => $employees,
            'templates' => $templates
        ];
    }
}
