<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationModel extends Model
{
    protected $table = 'organizations';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $protectFields = true;

    protected $allowedFields = [
        'organization_code',
        'name',
        'legal_name',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'logo',
        'timezone',
        'currency',
        'status'
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';


    /**
     * Get paginated organizations.
     */
    public function getOrganizations(
        int $page = 1,
        int $pageSize = 10,
        string $search = '',
        string $status = '',
        string $orderBy = 'id',
        string $direction = 'desc'
    ): array {

        $builder = $this->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('organization_code', $search)
                ->orLike('name', $search)
                ->orLike('legal_name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->orLike('city', $search)
                ->orLike('state', $search)
                ->groupEnd();
        }

        if ($status !== '') {
            $builder->where('status', $status);
        }

        $allowedOrderBy = [
            'id'                => 'id',
            'organization_code' => 'organization_code',
            'name'              => 'name',
            'email'             => 'email',
            'city'              => 'city',
            'status'            => 'status',
            'created_at'        => 'created_at'
        ];

        $orderColumn = $allowedOrderBy[$orderBy] ?? 'id';

        $direction = strtolower($direction) === 'asc'
            ? 'ASC'
            : 'DESC';

        $totalBuilder = clone $builder;

        $total = $totalBuilder->countAllResults();

        $offset = max(
            0,
            ($page - 1) * $pageSize
        );

        $data = $builder
            ->orderBy($orderColumn, $direction)
            ->limit($pageSize, $offset)
            ->get()
            ->getResultArray();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'lastPage' => $total > 0
                ? (int) ceil($total / $pageSize)
                : 1
        ];
    }


    /**
     * Find organization by code.
     */
    public function findByCode(
        string $code,
        ?int $ignoreId = null
    ): ?array {

        $builder = $this
            ->where('organization_code', $code);

        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->first();
    }
}
