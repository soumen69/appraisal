<?php

namespace App\Models;

use CodeIgniter\Model;

class DesignationModel extends Model
{
    protected $table = 'designations';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'organization_id',
        'designation_code',
        'title',
        'level',
        'description',
        'status',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';


    public function getDesignations(
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
                'designations.id',
                'designations.designation_code',
                'designations.title',
                'designations.level',
                'designations.description',

                "
            CASE
                WHEN COUNT(DISTINCT designations.status) = 1
                    THEN MAX(designations.status)
                ELSE 'mixed'
            END AS status
            ",

                "
            GROUP_CONCAT(
                DISTINCT organizations.name
                ORDER BY organizations.name ASC
                SEPARATOR ', '
            ) AS organization_names
            ",

                "
            GROUP_CONCAT(
                DISTINCT designations.organization_id
                ORDER BY designations.organization_id ASC
                SEPARATOR ','
            ) AS organization_ids
            ",

                'MIN(designations.id) AS group_id',

                'MIN(designations.created_at) AS created_at',
            ])
            ->join(
                'organizations',
                'organizations.id = designations.organization_id',
                'left'
            );

        if ($search !== '') {

            $builder->groupStart()

                ->like(
                    'designations.designation_code',
                    $search
                )

                ->orLike(
                    'designations.title',
                    $search
                )

                ->orLike(
                    'designations.description',
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
                'designations.status',
                $status
            );
        }

        $builder->groupBy([
            'designations.designation_code',
            'designations.title',
            'designations.level',
        ]);

        $allowedOrderBy = [

            'id' =>
            'group_id',

            'designation_code' =>
            'designations.designation_code',

            'title' =>
            'designations.title',

            'level' =>
            'designations.level',

            'organization_name' =>
            'organization_names',

            'status' =>
            'status',

            'created_at' =>
            'created_at',
        ];

        $orderColumn =
            $allowedOrderBy[$orderBy]
            ?? 'group_id';

        $direction =
            strtolower($direction) === 'asc'
            ? 'ASC'
            : 'DESC';

        $countBuilder = clone $builder;

        $total = (int) $countBuilder->countAllResults();

        $offset =
            max(
                0,
                ($page - 1) * $pageSize
            );

        $data = $builder
            ->orderBy(
                $orderColumn,
                $direction
            )
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


    public function findByCode(
        string $code,
        int $organizationId,
        ?int $ignoreId = null
    ): ?array {
        $builder = $this
            ->where(
                'designation_code',
                $code
            )
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

        return $builder->first();
    }


    public function getDesignationGroup(
        string $code
    ): ?array {

        $rows = $this->builder()
            ->select([
                'designations.*',
                'organizations.name AS organization_name',
            ])
            ->join(
                'organizations',
                'organizations.id = designations.organization_id',
                'left'
            )
            ->where(
                'designations.designation_code',
                $code
            )
            ->orderBy(
                'organizations.name',
                'ASC'
            )
            ->get()
            ->getResultArray();

        if (!$rows) {
            return null;
        }

        $first = $rows[0];

        return [
            'id' =>
            $first['id'],

            'designation_code' =>
            $first['designation_code'],

            'title' =>
            $first['title'],

            'level' =>
            $first['level'],

            'description' =>
            $first['description'],

            'status' =>
            count(
                array_unique(
                    array_column(
                        $rows,
                        'status'
                    )
                )
            ) === 1
                ? $first['status']
                : 'mixed',

            'organizations' =>
            array_map(
                static function ($row) {
                    return [
                        'id' =>
                        $row['organization_id'],

                        'name' =>
                        $row['organization_name'],

                        'status' =>
                        $row['status'],
                    ];
                },
                $rows
            ),
        ];
    }

    public function getDesignationGroupById(int $id): ?array
    {
        /*
     * Resolve the representative designation.
     */
        $designation = $this->find($id);

        if (!$designation) {
            return null;
        }

        /*
     * A designation group is identified by:
     *
     * designation_code
     * title
     * level
     *
     * across organizations.
     */
        $builder = $this->builder()
            ->select([
                'designations.id',
                'designations.organization_id',
                'designations.designation_code',
                'designations.title',
                'designations.level',
                'designations.description',
                'designations.status',
                'organizations.name AS organization_name',
            ])
            ->join(
                'organizations',
                'organizations.id = designations.organization_id',
                'left'
            )
            ->where(
                'designations.designation_code',
                $designation['designation_code']
            )
            ->where(
                'designations.title',
                $designation['title']
            )
            ->where(
                'designations.level',
                $designation['level']
            )
            ->orderBy(
                'organizations.name',
                'ASC'
            );

        $rows = $builder
            ->get()
            ->getResultArray();

        if (!$rows) {
            return null;
        }

        /*
     * Determine group status.
     */
        $statuses = array_values(
            array_unique(
                array_column(
                    $rows,
                    'status'
                )
            )
        );

        /*
     * Organization IDs.
     */
        $organizationIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    array_column(
                        $rows,
                        'organization_id'
                    )
                )
            )
        );

        /*
     * Organization names.
     */
        $organizationNames = array_values(
            array_filter(
                array_column(
                    $rows,
                    'organization_name'
                )
            )
        );

        return [
            'id' =>
            (int) $rows[0]['id'],

            'group_id' =>
            (int) $rows[0]['id'],

            'designation_code' =>
            $rows[0]['designation_code'],

            'title' =>
            $rows[0]['title'],

            'level' =>
            (int) $rows[0]['level'],

            'description' =>
            $rows[0]['description'],

            'status' =>
            count($statuses) === 1
                ? $statuses[0]
                : 'mixed',

            'organization_names' =>
            implode(
                ', ',
                $organizationNames
            ),

            'organization_ids' =>
            $organizationIds,
        ];
    }
}
