<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingScaleModel extends Model
{
    protected $table = 'rating_scales';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'organization_id',
        'scale_name',
        'min_value',
        'max_value'
    ];
}
