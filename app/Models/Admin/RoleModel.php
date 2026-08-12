<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $protectFields = true;

    protected $allowedFields = [
        'name',
        'slug',
        'display_name',
        'icon',
        'color',
        'sort_order',
        'status',
        'created_by',
        'description',
        'is_system'
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';
}