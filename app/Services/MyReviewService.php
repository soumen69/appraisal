<?php

namespace App\Services;

use App\Models\AppraisalCycleTemplateAssignmentModel;
use RuntimeException;
use InvalidArgumentException;

class MyReviewService
{
    protected AppraisalCycleTemplateAssignmentModel $assignments;
    protected $db;

    public function __construct()
    {
        $this->assignments = new AppraisalCycleTemplateAssignmentModel();
        $this->db = db_connect();
    }

    // public function getMyReviews(int $employeeId): array
    // {
    //     if ($employeeId <= 0) return [];

    //     $reviews = $this->assignments->getEmployeeMyReviews($employeeId);

    //     foreach ($reviews as &$review) {
    //         $review['template_id'] = $review['resolved_template_id'] ?? null;
    //         $review['template_name'] = $review['resolved_template_name'] ?? null;

    //         $appraisal = $this->db->table('appraisals')
    //             ->select('id, status')
    //             ->where('appraisal_cycle_id', $review['cycle_id'])
    //             ->where('employee_id', $employeeId)
    //             ->where('reviewer_id', $employeeId)
    //             ->get()
    //             ->getRowArray();

    //         $review['appraisal_id'] = $appraisal['id'] ?? null;
    //         $review['status'] = $appraisal['status'] ?? 'pending';
    //     }

    //     unset($review);

    //     return $reviews;
    // }

    public function getMyReviews(int $userId): array
    {
        if ($userId <= 0) {
            return [
                'assigned_to_me' => [],
                'about_me' => []
            ];
        }

        $assignedToMe = $this->db->table('appraisals a')
            ->select('
                a.id AS appraisal_id,
                a.appraisal_cycle_id AS cycle_id,
                a.employee_id,
                a.reviewer_id,
                a.reviewer_role_id,
                a.review_type,
                a.template_id,
                a.status,
                a.overall_score,
                a.submitted_at,
                ac.cycle_name,
                ac.cycle_code,
                ac.start_date,
                ac.end_date,
                ac.status AS cycle_status,
                at.template_name,
                eu.first_name AS employee_first_name,
                eu.last_name AS employee_last_name,
                eu.full_name AS employee_full_name,
                eu.employee_code,
                ed.name AS employee_department,
                edes.title AS employee_designation
            ')
            ->join('appraisal_cycles ac', 'ac.id = a.appraisal_cycle_id')
            ->join('appraisal_templates at', 'at.id = a.template_id')
            ->join('users eu', 'eu.id = a.employee_id')
            ->join('departments ed', 'ed.id = eu.department_id', 'left')
            ->join('designations edes', 'edes.id = eu.designation_id', 'left')
            ->where('a.reviewer_id', $userId)
            ->orderBy('a.status', 'ASC')
            ->orderBy('ac.start_date', 'DESC')
            ->orderBy('a.id', 'DESC')
            ->get()
            ->getResultArray();

        $aboutMe = $this->db->table('appraisals a')
            ->select('
                a.id AS appraisal_id,
                a.appraisal_cycle_id AS cycle_id,
                a.employee_id,
                a.reviewer_id,
                a.reviewer_role_id,
                a.review_type,
                a.template_id,
                a.status,
                a.overall_score,
                a.submitted_at,
                ac.cycle_name,
                ac.cycle_code,
                ac.start_date,
                ac.end_date,
                ac.status AS cycle_status,
                at.template_name,
                ru.first_name AS reviewer_first_name,
                ru.last_name AS reviewer_last_name,
                ru.full_name AS reviewer_full_name,
                ru.employee_code AS reviewer_employee_code,
                rr.display_name AS reviewer_role_name
            ')
            ->join('appraisal_cycles ac', 'ac.id = a.appraisal_cycle_id')
            ->join('appraisal_templates at', 'at.id = a.template_id')
            ->join('users ru', 'ru.id = a.reviewer_id')
            ->join('roles rr', 'rr.id = a.reviewer_role_id', 'left')
            ->where('a.employee_id', $userId)
            ->where('a.reviewer_id !=', $userId)
            ->orderBy('ac.start_date', 'DESC')
            ->orderBy('a.id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($assignedToMe as &$review) {
            $review['view_type'] = 'assigned';
            $review['employee_name'] = $this->getUserName($review['employee_full_name'], $review['employee_first_name'], $review['employee_last_name']);
            $review['reviewer_name'] = null;
            $review['can_edit'] = in_array($review['status'], ['pending', 'in_progress'], true);
            $review['can_submit'] = in_array($review['status'], ['pending', 'in_progress'], true);
        }

        unset($review);

        foreach ($aboutMe as &$review) {
            $review['view_type'] = 'about_me';
            $review['employee_name'] = null;
            $review['reviewer_name'] = $this->getUserName($review['reviewer_full_name'], $review['reviewer_first_name'], $review['reviewer_last_name']);
            $review['can_edit'] = false;
            $review['can_submit'] = false;
        }

        unset($review);

        $selfAvailable = $this->getAvailableSelfReviews($userId);

        return [
            'assigned_to_me' => $assignedToMe,
            'about_me' => $aboutMe,
            'available_self_reviews' => $selfAvailable
        ];
    }

    protected function getUserName(?string $fullName, ?string $firstName, ?string $lastName): string
    {
        $name = trim((string) $fullName);

        if ($name !== '') {
            return $name;
        }

        return trim(trim((string) $firstName) . ' ' . trim((string) $lastName));
    }

    protected function getAvailableSelfReviews(int $userId): array
    {
        $employee = $this->db->table('users')
            ->select('id, organization_id, department_id, designation_id')
            ->where('id', $userId)
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        if (!$employee) {
            return [];
        }

        $role = $this->db->table('user_roles')
            ->select('role_id')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$role) {
            return [];
        }

        $cycles = $this->db->table('appraisal_cycles ac')
            ->select('ac.id AS cycle_id, ac.cycle_name, ac.cycle_code, ac.start_date, ac.end_date, ac.status AS cycle_status')
            ->join(
                'appraisal_cycle_participants cp',
                'cp.appraisal_cycle_id = ac.id AND cp.employee_id = ' . $this->db->escape($userId) . ' AND cp.status = \'active\''
            )
            ->where('ac.organization_id', $employee['organization_id'])
            ->where('ac.status', 'active')
            ->get()
            ->getResultArray();

        $available = [];

        foreach ($cycles as $cycle) {
            $existing = $this->db->table('appraisals')
                ->select('id')
                ->where('appraisal_cycle_id', $cycle['cycle_id'])
                ->where('employee_id', $userId)
                ->where('reviewer_id', $userId)
                ->where('review_type', 'self')
                ->get()
                ->getRowArray();

            if ($existing) {
                continue;
            }

            $selfAllowed = $this->db->table('review_matrix')
                ->select('id')
                ->where('organization_id', $employee['organization_id'])
                ->where('reviewer_role_id', $role['role_id'])
                ->where('reviewee_role_id', $role['role_id'])
                ->where('allow_self_review', 1)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if (!$selfAllowed) {
                continue;
            }

            $template = $this->assignments->resolveTemplate(
                (int) $cycle['cycle_id'],
                $userId,
                'self'
            );

            if (!$template) {
                continue;
            }

            $cycle['template_id'] = (int) $template['template_id'];
            $cycle['template_name'] = $template['template_name'] ?? null;
            $available[] = $cycle;
        }

        return $available;
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
            ->where('review_type', 'self')
            ->get()
            ->getRowArray();

        if ($existingAppraisal) {
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

        $this->db->transBegin();

        try {
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

            if ($this->db->transStatus() === false) {
                throw new RuntimeException('Unable to create the review.');
            }

            $appraisalId = (int) $this->db->insertID();

            $this->db->transCommit();

            return [
                'success' => true,
                'message' => 'Review started successfully.',
                'data' => [
                    'review_id' => $appraisalId,
                    'redirect_url' => base_url('my-reviews/review/' . $appraisalId)
                ]
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return [
                'success' => false,
                'message' => 'Unable to start your review. Please try again.'
            ];
        }
    }

    // public function getReviewData(int $reviewId, int $userId): array
    // {
    //     if ($reviewId <= 0 || $userId <= 0) {
    //         return ['success' => false, 'message' => 'Invalid review request.'];
    //     }

    //     $review = $this->db->table('appraisals a')
    //         ->select('a.id, a.appraisal_cycle_id, a.employee_id, a.reviewer_id, a.reviewer_role_id, a.template_id, a.status, a.overall_score, a.overall_comment, a.submitted_at, ac.cycle_name, ac.cycle_code, ac.start_date, ac.end_date, ac.status AS cycle_status, at.template_name, u.first_name, u.last_name, u.full_name, u.employee_code, d.name AS department_name, des.title AS designation_name')
    //         ->join('appraisal_cycles ac', 'ac.id = a.appraisal_cycle_id')
    //         ->join('appraisal_templates at', 'at.id = a.template_id')
    //         ->join('users u', 'u.id = a.employee_id')
    //         ->join('departments d', 'd.id = u.department_id', 'left')
    //         ->join('designations des', 'des.id = u.designation_id', 'left')
    //         ->where('a.id', $reviewId)
    //         ->where('a.reviewer_id', $userId)
    //         ->get()
    //         ->getRowArray();

    //     if (!$review) {
    //         return ['success' => false, 'message' => 'Review not found or you do not have permission to access it.'];
    //     }

    //     $sections = $this->db->table('appraisal_template_sections')
    //         ->select('id, template_id, section_name, sort_order')
    //         ->where('template_id', $review['template_id'])
    //         ->orderBy('sort_order', 'ASC')
    //         ->orderBy('id', 'ASC')
    //         ->get()
    //         ->getResultArray();

    //     $sectionIds = array_column($sections, 'id');
    //     $questions = [];

    //     if (!empty($sectionIds)) {
    //         $questions = $this->db->table('appraisal_questions')
    //             ->select('id, section_id, question, answer_type, is_required, weight, sort_order')
    //             ->whereIn('section_id', $sectionIds)
    //             ->orderBy('sort_order', 'ASC')
    //             ->orderBy('id', 'ASC')
    //             ->get()
    //             ->getResultArray();
    //     }

    //     $answers = $this->db->table('appraisal_answers')
    //         ->select('id, question_id, rating, answer_text, answer_number, answer_yes_no, comment')
    //         ->where('appraisal_id', $reviewId)
    //         ->get()
    //         ->getResultArray();

    //     $answersByQuestion = [];

    //     foreach ($answers as $answer) {
    //         $answersByQuestion[$answer['question_id']] = [
    //             'id' => (int) $answer['id'],
    //             'rating' => $answer['rating'],
    //             'answer_text' => $answer['answer_text'],
    //             'answer_number' => $answer['answer_number'],
    //             'answer_yes_no' => $answer['answer_yes_no'],
    //             'comment' => $answer['comment']
    //         ];
    //     }

    //     $questionsBySection = [];

    //     foreach ($questions as $question) {
    //         $question['id'] = (int) $question['id'];
    //         $question['section_id'] = (int) $question['section_id'];
    //         $question['is_required'] = (bool) $question['is_required'];
    //         $question['weight'] = (float) $question['weight'];
    //         $question['answer'] = $answersByQuestion[$question['id']] ?? [
    //             'id' => null,
    //             'rating' => null,
    //             'answer_text' => null,
    //             'answer_number' => null,
    //             'answer_yes_no' => null,
    //             'comment' => null
    //         ];

    //         $questionsBySection[$question['section_id']][] = $question;
    //     }

    //     foreach ($sections as &$section) {
    //         $section['id'] = (int) $section['id'];
    //         $section['template_id'] = (int) $section['template_id'];
    //         $section['questions'] = $questionsBySection[$section['id']] ?? [];
    //     }

    //     unset($section);

    //     $employeeName = trim($review['full_name'] ?: trim(($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')));

    //     return [
    //         'success' => true,
    //         'data' => [
    //             'review' => [
    //                 'id' => (int) $review['id'],
    //                 'status' => $review['status'],
    //                 'overall_score' => $review['overall_score'],
    //                 'overall_comment' => $review['overall_comment'],
    //                 'submitted_at' => $review['submitted_at']
    //             ],
    //             'cycle' => [
    //                 'id' => (int) $review['appraisal_cycle_id'],
    //                 'name' => $review['cycle_name'],
    //                 'code' => $review['cycle_code'],
    //                 'start_date' => $review['start_date'],
    //                 'end_date' => $review['end_date']
    //             ],
    //             'template' => [
    //                 'id' => (int) $review['template_id'],
    //                 'name' => $review['template_name']
    //             ],
    //             'employee' => [
    //                 'id' => (int) $review['employee_id'],
    //                 'name' => $employeeName,
    //                 'employee_code' => $review['employee_code'],
    //                 'department' => $review['department_name'],
    //                 'designation' => $review['designation_name']
    //             ],
    //             'sections' => $sections
    //         ]
    //     ];
    // }

    public function getReviewData(int $reviewId, int $userId): array
    {
        if ($reviewId <= 0 || $userId <= 0) {
            return ['success' => false, 'message' => 'Invalid review request.'];
        }

        $review = $this->db->table('appraisals a')
            ->select('
                a.id,
                a.appraisal_cycle_id,
                a.employee_id,
                a.reviewer_id,
                a.reviewer_role_id,
                a.review_type,
                a.template_id,
                a.status,
                a.overall_score,
                a.overall_comment,
                a.submitted_at,
                a.approved_at,
                ac.cycle_name,
                ac.cycle_code,
                ac.start_date,
                ac.end_date,
                ac.status AS cycle_status,
                at.template_name,
                eu.first_name AS employee_first_name,
                eu.last_name AS employee_last_name,
                eu.full_name AS employee_full_name,
                eu.employee_code,
                ed.name AS department_name,
                edes.title AS designation_name,
                ru.first_name AS reviewer_first_name,
                ru.last_name AS reviewer_last_name,
                ru.full_name AS reviewer_full_name,
                ru.employee_code AS reviewer_employee_code,
                rr.display_name AS reviewer_role_name
            ')
            ->join('appraisal_cycles ac', 'ac.id = a.appraisal_cycle_id')
            ->join('appraisal_templates at', 'at.id = a.template_id')
            ->join('users eu', 'eu.id = a.employee_id')
            ->join('users ru', 'ru.id = a.reviewer_id')
            ->join('roles rr', 'rr.id = a.reviewer_role_id', 'left')
            ->join('departments ed', 'ed.id = eu.department_id', 'left')
            ->join('designations edes', 'edes.id = eu.designation_id', 'left')
            ->where('a.id', $reviewId)
            ->groupStart()
            ->where('a.reviewer_id', $userId)
            ->orWhere('a.employee_id', $userId)
            ->groupEnd()
            ->get()
            ->getRowArray();

        if (!$review) {
            return [
                'success' => false,
                'message' => 'Review not found or you do not have permission to access it.'
            ];
        }

        $canEdit = (int) $review['reviewer_id'] === $userId
            && in_array($review['status'], ['pending', 'in_progress'], true);

        $canSubmit = $canEdit;

        $sections = $this->db->table('appraisal_template_sections')
            ->select('id, template_id, section_name, sort_order')
            ->where('template_id', $review['template_id'])
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $sectionIds = array_map('intval', array_column($sections, 'id'));
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
            $answersByQuestion[(int) $answer['question_id']] = [
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

        $employeeName = $this->getUserName(
            $review['employee_full_name'],
            $review['employee_first_name'],
            $review['employee_last_name']
        );

        $reviewerName = $this->getUserName(
            $review['reviewer_full_name'],
            $review['reviewer_first_name'],
            $review['reviewer_last_name']
        );

        return [
            'success' => true,
            'data' => [
                'review' => [
                    'id' => (int) $review['id'],
                    'status' => $review['status'],
                    'review_type' => $review['review_type'],
                    'overall_score' => $review['overall_score'],
                    'overall_comment' => $review['overall_comment'],
                    'submitted_at' => $review['submitted_at'],
                    'approved_at' => $review['approved_at'],
                    'can_edit' => $canEdit,
                    'can_submit' => $canSubmit,
                    'is_reviewer' => (int) $review['reviewer_id'] === $userId,
                    'is_employee' => (int) $review['employee_id'] === $userId
                ],
                'cycle' => [
                    'id' => (int) $review['appraisal_cycle_id'],
                    'name' => $review['cycle_name'],
                    'code' => $review['cycle_code'],
                    'start_date' => $review['start_date'],
                    'end_date' => $review['end_date'],
                    'status' => $review['cycle_status']
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
                'reviewer' => [
                    'id' => (int) $review['reviewer_id'],
                    'name' => $reviewerName,
                    'employee_code' => $review['reviewer_employee_code'],
                    'role' => $review['reviewer_role_name']
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

        if (!in_array($review['status'], ['pending', 'in_progress'], true)) {
            return [
                'success' => false,
                'message' => 'This review is locked and cannot be modified.'
            ];
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

    public function submitReview(int $reviewId, int $userId, array $payload): array
    {
        if ($reviewId <= 0 || $userId <= 0) {
            return ['success' => false, 'message' => 'Invalid review request.'];
        }

        $review = $this->db->table('appraisals')
            ->select('id, template_id, status')
            ->where('id', $reviewId)
            ->where('reviewer_id', $userId)
            ->get()
            ->getRowArray();

        if (!$review) {
            return ['success' => false, 'message' => 'Review not found or you do not have permission to submit it.'];
        }

        if (!in_array($review['status'], ['pending', 'in_progress'], true)) {
            return [
                'success' => false,
                'message' => 'This review is locked and cannot be submitted.'
            ];
        }
        $answers = $payload['answers'] ?? [];

        if (!is_array($answers)) {
            return ['success' => false, 'message' => 'Invalid answer data.'];
        }

        $sections = $this->db->table('appraisal_template_sections')
            ->select('id')
            ->where('template_id', (int)$review['template_id'])
            ->get()
            ->getResultArray();

        $sectionIds = array_map('intval', array_column($sections, 'id'));

        if (empty($sectionIds)) {
            return ['success' => false, 'message' => 'No questions are available for this review.'];
        }

        $questions = $this->db->table('appraisal_questions')
            ->select('id, answer_type, is_required, weight')
            ->whereIn('section_id', $sectionIds)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($questions)) {
            return ['success' => false, 'message' => 'No questions are available for this review.'];
        }

        $validQuestions = [];

        foreach ($questions as $question) {
            $validQuestions[(int)$question['id']] = [
                'answer_type' => $question['answer_type'],
                'is_required' => (int)($question['is_required'] ?? 0),
                'weight' => (float)($question['weight'] ?? 1)
            ];
        }

        $submittedAnswers = [];

        foreach ($answers as $answer) {
            $questionId = (int)($answer['question_id'] ?? 0);

            if ($questionId <= 0 || !isset($validQuestions[$questionId])) {
                continue;
            }

            $submittedAnswers[$questionId] = $answer;
        }

        foreach ($validQuestions as $questionId => $question) {
            if (!$question['is_required']) {
                continue;
            }

            $answer = $submittedAnswers[$questionId] ?? null;

            if (!$this->hasValidAnswerForSubmission($answer, $question['answer_type'])) {
                return [
                    'success' => false,
                    'message' => 'Please answer all required questions before submitting.'
                ];
            }
        }

        $now = date('Y-m-d H:i:s');

        $this->db->transBegin();

        try {
            $ratingTotal = 0;
            $ratingWeightTotal = 0;

            foreach ($submittedAnswers as $questionId => $answer) {
                $answerType = $validQuestions[$questionId]['answer_type'];

                $data = [
                    'rating' => null,
                    'answer_text' => null,
                    'answer_number' => null,
                    'answer_yes_no' => null,
                    'comment' => isset($answer['comment']) && $answer['comment'] !== ''
                        ? trim((string)$answer['comment'])
                        : null,
                    'updated_at' => $now
                ];

                if ($answerType === 'rating') {
                    $rating = isset($answer['rating']) && $answer['rating'] !== ''
                        ? (float)$answer['rating']
                        : null;

                    $data['rating'] = $rating;

                    if ($rating !== null) {
                        $weight = $validQuestions[$questionId]['weight'];

                        if ($weight <= 0) {
                            $weight = 1;
                        }

                        $ratingTotal += $rating * $weight;
                        $ratingWeightTotal += $weight;
                    }
                }

                if ($answerType === 'text') {
                    $data['answer_text'] = isset($answer['answer_text']) && trim((string)$answer['answer_text']) !== ''
                        ? trim((string)$answer['answer_text'])
                        : null;
                }

                if ($answerType === 'number') {
                    $data['answer_number'] = isset($answer['answer_number']) && $answer['answer_number'] !== ''
                        ? (float)$answer['answer_number']
                        : null;
                }

                if ($answerType === 'yes_no') {
                    $yesNo = $answer['answer_yes_no'] ?? null;

                    if ($yesNo !== null && $yesNo !== '') {
                        $value = filter_var($yesNo, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                        if ($value === null && ($yesNo === 0 || $yesNo === '0' || $yesNo === false)) {
                            $value = false;
                        }

                        if ($value === null && ($yesNo === 1 || $yesNo === '1' || $yesNo === true)) {
                            $value = true;
                        }

                        if ($value === null) {
                            throw new InvalidArgumentException('Invalid yes/no answer.');
                        }

                        $data['answer_yes_no'] = $value ? 1 : 0;
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
                        ->where('id', (int)$existingAnswer['id'])
                        ->update($data);
                } else {
                    $hasAnswer = $data['rating'] !== null
                        || $data['answer_text'] !== null
                        || $data['answer_number'] !== null
                        || $data['answer_yes_no'] !== null
                        || $data['comment'] !== null;

                    if (!$hasAnswer) {
                        continue;
                    }

                    $data['appraisal_id'] = $reviewId;
                    $data['question_id'] = $questionId;
                    $data['created_at'] = $now;

                    $this->db->table('appraisal_answers')->insert($data);
                }
            }

            $overallScore = $ratingWeightTotal > 0
                ? round($ratingTotal / $ratingWeightTotal, 2)
                : 0;

            $overallComment = isset($payload['overall_comment']) && trim((string)$payload['overall_comment']) !== ''
                ? trim((string)$payload['overall_comment'])
                : null;

            $this->db->table('appraisals')
                ->where('id', $reviewId)
                ->where('reviewer_id', $userId)
                ->whereIn('status', ['pending', 'in_progress'])
                ->update([
                    'status' => 'submitted',
                    'overall_score' => $overallScore,
                    'overall_comment' => $overallComment,
                    'submitted_at' => $now,
                    'updated_at' => $now
                ]);

            if ($this->db->affectedRows() <= 0) {
                throw new RuntimeException('Unable to submit this review. The review may already have been submitted.');
            }

            if ($this->db->transStatus() === false) {
                throw new RuntimeException('Unable to submit your review.');
            }

            $this->db->transCommit();

            return [
                'success' => true,
                'message' => 'Review submitted successfully.',
                'data' => [
                    'review_id' => $reviewId,
                    'status' => 'submitted',
                    'overall_score' => $overallScore,
                    'submitted_at' => $now
                ]
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();

            return [
                'success' => false,
                'message' => $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Unable to submit your review. Please try again.'
            ];
        }
    }

    protected function hasValidAnswerForSubmission(?array $answer, string $answerType): bool
    {
        if (!$answer) {
            return false;
        }

        return match ($answerType) {
            'rating' => isset($answer['rating']) && $answer['rating'] !== '' && is_numeric($answer['rating']),
            'text' => isset($answer['answer_text']) && trim((string)$answer['answer_text']) !== '',
            'number' => isset($answer['answer_number']) && $answer['answer_number'] !== '' && is_numeric($answer['answer_number']),
            'yes_no' => array_key_exists('answer_yes_no', $answer)
                && $answer['answer_yes_no'] !== null
                && $answer['answer_yes_no'] !== '',
            default => false
        };
    }
}
