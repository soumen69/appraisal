<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalCycleModel extends Model
{
    protected $table = 'appraisal_cycles';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'organization_id',
        'cycle_name',
        'cycle_code',
        'description',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $useTimestamps = true;

    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'organization_id' => 'required|is_natural_no_zero',
        'cycle_name'      => 'required|max_length[150]',
        'cycle_code'      => 'permit_empty|max_length[50]',
        'start_date'      => 'required|valid_date[Y-m-d]',
        'end_date'        => 'required|valid_date[Y-m-d]',
        'status'          => 'required|in_list[draft,active,completed,closed]',
    ];

    protected $validationMessages = [
        'organization_id' => [
            'required' => 'Organization is required.',
        ],
        'cycle_name' => [
            'required' => 'Cycle name is required.',
        ],
        'start_date' => [
            'required' => 'Start date is required.',
        ],
        'end_date' => [
            'required' => 'End date is required.',
        ],
    ];

    public function getCycles(
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
                'appraisal_cycles.*',
                'organizations.name AS organization_name',
                'users.full_name AS created_by_name',
            ])
            ->join(
                'organizations',
                'organizations.id = appraisal_cycles.organization_id',
                'left'
            )
            ->join(
                'users',
                'users.id = appraisal_cycles.created_by',
                'left'
            );

        if ($search !== '') {
            $builder
                ->groupStart()
                ->like('appraisal_cycles.cycle_name', $search)
                ->orLike('appraisal_cycles.cycle_code', $search)
                ->orLike('appraisal_cycles.description', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where(
                'appraisal_cycles.status',
                $status
            );
        }

        $allowedOrderBy = [
            'id'         => 'appraisal_cycles.id',
            'cycle_name' => 'appraisal_cycles.cycle_name',
            'cycle_code' => 'appraisal_cycles.cycle_code',
            'start_date' => 'appraisal_cycles.start_date',
            'end_date'   => 'appraisal_cycles.end_date',
            'status'     => 'appraisal_cycles.status',
            'created_at' => 'appraisal_cycles.created_at',
        ];

        $orderColumn =
            $allowedOrderBy[$orderBy]
            ?? 'appraisal_cycles.id';

        $direction =
            strtolower($direction) === 'asc'
            ? 'ASC'
            : 'DESC';

        $builder->orderBy(
            $orderColumn,
            $direction
        );

        $totalBuilder = clone $builder;

        $total =
            $totalBuilder->countAllResults();

        $offset =
            max(
                0,
                ($page - 1) * $pageSize
            );

        $data =
            $builder
            ->limit(
                $pageSize,
                $offset
            )
            ->get()
            ->getResultArray();

        return [
            'data'     => $data,
            'total'    => $total,
            'page'     => $page,
            'pageSize' => $pageSize,
            'lastPage' => $total > 0
                ? (int) ceil($total / $pageSize)
                : 1,
        ];
    }

    public function getCycle(
        int $id
    ): ?array {
        return $this->builder()
            ->select([
                'appraisal_cycles.*',
                'organizations.name AS organization_name',
                'users.full_name AS created_by_name',
            ])
            ->join(
                'organizations',
                'organizations.id = appraisal_cycles.organization_id',
                'left'
            )
            ->join(
                'users',
                'users.id = appraisal_cycles.created_by',
                'left'
            )
            ->where(
                'appraisal_cycles.id',
                $id
            )
            ->get()
            ->getRowArray();
    }

    public function cycleCodeExists(
        string $cycleCode,
        ?int $ignoreId = null
    ): bool {
        $builder =
            $this->where(
                'cycle_code',
                $cycleCode
            );

        if ($ignoreId !== null) {
            $builder->where(
                'id !=',
                $ignoreId
            );
        }

        return $builder->countAllResults() > 0;
    }
}
