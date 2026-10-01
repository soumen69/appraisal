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

    public function getReviews(
        int $page = 1,
        int $pageSize = 10,
        string $search = '',
        string $cycleId = '',
        string $status = '',
        string $reviewType = '',
        string $orderBy = 'id',
        string $direction = 'desc'
    ): array {
        $db = db_connect();

        $page = max(1, $page);
        $pageSize = in_array($pageSize, [10, 25, 50, 100], true) ? $pageSize : 10;

        $allowedStatuses = [
            'pending',
            'in_progress',
            'submitted',
            'approved',
            'rejected',
        ];

        $allowedReviewTypes = ['self', 'matrix'];

        $allowedOrderBy = [
            'id'            => 'latest_appraisal_id',
            'cycle_name'    => 'cycle_name',
            'employee_name' => 'employee_name',
            'reviewer_name' => 'reviewer_names',
            'review_type'   => 'review_types',
            'status'        => 'review_statuses',
            'overall_score' => 'average_score',
            'submitted_at'  => 'latest_submitted_at',
            'created_at'    => 'latest_created_at',
        ];

        $orderColumn = $allowedOrderBy[$orderBy] ?? 'latest_appraisal_id';
        $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';

        /*
     * Build the grouped reviews query.
     * One result row represents one employee in one appraisal cycle.
     */
        $buildQuery = function () use (
            $db,
            $cycleId,
            $status,
            $reviewType,
            $search,
            $allowedStatuses,
            $allowedReviewTypes
        ) {
            $builder = $db->table('appraisals a');

            $builder->select([
                'a.employee_id',
                'a.appraisal_cycle_id AS cycle_id',

                'MAX(a.id) AS latest_appraisal_id',

                'MAX(c.cycle_name) AS cycle_name',
                'MAX(c.cycle_code) AS cycle_code',
                'MAX(c.start_date) AS start_date',
                'MAX(c.end_date) AS end_date',
                'MAX(c.status) AS cycle_status',

                'MAX(t.template_name) AS template_name',

                'MAX(e.employee_code) AS employee_code',
                'MAX(e.full_name) AS employee_name',
                'MAX(e.department_id) AS employee_department_id',
                'MAX(e.designation_id) AS employee_designation_id',
                'MAX(d.name) AS employee_department',
                'MAX(des.title) AS employee_designation',

                /*
             * Include each reviewer and their score.
             * A missing score is displayed as Pending.
             */
                "GROUP_CONCAT(
                DISTINCT CONCAT(
                    COALESCE(r.full_name, 'Unknown reviewer'),
                    ' (',
                    COALESCE(CAST(a.overall_score AS CHAR), 'Pending'),
                    ')'
                )
                ORDER BY r.full_name
                SEPARATOR ', '
            ) AS reviewer_names",

                "GROUP_CONCAT(
                DISTINCT a.review_type
                ORDER BY a.review_type
                SEPARATOR ', '
            ) AS review_types",

                "GROUP_CONCAT(
                DISTINCT a.status
                ORDER BY a.status
                SEPARATOR ', '
            ) AS review_statuses",

                'AVG(a.overall_score) AS average_score',

                'MAX(a.submitted_at) AS latest_submitted_at',
                'MAX(a.approved_at) AS latest_approved_at',
                'MAX(a.created_at) AS latest_created_at',
                'MAX(a.updated_at) AS latest_updated_at',

                'COUNT(a.id) AS reviewer_count',
            ]);

            $builder->join(
                'appraisal_cycles c',
                'c.id = a.appraisal_cycle_id',
                'left'
            );

            $builder->join(
                'appraisal_templates t',
                't.id = a.template_id',
                'left'
            );

            $builder->join(
                'users e',
                'e.id = a.employee_id',
                'left'
            );

            $builder->join(
                'users r',
                'r.id = a.reviewer_id',
                'left'
            );

            $builder->join(
                'departments d',
                'd.id = e.department_id',
                'left'
            );

            $builder->join(
                'designations des',
                'des.id = e.designation_id',
                'left'
            );

            /*
         * If a cycle is selected, show reviews for that cycle.
         * Otherwise, choose each employee's latest cycle that has
         * appraisal records.
         */
            if ($cycleId !== '' && ctype_digit($cycleId) && (int) $cycleId > 0) {
                $builder->where('a.appraisal_cycle_id', (int) $cycleId);
            } else {
                $builder->where(
                    "a.appraisal_cycle_id = (
                    SELECT c2.id
                    FROM appraisal_cycles c2
                    INNER JOIN appraisals a2
                        ON a2.appraisal_cycle_id = c2.id
                    WHERE a2.employee_id = a.employee_id
                    ORDER BY
                        (c2.end_date IS NULL) ASC,
                        c2.end_date DESC,
                        c2.id DESC
                    LIMIT 1
                )",
                    null,
                    false
                );
            }

            if ($status !== '' && in_array($status, $allowedStatuses, true)) {
                $builder->where('a.status', $status);
            }

            if ($reviewType !== '' && in_array($reviewType, $allowedReviewTypes, true)) {
                $builder->where('a.review_type', $reviewType);
            }

            if (trim($search) !== '') {
                $builder->groupStart()
                    ->like('c.cycle_name', $search)
                    ->orLike('c.cycle_code', $search)
                    ->orLike('e.full_name', $search)
                    ->orLike('e.employee_code', $search)
                    ->orLike('r.full_name', $search)
                    ->orLike('t.template_name', $search)
                    ->groupEnd();
            }

            $builder->groupBy([
                'a.employee_id',
                'a.appraisal_cycle_id',
            ]);

            return $builder;
        };

        /*
     * Count the grouped rows using a derived table.
     * Do not add ordering or pagination to this query.
     */
        $countBuilder = $buildQuery();
        $groupedSql = $countBuilder->getCompiledSelect();

        $countSql = "
        SELECT COUNT(*) AS total
        FROM ({$groupedSql}) AS grouped_reviews";

        $total = (int) ($db->query($countSql)->getRowArray()['total'] ?? 0);

        /*
     * Fetch the requested page of grouped results.
     */
        $dataBuilder = $buildQuery();

        $data = $dataBuilder
            ->orderBy($orderColumn, $direction)
            ->limit($pageSize, ($page - 1) * $pageSize)
            ->get()
            ->getResultArray();

        return [
            'data'     => $data,
            'total'    => $total,
            'page'     => $page,
            'pageSize' => $pageSize,
            'lastPage' => $total > 0 ? (int) ceil($total / $pageSize) : 1,
        ];
    }
    public function getCompletedReviewsForEmployeeCycle(
        int $employeeId,
        int $cycleId
    ): array {
        if ($employeeId <= 0 || $cycleId <= 0) {
            return [];
        }

        return $this->builder()
            ->select([
                'appraisals.id',
                'appraisals.employee_id',
                'appraisals.reviewer_id',
                'appraisals.reviewer_role_id',
                'appraisals.review_type',
                'appraisals.overall_score',
                'appraisals.status',
                'appraisals.submitted_at',
                'appraisals.approved_at',
            ])
            ->where(
                'appraisals.employee_id',
                $employeeId
            )
            ->where(
                'appraisals.appraisal_cycle_id',
                $cycleId
            )
            ->whereIn(
                'appraisals.status',
                ['submitted', 'approved']
            )
            ->where(
                'appraisals.overall_score IS NOT NULL',
                null,
                false
            )
            ->orderBy(
                'appraisals.id',
                'ASC'
            )
            ->get()
            ->getResultArray();
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
