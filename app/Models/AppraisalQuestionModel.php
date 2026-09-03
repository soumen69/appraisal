<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalQuestionModel extends Model
{
    protected $table =
    'appraisal_questions';

    protected $primaryKey =
    'id';

    protected $returnType =
    'array';

    protected $useAutoIncrement =
    true;

    protected $protectFields =
    true;

    protected $allowedFields = [

        'section_id',

        'question',

        'answer_type',

        'is_required',

        'sort_order',
    ];

    protected $useTimestamps =
    true;

    protected $dateFormat =
    'datetime';

    protected $createdField =
    'created_at';

    protected $updatedField =
    'updated_at';


    protected $validationRules = [

        'section_id' =>
        'required|is_natural_no_zero',

        'question' =>
        'required',

        'answer_type' =>
        'required|in_list[rating,text,number,yes_no]',

        'is_required' =>
        'permit_empty|in_list[0,1]',

        'sort_order' =>
        'permit_empty|is_natural_no_zero',
    ];


    public function getNextSortOrder(
        int $sectionId
    ): int {

        $result =
            $this->builder()
            ->selectMax(
                'sort_order',
                'max_sort_order'
            )
            ->where(
                'section_id',
                $sectionId
            )
            ->get()
            ->getRowArray();

        return ((int) (
            $result['max_sort_order']
            ?? 0
        )) + 1;
    }


    public function getQuestion(
        int $questionId
    ): ?array {

        return
            $this->builder()
            ->where(
                'id',
                $questionId
            )
            ->get()
            ->getRowArray();
    }


    public function getQuestionsBySection(
        int $sectionId
    ): array {

        return
            $this->builder()
            ->where(
                'section_id',
                $sectionId
            )
            ->orderBy(
                'sort_order',
                'ASC'
            )
            ->orderBy(
                'id',
                'ASC'
            )
            ->get()
            ->getResultArray();
    }
}
