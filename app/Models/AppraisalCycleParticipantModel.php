<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalCycleParticipantModel extends Model
{
    protected $table = 'appraisal_cycle_participants';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'appraisal_cycle_id',
        'employee_id',
        'status'
    ];

    public function getParticipants(int $cycleId): array
    {
        $assignmentModel = new AppraisalCycleTemplateAssignmentModel();
        return $assignmentModel->getResolvedParticipants($cycleId);
    }

    public function getParticipant(int $id): ?array
    {
        return $this
            ->builder()
            ->select([
                'appraisal_cycle_participants.*',
                'users.first_name',
                'users.last_name',
                'users.employee_code',
                'users.email',
                'departments.name AS department_name',
                'designations.title AS designation_name'
            ])
            ->join('users', 'users.id = appraisal_cycle_participants.employee_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->join('designations', 'designations.id = users.designation_id', 'left')
            ->where('appraisal_cycle_participants.id', $id)
            ->get()
            ->getRowArray();
    }

    public function participantExists(int $cycleId, int $employeeId): bool
    {
        return $this->builder()
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->countAllResults() > 0;
    }

    public function getAvailableEmployees(int $cycleId, int $organizationId): array
    {
        return db_connect()
            ->table('users')
            ->select([
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.employee_code',
                'users.email',
                'departments.name AS department_name',
                'designations.title AS designation_name'
            ])
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->join('designations', 'designations.id = users.designation_id', 'left')
            ->where('users.organization_id', $organizationId)
            ->whereNotIn(
                'users.id',
                db_connect()
                    ->table('appraisal_cycle_participants')
                    ->select('employee_id')
                    ->where('appraisal_cycle_id', $cycleId)
            )
            ->orderBy('users.first_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getCycleParticipantCount(int $cycleId): int
    {
        return $this->where('appraisal_cycle_id', $cycleId)->countAllResults();
    }

    public function getActiveParticipants(int $cycleId): array
    {
        return $this
            ->where('appraisal_cycle_id', $cycleId)
            ->where('status', 'active')
            ->findAll();
    }
}
