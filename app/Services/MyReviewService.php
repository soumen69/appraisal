<?php

namespace App\Services;

use App\Models\AppraisalCycleTemplateAssignmentModel;

class MyReviewService
{
    protected AppraisalCycleTemplateAssignmentModel $assignments;
    protected $db;

    public function __construct()
    {
        $this->assignments = new AppraisalCycleTemplateAssignmentModel();
        $this->db = db_connect();
    }

    public function getMyReviews(int $employeeId): array
    {
        if ($employeeId <= 0) return [];

        $reviews = $this->assignments->getEmployeeMyReviews($employeeId);

        foreach ($reviews as &$review) {
            $review['template_id'] = $review['resolved_template_id'] ?? null;
            $review['template_name'] = $review['resolved_template_name'] ?? null;

            $appraisal = $this->db->table('appraisals')
                ->select('id, status')
                ->where('appraisal_cycle_id', $review['cycle_id'])
                ->where('employee_id', $employeeId)
                ->where('reviewer_id', $employeeId)
                ->get()
                ->getRowArray();

            $review['appraisal_id'] = $appraisal['id'] ?? null;
            $review['status'] = $appraisal['status'] ?? 'pending';
        }

        unset($review);

        return $reviews;
    }

    public function startReview(int $cycleId, int $userId): array
    {
        if ($cycleId <= 0 || $userId <= 0) {
            return ['success' => false, 'message' => 'Invalid review request.'];
        }

        $employee = $this->db->table('users')
            ->select('id, organization_id, department_id, designation_id')
            ->where('id', $userId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$employee) {
            return ['success' => false, 'message' => 'Employee account was not found or is inactive.'];
        }

        $role = $this->db->table('user_roles')
            ->select('role_id')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$role) {
            return ['success' => false, 'message' => 'No role is assigned to your account.'];
        }

        $cycle = $this->db->table('appraisal_cycles')
            ->select('id')
            ->where('id', $cycleId)
            ->where('organization_id', $employee['organization_id'])
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$cycle) {
            return ['success' => false, 'message' => 'This appraisal cycle is not currently active.'];
        }

        $participant = $this->db->table('appraisal_cycle_participants')
            ->select('id')
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $userId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$participant) {
            return ['success' => false, 'message' => 'You are not an active participant in this appraisal cycle.'];
        }

        $selfReviewAllowed = $this->db->table('review_matrix')
            ->select('id')
            ->where('organization_id', $employee['organization_id'])
            ->where('reviewer_role_id', $role['role_id'])
            ->where('reviewee_role_id', $role['role_id'])
            ->where('allow_self_review', 1)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$selfReviewAllowed) {
            return ['success' => false, 'message' => 'Self review is not enabled for your role.'];
        }

        $template = $this->assignments->resolveTemplate($cycleId, $userId, 'self');

        if (!$template) {
            return ['success' => false, 'message' => 'No appraisal template is assigned to you for this cycle.'];
        }

        $existingAppraisal = $this->db->table('appraisals')
            ->where('appraisal_cycle_id', $cycleId)
            ->where('employee_id', $userId)
            ->where('reviewer_id', $userId)
            ->where('reviewer_role_id', $role['role_id'])
            ->get()
            ->getRowArray();

        if ($existingAppraisal) {
            $existingTemplateId = (int) $existingAppraisal['template_id'];
            $resolvedTemplateId = (int) $template['template_id'];
            $lockedStatuses = ['submitted', 'approved'];

            if (
                $existingTemplateId !== $resolvedTemplateId
                && !in_array($existingAppraisal['status'], $lockedStatuses, true)
            ) {
                $this->db->transStart();

                $this->db->table('appraisal_answers')
                    ->where('appraisal_id', $existingAppraisal['id'])
                    ->delete();

                $this->db->table('appraisals')
                    ->where('id', $existingAppraisal['id'])
                    ->update([
                        'template_id' => $resolvedTemplateId,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                $this->db->transComplete();

                if (!$this->db->transStatus()) {
                    return ['success' => false, 'message' => 'Unable to update the appraisal template. Please try again.'];
                }
            }

            return [
                'success' => true,
                'message' => 'Review loaded successfully.',
                'data' => [
                    'review_id' => (int) $existingAppraisal['id'],
                    'redirect_url' => base_url('my-reviews/review/' . $existingAppraisal['id'])
                ]
            ];
        }

        $now = date('Y-m-d H:i:s');

        $this->db->transStart();

        $this->db->table('appraisals')->insert([
            'appraisal_cycle_id' => $cycleId,
            'employee_id' => $userId,
            'reviewer_id' => $userId,
            'reviewer_role_id' => $role['role_id'],
            'review_type' => 'self',
            'template_id' => $template['template_id'],
            'status' => 'in_progress',
            'overall_score' => 0,
            'created_at' => $now,
            'updated_at' => $now
        ]);

        $appraisalId = (int) $this->db->insertID();

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Unable to start your review. Please try again.'];
        }

        return [
            'success' => true,
            'message' => 'Review started successfully.',
            'data' => [
                'review_id' => $appraisalId,
                'redirect_url' => base_url('my-reviews/review/' . $appraisalId)
            ]
        ];
    }

    // protected function resolveTemplate(int $cycleId, array $employee, int $reviewerRoleId): ?array
    // {
    //     return $this->db->query("
    //     SELECT template_id
    //     FROM appraisal_cycle_template_assignments
    //     WHERE appraisal_cycle_id = ?
    //         AND review_type = 'self'
    //         AND reviewer_role_id = ?
    //         AND (
    //             (assignment_type = 'employee' AND employee_id = ?)
    //             OR (assignment_type = 'designation' AND designation_id = ?)
    //             OR (assignment_type = 'department' AND department_id = ?)
    //         )
    //     ORDER BY
    //         CASE
    //             WHEN assignment_type = 'employee' AND employee_id = ? THEN 1
    //             WHEN assignment_type = 'designation' AND designation_id = ? THEN 2
    //             WHEN assignment_type = 'department' AND department_id = ? THEN 3
    //             ELSE 999
    //         END,
    //         priority ASC,
    //         id ASC
    //     LIMIT 1", [
    //         $cycleId,
    //         $reviewerRoleId,
    //         $employee['id'],
    //         $employee['designation_id'],
    //         $employee['department_id'],
    //         $employee['id'],
    //         $employee['designation_id'],
    //         $employee['department_id']
    //     ])->getRowArray();
    // }

    public function getReviewData(int $reviewId, int $userId): array
    {
        if ($reviewId <= 0 || $userId <= 0) {
            return ['success' => false, 'message' => 'Invalid review request.'];
        }

        $review = $this->db->table('appraisals a')
            ->select('a.id, a.appraisal_cycle_id, a.employee_id, a.reviewer_id, a.reviewer_role_id, a.template_id, a.status, a.overall_score, a.overall_comment, a.submitted_at, ac.cycle_name, ac.cycle_code, ac.start_date, ac.end_date, ac.status AS cycle_status, at.template_name, u.first_name, u.last_name, u.full_name, u.employee_code, d.name AS department_name, des.title AS designation_name')
            ->join('appraisal_cycles ac', 'ac.id = a.appraisal_cycle_id')
            ->join('appraisal_templates at', 'at.id = a.template_id')
            ->join('users u', 'u.id = a.employee_id')
            ->join('departments d', 'd.id = u.department_id', 'left')
            ->join('designations des', 'des.id = u.designation_id', 'left')
            ->where('a.id', $reviewId)
            ->where('a.reviewer_id', $userId)
            ->get()
            ->getRowArray();

        if (!$review) {
            return ['success' => false, 'message' => 'Review not found or you do not have permission to access it.'];
        }

        $sections = $this->db->table('appraisal_template_sections')
            ->select('id, template_id, section_name, sort_order')
            ->where('template_id', $review['template_id'])
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $sectionIds = array_column($sections, 'id');
        $questions = [];

        if (!empty($sectionIds)) {
            $questions = $this->db->table('appraisal_questions')
                ->select('id, section_id, question, answer_type, is_required, weight, sort_order')
                ->whereIn('section_id', $sectionIds)
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();
        }

        $answers = $this->db->table('appraisal_answers')
            ->select('id, question_id, rating, answer_text, answer_number, answer_yes_no, comment')
            ->where('appraisal_id', $reviewId)
            ->get()
            ->getResultArray();

        $answersByQuestion = [];

        foreach ($answers as $answer) {
            $answersByQuestion[$answer['question_id']] = [
                'id' => (int) $answer['id'],
                'rating' => $answer['rating'],
                'answer_text' => $answer['answer_text'],
                'answer_number' => $answer['answer_number'],
                'answer_yes_no' => $answer['answer_yes_no'],
                'comment' => $answer['comment']
            ];
        }

        $questionsBySection = [];

        foreach ($questions as $question) {
            $question['id'] = (int) $question['id'];
            $question['section_id'] = (int) $question['section_id'];
            $question['is_required'] = (bool) $question['is_required'];
            $question['weight'] = (float) $question['weight'];
            $question['answer'] = $answersByQuestion[$question['id']] ?? [
                'id' => null,
                'rating' => null,
                'answer_text' => null,
                'answer_number' => null,
                'answer_yes_no' => null,
                'comment' => null
            ];

            $questionsBySection[$question['section_id']][] = $question;
        }

        foreach ($sections as &$section) {
            $section['id'] = (int) $section['id'];
            $section['template_id'] = (int) $section['template_id'];
            $section['questions'] = $questionsBySection[$section['id']] ?? [];
        }

        unset($section);

        $employeeName = trim($review['full_name'] ?: trim(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')));

        return [
            'success' => true,
            'data' => [
                'review' => [
                    'id' => (int) $review['id'],
                    'status' => $review['status'],
                    'overall_score' => $review['overall_score'],
                    'overall_comment' => $review['overall_comment'],
                    'submitted_at' => $review['submitted_at']
                ],
                'cycle' => [
                    'id' => (int) $review['appraisal_cycle_id'],
                    'name' => $review['cycle_name'],
                    'code' => $review['cycle_code'],
                    'start_date' => $review['start_date'],
                    'end_date' => $review['end_date']
                ],
                'template' => [
                    'id' => (int) $review['template_id'],
                    'name' => $review['template_name']
                ],
                'employee' => [
                    'id' => (int) $review['employee_id'],
                    'name' => $employeeName,
                    'employee_code' => $review['employee_code'],
                    'department' => $review['department_name'],
                    'designation' => $review['designation_name']
                ],
                'sections' => $sections
            ]
        ];
    }

    public function saveDraft(int $reviewId, int $userId, array $payload): array
    {
        if ($reviewId <= 0 || $userId <= 0) return ['success' => false, 'message' => 'Invalid review request.'];

        $review = $this->db->table('appraisals')
            ->select('id, template_id, status')
            ->where('id', $reviewId)
            ->where('reviewer_id', $userId)
            ->get()
            ->getRowArray();

        if (!$review) return ['success' => false, 'message' => 'Review not found or you do not have permission to modify it.'];

        if (in_array($review['status'], ['submitted', 'approved'], true)) {
            return ['success' => false, 'message' => 'This review has already been submitted and cannot be modified.'];
        }

        $answers = $payload['answers'] ?? [];

        if (!is_array($answers)) {
            return ['success' => false, 'message' => 'Invalid answer data.'];
        }

        $sections = $this->db->table('appraisal_template_sections')
            ->select('id')
            ->where('template_id', $review['template_id'])
            ->get()
            ->getResultArray();

        $sectionIds = array_column($sections, 'id');

        $questions = [];

        if (!empty($sectionIds)) {
            $questions = $this->db->table('appraisal_questions')
                ->select('id, answer_type')
                ->whereIn('section_id', $sectionIds)
                ->get()
                ->getResultArray();
        }

        $validQuestions = [];

        foreach ($questions as $question) {
            $validQuestions[(int) $question['id']] = $question['answer_type'];
        }

        $now = date('Y-m-d H:i:s');

        $this->db->transStart();

        foreach ($answers as $answer) {
            $questionId = (int) ($answer['question_id'] ?? 0);

            if ($questionId <= 0 || !isset($validQuestions[$questionId])) continue;

            $answerType = $validQuestions[$questionId];

            $data = [
                'rating' => null,
                'answer_text' => null,
                'answer_number' => null,
                'answer_yes_no' => null,
                'comment' => isset($answer['comment']) && $answer['comment'] !== '' ? trim((string) $answer['comment']) : null,
                'updated_at' => $now
            ];

            if ($answerType === 'rating') {
                $data['rating'] = isset($answer['rating']) && $answer['rating'] !== '' ? (float) $answer['rating'] : null;
            }

            if ($answerType === 'text') {
                $data['answer_text'] = isset($answer['answer_text']) && $answer['answer_text'] !== '' ? trim((string) $answer['answer_text']) : null;
            }

            if ($answerType === 'number') {
                $data['answer_number'] = isset($answer['answer_number']) && $answer['answer_number'] !== '' ? (float) $answer['answer_number'] : null;
            }

            if ($answerType === 'yes_no') {
                $yesNo = $answer['answer_yes_no'] ?? null;

                if ($yesNo !== null && $yesNo !== '') {
                    $data['answer_yes_no'] = filter_var($yesNo, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                    if ($data['answer_yes_no'] !== null) {
                        $data['answer_yes_no'] = $data['answer_yes_no'] ? 1 : 0;
                    }
                }
            }

            $existingAnswer = $this->db->table('appraisal_answers')
                ->select('id')
                ->where('appraisal_id', $reviewId)
                ->where('question_id', $questionId)
                ->get()
                ->getRowArray();

            if ($existingAnswer) {
                $this->db->table('appraisal_answers')
                    ->where('id', $existingAnswer['id'])
                    ->update($data);
            } else {
                $hasAnswer = $data['rating'] !== null
                    || $data['answer_text'] !== null
                    || $data['answer_number'] !== null
                    || $data['answer_yes_no'] !== null
                    || $data['comment'] !== null;

                if (!$hasAnswer) continue;

                $data['appraisal_id'] = $reviewId;
                $data['question_id'] = $questionId;
                $data['created_at'] = $now;

                $this->db->table('appraisal_answers')->insert($data);
            }
        }

        $this->db->table('appraisals')
            ->where('id', $reviewId)
            ->update([
                'status' => 'in_progress',
                'overall_comment' => isset($payload['overall_comment']) && $payload['overall_comment'] !== '' ? trim((string) $payload['overall_comment']) : null,
                'updated_at' => $now
            ]);

        $this->db->transComplete();

        if (!$this->db->transStatus()) {
            return ['success' => false, 'message' => 'Unable to save your draft. Please try again.'];
        }

        return [
            'success' => true,
            'message' => 'Draft saved successfully.'
        ];
    }
}
