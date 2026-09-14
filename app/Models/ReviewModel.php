<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table = 'appraisals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields = true;

    protected $allowedFields = [
        'appraisal_cycle_id',
        'employee_id',
        'reviewer_id',
        'reviewer_role_id',
        'review_type',
        'template_id',
        'status',
        'overall_score',
        'overall_comment',
        'submitted_at',
        'approved_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getReviews(int $page = 1, int $pageSize = 10, string $search = '', string $cycleId = '', string $status = '', string $reviewType = '', string $orderBy = 'id', string $direction = 'desc'): array
    {
        $builder = $this->builder();

        $builder->select([
            'appraisals.id AS appraisal_id',
            'appraisals.appraisal_cycle_id AS cycle_id',
            'appraisals.employee_id',
            'appraisals.reviewer_id',
            'appraisals.reviewer_role_id',
            'appraisals.review_type',
            'appraisals.template_id',
            'appraisals.status',
            'appraisals.overall_score',
            'appraisals.overall_comment',
            'appraisals.submitted_at',
            'appraisals.approved_at',
            'appraisals.created_at',
            'appraisals.updated_at',

            'appraisal_cycles.cycle_name',
            'appraisal_cycles.cycle_code',
            'appraisal_cycles.start_date',
            'appraisal_cycles.end_date',
            'appraisal_cycles.status AS cycle_status',

            'templates.template_name',

            'employees.employee_code',
            'employees.full_name AS employee_name',
            'employees.department_id AS employee_department_id',
            'employees.designation_id AS employee_designation_id',

            'departments.name AS employee_department',
            'designations.title AS employee_designation',

            'reviewers.full_name AS reviewer_name',
        ]);

        $builder->join(
            'appraisal_cycles',
            'appraisal_cycles.id = appraisals.appraisal_cycle_id',
            'left'
        );

        $builder->join(
            'appraisal_templates AS templates',
            'templates.id = appraisals.template_id',
            'left'
        );

        $builder->join(
            'users AS employees',
            'employees.id = appraisals.employee_id',
            'left'
        );

        $builder->join(
            'users AS reviewers',
            'reviewers.id = appraisals.reviewer_id',
            'left'
        );

        $builder->join(
            'departments',
            'departments.id = employees.department_id',
            'left'
        );

        $builder->join(
            'designations',
            'designations.id = employees.designation_id',
            'left'
        );

        if ($search !== '') {
            $builder
                ->groupStart()
                ->like('appraisal_cycles.cycle_name', $search)
                ->orLike('appraisal_cycles.cycle_code', $search)
                ->orLike('employees.full_name', $search)
                ->orLike('employees.employee_code', $search)
                ->orLike('reviewers.full_name', $search)
                ->orLike('templates.template_name', $search)
                ->groupEnd();
        }

        if ($cycleId !== '' && ctype_digit($cycleId) && (int) $cycleId > 0) {
            $builder->where(
                'appraisals.appraisal_cycle_id',
                (int) $cycleId
            );
        }

        if ($status !== '') {
            $allowedStatuses = [
                'pending',
                'in_progress',
                'submitted',
                'approved',
                'rejected',
            ];

            if (in_array($status, $allowedStatuses, true)) {
                $builder->where(
                    'appraisals.status',
                    $status
                );
            }
        }

        if ($reviewType !== '') {
            $allowedReviewTypes = [
                'self',
                'matrix',
            ];

            if (in_array($reviewType, $allowedReviewTypes, true)) {
                $builder->where(
                    'appraisals.review_type',
                    $reviewType
                );
            }
        }

        $allowedOrderBy = [
            'id' => 'appraisals.id',
            'cycle_name' => 'appraisal_cycles.cycle_name',
            'employee_name' => 'employees.full_name',
            'reviewer_name' => 'reviewers.full_name',
            'review_type' => 'appraisals.review_type',
            'status' => 'appraisals.status',
            'overall_score' => 'appraisals.overall_score',
            'submitted_at' => 'appraisals.submitted_at',
            'created_at' => 'appraisals.created_at',
        ];

        $orderColumn = $allowedOrderBy[$orderBy] ?? 'appraisals.id';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';

        $builder->orderBy($orderColumn, $direction);

        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults();

        $offset = max(0, ($page - 1) * $pageSize);

        $data = $builder
            ->limit($pageSize, $offset)
            ->get()
            ->getResultArray();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'lastPage' => $total > 0 ? (int) ceil($total / $pageSize) : 1,
        ];
    }

    public function getReview(int $id): ?array
    {
        return $this->builder()
            ->select([
                'appraisals.*',

                'appraisal_cycles.cycle_name',
                'appraisal_cycles.cycle_code',
                'appraisal_cycles.description AS cycle_description',
                'appraisal_cycles.start_date',
                'appraisal_cycles.end_date',
                'appraisal_cycles.status AS cycle_status',

                'templates.template_name',
                'templates.description AS template_description',

                'employees.full_name AS employee_name',
                'employees.employee_code',
                'employees.email AS employee_email',
                'employees.department_id AS employee_department_id',
                'employees.designation_id AS employee_designation_id',

                'departments.name AS employee_department',
                'designations.title AS employee_designation',

                'reviewers.full_name AS reviewer_name',
                'reviewers.email AS reviewer_email',

                'roles.name AS reviewer_role_name',
            ])
            ->join(
                'appraisal_cycles',
                'appraisal_cycles.id = appraisals.appraisal_cycle_id',
                'left'
            )
            ->join(
                'appraisal_templates AS templates',
                'templates.id = appraisals.template_id',
                'left'
            )
            ->join(
                'users AS employees',
                'employees.id = appraisals.employee_id',
                'left'
            )
            ->join(
                'users AS reviewers',
                'reviewers.id = appraisals.reviewer_id',
                'left'
            )
            ->join(
                'departments',
                'departments.id = employees.department_id',
                'left'
            )
            ->join(
                'designations',
                'designations.id = employees.designation_id',
                'left'
            )
            ->join(
                'roles',
                'roles.id = appraisals.reviewer_role_id',
                'left'
            )
            ->where(
                'appraisals.id',
                $id
            )
            ->get()
            ->getRowArray();
    }

    public function getReviewSections(int $templateId, int $appraisalId): array
    {
        $sections = db_connect()
            ->table('appraisal_template_sections')
            ->select([
                'appraisal_template_sections.id',
                'appraisal_template_sections.section_name',
                'appraisal_template_sections.sort_order',
            ])
            ->where(
                'appraisal_template_sections.template_id',
                $templateId
            )
            ->orderBy(
                'appraisal_template_sections.sort_order',
                'ASC'
            )
            ->get()
            ->getResultArray();

        if (!$sections) {
            return [];
        }

        $sectionIds = array_map(
            static fn($section) => (int) $section['id'],
            $sections
        );

        $questions = db_connect()
            ->table('appraisal_questions AS questions')
            ->select([
                'questions.id',
                'questions.section_id',
                'questions.question',
                'questions.answer_type',
                'questions.is_required',
                'questions.weight',
                'questions.sort_order',
            ])
            ->whereIn(
                'questions.section_id',
                $sectionIds
            )
            ->orderBy(
                'questions.sort_order',
                'ASC'
            )
            ->get()
            ->getResultArray();

        $answers = db_connect()
            ->table('appraisal_answers')
            ->select([
                'question_id',
                'rating',
                'answer_text',
                'answer_number',
                'answer_yes_no',
                'comment',
            ])
            ->where(
                'appraisal_id',
                $appraisalId
            )
            ->get()
            ->getResultArray();

        $answerMap = [];

        foreach ($answers as $answer) {
            $answerMap[(int) $answer['question_id']] = $answer;
        }

        $questionMap = [];

        foreach ($questions as $question) {
            $questionId = (int) $question['id'];

            $question['id'] = $questionId;
            $question['section_id'] = (int) $question['section_id'];
            $question['is_required'] = (int) $question['is_required'];
            $question['answer'] = $answerMap[$questionId] ?? null;

            $questionMap[$question['section_id']][] = $question;
        }

        foreach ($sections as &$section) {
            $sectionId = (int) $section['id'];
            $section['id'] = $sectionId;
            $section['questions'] = $questionMap[$sectionId] ?? [];
        }

        unset($section);

        return $sections;
    }

    public function getCyclesForFilter(): array
    {
        return db_connect()
            ->table('appraisal_cycles')
            ->select([
                'id',
                'cycle_name',
                'cycle_code',
            ])
            ->orderBy(
                'start_date',
                'DESC'
            )
            ->orderBy(
                'id',
                'DESC'
            )
            ->get()
            ->getResultArray();
    }
}
