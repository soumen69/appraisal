<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingScaleValueModel extends Model
{
    protected $table = 'rating_scale_values';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'rating_scale_id',
        'value',
        'title',
        'description'
    ];
}
