<?php

namespace App\Models\Admin;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $protectFields = true;

    protected $allowedFields = [
        'module_id',
        'parent_id',
        'title',
        'icon',
        'route',
        'permission_id',
        'is_system',
        'sort_order',
        'is_sidebar',
        'is_visible',
        'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
