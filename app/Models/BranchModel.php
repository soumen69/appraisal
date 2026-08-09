<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchModel extends Model
{
    protected $table = 'branches';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'organization_id',
        'branch_code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'status',
    ];

    protected $useTimestamps = true;

    protected $dateFormat = 'datetime';

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected $validationRules = [
        'organization_id' => 'required|integer',
        'name'            => 'required|max_length[150]',
        'branch_code'     => 'permit_empty|max_length[30]',
        'email'           => 'permit_empty|valid_email|max_length[150]',
        'phone'           => 'permit_empty|max_length[30]',
        'address'         => 'permit_empty',
        'city'            => 'permit_empty|max_length[100]',
        'state'           => 'permit_empty|max_length[100]',
        'country'         => 'permit_empty|max_length[100]',
        'postal_code'     => 'permit_empty|max_length[20]',
        'status'          => 'required|in_list[active,inactive]',
    ];

    protected $validationMessages = [
        'organization_id' => [
            'required' => 'Organization is required.',
            'integer'  => 'Invalid organization.',
        ],

        'name' => [
            'required'   => 'Branch name is required.',
            'max_length' => 'Branch name cannot exceed 150 characters.',
        ],

        'email' => [
            'valid_email' => 'Please enter a valid email address.',
        ],

        'status' => [
            'required' => 'Status is required.',
            'in_list'  => 'Invalid branch status.',
        ],
    ];

    protected $skipValidation = false;

    protected $cleanValidationRules = true;


    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function getBranches(
        int $page = 1,
        int $pageSize = 10,
        string $search = '',
        string $status = '',
        string $organizationId = '',
        string $orderBy = 'id',
        string $direction = 'desc'
    ): array {

        $builder = $this->builder();

        $builder
            ->select([
                'branches.id',
                'branches.organization_id',
                'branches.branch_code',
                'branches.name',
                'branches.email',
                'branches.phone',
                'branches.address',
                'branches.city',
                'branches.state',
                'branches.country',
                'branches.postal_code',
                'branches.status',
                'branches.created_at',

                'organizations.name AS organization_name',
            ])
            ->join(
                'organizations',
                'organizations.id = branches.organization_id',
                'left'
            );


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {

            $builder->groupStart()
                ->like('branches.branch_code', $search)
                ->orLike('branches.name', $search)
                ->orLike('branches.email', $search)
                ->orLike('branches.phone', $search)
                ->orLike('branches.city', $search)
                ->orLike('branches.state', $search)
                ->orLike('organizations.name', $search)
                ->groupEnd();
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($status !== '') {
            $builder->where(
                'branches.status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Organization
        |--------------------------------------------------------------------------
        */

        if ($organizationId !== '') {
            $builder->where(
                'branches.organization_id',
                (int) $organizationId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Safe Sorting
        |--------------------------------------------------------------------------
        */

        $allowedOrderBy = [
            'id'              => 'branches.id',
            'branch_code'     => 'branches.branch_code',
            'name'            => 'branches.name',
            'organization'    => 'organizations.name',
            'city'            => 'branches.city',
            'status'          => 'branches.status',
            'created_at'      => 'branches.created_at',
        ];

        $orderColumn =
            $allowedOrderBy[$orderBy]
            ?? 'branches.id';

        $direction =
            strtolower($direction) === 'asc'
            ? 'ASC'
            : 'DESC';


        $builder->orderBy(
            $orderColumn,
            $direction
        );


        /*
        |--------------------------------------------------------------------------
        | Count
        |--------------------------------------------------------------------------
        */

        $totalBuilder = clone $builder;

        $total =
            $totalBuilder->countAllResults();


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $page =
            max(1, $page);

        $pageSize =
            min(
                max(1, $pageSize),
                100
            );

        $offset =
            ($page - 1) * $pageSize;


        $data = $builder
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


    /*
    |--------------------------------------------------------------------------
    | Single Branch
    |--------------------------------------------------------------------------
    */

    public function getBranch(
        int $id
    ): ?array {

        return $this->builder()
            ->select([
                'branches.*',

                'organizations.name AS organization_name',
            ])
            ->join(
                'organizations',
                'organizations.id = branches.organization_id',
                'left'
            )
            ->where(
                'branches.id',
                $id
            )
            ->get()
            ->getRowArray();
    }


    /*
    |--------------------------------------------------------------------------
    | Organization Options
    |--------------------------------------------------------------------------
    |
    | Kept here so the module remains:
    |
    | UI → Controller → Model
    |
    */

    public function getOrganizationOptions(): array
    {
        return db_connect()
            ->table('organizations')
            ->select([
                'id',
                'name',
            ])
            ->orderBy(
                'name',
                'ASC'
            )
            ->get()
            ->getResultArray();
    }


    /*
    |--------------------------------------------------------------------------
    | Branch Options
    |--------------------------------------------------------------------------
    |
    | Useful for Employee → Branch dropdown.
    |
    */

    public function getOptions(
        ?int $organizationId = null
    ): array {

        $builder = $this->builder()
            ->select([
                'id',
                'organization_id',
                'branch_code',
                'name',
            ])
            ->where(
                'status',
                'active'
            );

        if ($organizationId !== null) {

            $builder->where(
                'organization_id',
                $organizationId
            );
        }

        return $builder
            ->orderBy(
                'name',
                'ASC'
            )
            ->get()
            ->getResultArray();
    }


    /*
    |--------------------------------------------------------------------------
    | Duplicate Protection
    |--------------------------------------------------------------------------
    */

    public function branchCodeExists(
        string $branchCode,
        int $organizationId,
        ?int $ignoreId = null
    ): bool {

        if (trim($branchCode) === '') {
            return false;
        }

        $builder = $this->builder()
            ->where(
                'organization_id',
                $organizationId
            )
            ->where(
                'branch_code',
                trim($branchCode)
            );

        if ($ignoreId !== null) {

            $builder->where(
                'id !=',
                $ignoreId
            );
        }

        return $builder
            ->countAllResults() > 0;
    }
}
