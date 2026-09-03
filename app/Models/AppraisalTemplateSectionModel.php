<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalTemplateSectionModel extends Model
{
    protected $table =
    'appraisal_template_sections';

    protected $primaryKey =
    'id';

    protected $returnType =
    'array';

    protected $useAutoIncrement =
    true;

    protected $protectFields =
    true;

    protected $allowedFields = [

        'template_id',

        'section_name',

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

        'template_id' =>
        'required|is_natural_no_zero',

        'section_name' =>
        'required|max_length[150]',

        'sort_order' =>
        'permit_empty|is_natural_no_zero',
    ];


    public function getNextSortOrder(
        int $templateId
    ): int {

        $result =
            $this->builder()
            ->selectMax(
                'sort_order',
                'max_sort_order'
            )
            ->where(
                'template_id',
                $templateId
            )
            ->get()
            ->getRowArray();

        return ((int) (
                $result['max_sort_order']
                ?? 0
            )) + 1;
    }


    public function getSection(
        int $sectionId
    ): ?array {

        return
            $this->builder()
            ->where(
                'id',
                $sectionId
            )
            ->get()
            ->getRowArray();
    }


    public function getSectionsByTemplate(
        int $templateId
    ): array {

        return
            $this->builder()
            ->select([

                'appraisal_template_sections.*',

                'COUNT(appraisal_questions.id)
                AS question_count',
            ])
            ->join(
                'appraisal_questions',

                'appraisal_questions.section_id
                = appraisal_template_sections.id',

                'left'
            )
            ->where(
                'appraisal_template_sections.template_id',
                $templateId
            )
            ->groupBy(
                'appraisal_template_sections.id'
            )
            ->orderBy(
                'appraisal_template_sections.sort_order',
                'ASC'
            )
            ->orderBy(
                'appraisal_template_sections.id',
                'ASC'
            )
            ->get()
            ->getResultArray();
    }
}
