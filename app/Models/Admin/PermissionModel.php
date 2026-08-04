<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $protectFields = true;

    protected $allowedFields = [

        'name',

        'slug',

        'module'

    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';
}