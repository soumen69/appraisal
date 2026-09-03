<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalTemplateModel extends Model
{
    protected $table = 'appraisal_templates';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'organization_id',
        'template_name',
        'description',
        'is_default',
        'status',
    ];

    protected $useTimestamps = true;

    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';


    protected $validationRules = [

        'organization_id' =>
        'required|is_natural_no_zero',

        'template_name' =>
        'required|max_length[150]',

        'is_default' =>
        'permit_empty|in_list[0,1]',

        'status' =>
        'required|in_list[active,inactive]',
    ];


    public function getTemplates(
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
                'appraisal_templates.*',
                'organizations.name AS organization_name',
            ])
            ->join(
                'organizations',
                'organizations.id = appraisal_templates.organization_id',
                'left'
            );


        if ($search !== '') {

            $builder
                ->groupStart()
                ->like(
                    'appraisal_templates.template_name',
                    $search
                )
                ->orLike(
                    'appraisal_templates.description',
                    $search
                )
                ->orLike(
                    'organizations.name',
                    $search
                )
                ->groupEnd();
        }


        if ($status !== '') {

            $builder->where(
                'appraisal_templates.status',
                $status
            );
        }


        $allowedOrderBy = [

            'id' =>
            'appraisal_templates.id',

            'template_name' =>
            'appraisal_templates.template_name',

            'organization_name' =>
            'organizations.name',

            'is_default' =>
            'appraisal_templates.is_default',

            'status' =>
            'appraisal_templates.status',

            'created_at' =>
            'appraisal_templates.created_at',
        ];


        $orderColumn =
            $allowedOrderBy[$orderBy]
            ?? 'appraisal_templates.id';


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

            'data' =>
            $data,

            'total' =>
            $total,

            'page' =>
            $page,

            'pageSize' =>
            $pageSize,

            'lastPage' =>
            $total > 0
                ? (int) ceil(
                    $total / $pageSize
                )
                : 1,
        ];
    }


    public function getTemplate(
        int $id
    ): ?array {

        return $this->builder()

            ->select([
                'appraisal_templates.*',
                'organizations.name AS organization_name',
            ])

            ->join(
                'organizations',
                'organizations.id = appraisal_templates.organization_id',
                'left'
            )

            ->where(
                'appraisal_templates.id',
                $id
            )

            ->get()

            ->getRowArray();
    }


    public function templateNameExists(
        int $organizationId,
        string $templateName,
        ?int $ignoreId = null
    ): bool {

        $builder =
            $this->builder()

            ->where(
                'organization_id',
                $organizationId
            )

            ->where(
                'template_name',
                $templateName
            );


        if ($ignoreId !== null) {

            $builder->where(
                'id !=',
                $ignoreId
            );
        }


        return
            $builder
            ->countAllResults() > 0;
    }


    public function clearDefaultTemplate(
        int $organizationId,
        ?int $ignoreId = null
    ): void {

        $builder =
            $this->builder()

            ->where(
                'organization_id',
                $organizationId
            );


        if ($ignoreId !== null) {

            $builder->where(
                'id !=',
                $ignoreId
            );
        }


        $builder->update([
            'is_default' => 0,
        ]);
    }
}
