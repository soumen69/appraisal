<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'organization_id',
        'department_code',
        'name',
        'description',
        'status',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';


    /**
     * Get departments grouped by department code + name.
     *
     * One department can belong to multiple organizations.
     * Example:
     *
     * QA | Quality Assurance
     *      ├── Delostyle
     *      └── CuddlyDuddly
     *
     * appears only once in the listing.
     */
    public function getDepartments(
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
                'departments.department_code',
                'departments.name',
                'MAX(departments.description) AS description',

                "CASE
                    WHEN COUNT(DISTINCT departments.status) = 1
                    THEN MAX(departments.status)
                    ELSE 'mixed'
                END AS status",

                "GROUP_CONCAT(
                    DISTINCT organizations.name
                    ORDER BY organizations.name ASC
                    SEPARATOR ', '
                ) AS organization_names",

                "GROUP_CONCAT(
                    DISTINCT departments.organization_id
                    ORDER BY departments.organization_id ASC
                    SEPARATOR ','
                ) AS organization_ids",

                'MIN(departments.id) AS group_id',

                'MIN(departments.created_at) AS created_at',
            ])
            ->join(
                'organizations',
                'organizations.id = departments.organization_id',
                'left'
            )
            ->groupBy([
                'departments.department_code',
                'departments.name',
            ]);


        /*
         * Search
         */
        if ($search !== '') {

            $builder->groupStart()

                ->like(
                    'departments.department_code',
                    $search
                )

                ->orLike(
                    'departments.name',
                    $search
                )

                ->orLike(
                    'departments.description',
                    $search
                )

                ->orLike(
                    'organizations.name',
                    $search
                )

                ->groupEnd();
        }


        /*
         * Status filter.
         *
         * If a grouped department has at least one matching
         * organization/status row, it remains in the result.
         */
        if ($status !== '') {

            $builder->where(
                'departments.status',
                $status
            );
        }


        /*
         * Whitelist sortable columns.
         */
        $allowedOrderBy = [

            'id' =>
            'group_id',

            'department_code' =>
            'departments.department_code',

            'name' =>
            'departments.name',

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


        /*
         * Count grouped records.
         */
        $countBuilder = clone $builder;

        $total =
            $countBuilder->countAllResults();


        /*
         * Pagination.
         */
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

            'data' => $data,

            'total' => $total,

            'page' => $page,

            'pageSize' => $pageSize,

            'lastPage' => $total > 0
                ? (int) ceil(
                    $total / $pageSize
                )
                : 1,
        ];
    }


    /**
     * Get a complete department group.
     *
     * Used by edit/view.
     */
    public function getDepartmentGroupById(
        int $id
    ): ?array {

        $department = $this->find($id);

        if (!$department) {
            return null;
        }


        /*
         * Group is identified by department_code + name.
         *
         * This allows the same department to exist
         * in multiple organizations.
         */
        $builder = $this->builder();

        $builder
            ->select([
                'departments.department_code',
                'departments.name',
                'MAX(departments.description) AS description',

                "CASE
                    WHEN COUNT(DISTINCT departments.status) = 1
                    THEN MAX(departments.status)
                    ELSE 'mixed'
                END AS status",

                "GROUP_CONCAT(
                    DISTINCT organizations.name
                    ORDER BY organizations.name ASC
                    SEPARATOR ', '
                ) AS organization_names",

                "GROUP_CONCAT(
                    DISTINCT departments.organization_id
                    ORDER BY departments.organization_id ASC
                    SEPARATOR ','
                ) AS organization_ids",

                'MIN(departments.id) AS group_id',
            ])
            ->join(
                'organizations',
                'organizations.id = departments.organization_id',
                'left'
            )
            ->where(
                'departments.department_code',
                $department['department_code']
            )
            ->where(
                'departments.name',
                $department['name']
            )
            ->groupBy([
                'departments.department_code',
                'departments.name',
            ]);

        return $builder
            ->get()
            ->getRowArray();
    }


    /**
     * Get all database rows belonging to a department group.
     */
    public function getGroupRows(
        ?string $code,
        string $name
    ): array {

        $builder = $this
            ->where('name', $name);

        if ($code === null || $code === '') {

            $builder->where(
                'department_code IS NULL',
                null,
                false
            );
        } else {

            $builder->where(
                'department_code',
                $code
            );
        }

        return $builder
            ->get()
            ->getResultArray();
    }


    /**
     * Find duplicate department within organization.
     */
    public function findByCode(
        string $code,
        int $organizationId,
        ?int $ignoreId = null
    ): ?array {

        $builder = $this
            ->where(
                'department_code',
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
}
