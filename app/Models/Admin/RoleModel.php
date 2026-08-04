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

        'description',

        'is_system'

    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';
}