<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalEmployeeFinalScoreModel extends Model
{
    protected $table = 'appraisal_employee_final_scores';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields = true;

    protected $allowedFields = [
        'appraisal_cycle_id',
        'employee_id',
        'self_score',
        'matrix_score',
        'final_score',
        'performance_grade',
        'final_comment',
        'approved_by',
        'approved_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function saveCalculation(int $cycleId, int $employeeId, ?float $selfScore, ?float $matrixScore, float $finalScore, ?string $performanceGrade = null, ?string $finalComment = null): bool
    {
        $existing = $this
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->first();

        $data = [
            'appraisal_cycle_id' => $cycleId,
            'employee_id' => $employeeId,
            'self_score' => $selfScore,
            'matrix_score' => $matrixScore,
            'final_score' => $finalScore,
            'performance_grade' => $performanceGrade,
            'final_comment' => $finalComment,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            return (bool) $this
                ->where('id', (int) $existing['id'])
                ->set($data)
                ->update();
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        return (bool) $this->insert($data);
    }

    public function getByCycleEmployee(int $cycleId, int $employeeId): ?array
    {
        return $this
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $employeeId)
            ->first();
    }

    public function getEmployeeFinalScore(int $cycleId, int $employeeId): ?array
    {
        return $this->builder()
            ->select([
                'appraisal_employee_final_scores.*',

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

                "CONCAT(
                    COALESCE(approver.first_name, ''),
                    CASE
                        WHEN approver.last_name IS NOT NULL
                        AND approver.last_name != ''
                        THEN CONCAT(' ', approver.last_name)
                        ELSE ''
                    END
                ) AS approved_by_name"
            ])
            ->join(
                'users employee',
                'employee.id = appraisal_employee_final_scores.employee_id',
                'left'
            )
            ->join(
                'users approver',
                'approver.id = appraisal_employee_final_scores.approved_by',
                'left'
            )
            ->where(
                'appraisal_employee_final_scores.appraisal_cycle_id',
                $cycleId
            )
            ->where(
                'appraisal_employee_final_scores.employee_id',
                $employeeId
            )
            ->get()
            ->getRowArray();
    }

    public function getCycleFinalScores(int $cycleId): array
    {
        return $this->builder()
            ->select([
                'appraisal_employee_final_scores.*',

                "CONCAT(
                    COALESCE(users.first_name, ''),
                    CASE
                        WHEN users.last_name IS NOT NULL
                        AND users.last_name != ''
                        THEN CONCAT(' ', users.last_name)
                        ELSE ''
                    END
                ) AS employee_name",

                'users.employee_code',

                'departments.name AS department_name',
                'designations.title AS designation_name'
            ])
            ->join(
                'users',
                'users.id = appraisal_employee_final_scores.employee_id',
                'left'
            )
            ->join(
                'departments',
                'departments.id = users.department_id',
                'left'
            )
            ->join(
                'designations',
                'designations.id = users.designation_id',
                'left'
            )
            ->where(
                'appraisal_employee_final_scores.appraisal_cycle_id',
                $cycleId
            )
            ->orderBy(
                'users.first_name',
                'ASC'
            )
            ->get()
            ->getResultArray();
    }

    public function exists(int $cycleId, int $employeeId): bool
    {
        return $this->builder()
            ->where(
                'appraisal_cycle_id',
                $cycleId
            )
            ->where(
                'employee_id',
                $employeeId
            )
            ->countAllResults() > 0;
    }

    public function getFinalScore(int $id): ?array
    {
        return $this
            ->where('id', $id)
            ->first();
    }

    // public function saveFinalScore(int $cycleId, int $employeeId,): bool
    // {
    //     $existing = $this->builder()
    //         ->select('id')
    //         ->where('appraisal_cycle_id', $cycleId)
    //         ->where('employee_id', $employeeId)
    //         ->get()
    //         ->getRowArray();

    //     if ($existing !== null) {
    //         return $this->update(
    //             (int)$existing['id'],
    //             $data
    //         );
    //     }

    //     $data['appraisal_cycle_id'] = $cycleId;
    //     $data['employee_id'] = $employeeId;

    //     return $this->insert($data) !== false;
    // }
}
