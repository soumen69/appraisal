<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReviewModel;
use Throwable;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AppSettingModel;
use App\Models\ReviewMatrixModel;
use App\Models\UserModel;
use App\Models\AppraisalEmployeeFinalScoreModel;

class ReviewController extends BaseController
{
    protected ReviewModel $reviews;
    protected ReviewMatrixModel $reviewMatrix;
    protected AppSettingModel $settings;
    protected UserModel $users;
    protected AppraisalEmployeeFinalScoreModel $finalScores;


    public function __construct()
    {
        $this->reviews      = new ReviewModel();
        $this->reviewMatrix = new ReviewMatrixModel();
        $this->settings     = new AppSettingModel();
        $this->users = new UserModel();
        $this->finalScores = new AppraisalEmployeeFinalScoreModel();
    }

    public function index()
    {
        return view('reviews/index', [
            'title' => 'Reviews',
            'page_title' => 'Appraisal Reviews',
            'page_subtitle' => 'Review employee appraisal results, inspect score breakdowns, and verify submitted appraisals.',
        ]);
    }

    public function list()
    {
        try {
            $filters = $this->request->getGet();

            $page = max(1, (int) ($filters['page'] ?? 1));
            $pageSize = (int) ($filters['pageSize'] ?? 10);

            if (!in_array($pageSize, [10, 25, 50, 100], true)) {
                $pageSize = 10;
            }

            $data = $this->reviews->getReviews(
                $page,
                $pageSize,
                trim($filters['search'] ?? ''),
                trim($filters['cycle_id'] ?? ''),
                trim($filters['status'] ?? ''),
                trim($filters['review_type'] ?? ''),
                trim($filters['orderBy'] ?? 'id'),
                trim($filters['direction'] ?? 'desc')
            );

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            log_message(
                'error',
                'Appraisal reviews list error: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load appraisal reviews.',
                ]);
        }
    }

    public function view(int $id)
    {
        return view('reviews/view', [
            'title' => 'Review Details',
            'page_title' => 'Appraisal Review',
            'page_subtitle' => 'View appraisal review details.',
            'reviewId' => $id,
        ]);
    }

    public function data(int $id)
    {
        try {
            if ($id <= 0) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Appraisal review not found.',
                    ]);
            }

            $review = $this->reviews->getReview($id);

            if (!$review) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Appraisal review not found.',
                    ]);
            }

            $templateId = (int) ($review['template_id'] ?? 0);

            $sections = $templateId > 0
                ? $this->reviews->getReviewSections($templateId, $id)
                : [];

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'review' => $review,
                    'sections' => $sections,
                ],
            ]);
        } catch (Throwable $e) {
            log_message(
                'error',
                'Appraisal review details error: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load appraisal review.',
                ]);
        }
    }

    public function cycles()
    {
        try {
            return $this->response->setJSON([
                'success' => true,
                'data' => $this->reviews->getCyclesForFilter(),
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Appraisal review cycles filter error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Unable to load appraisal cycles.',
            ]);
        }
    }

    public function calculateFinalScore(): ResponseInterface
    {
        $employeeCode = trim(
            (string) ($this->request->getPost('employee_code') ?? '')
        );

        $cycleId = (int) (
            $this->request->getPost('cycle_id') ?? 0
        );

        $calculationRule = trim(
            (string) ($this->request->getPost('calculation_rule') ?? '')
        );

        if ($employeeCode === '' || $cycleId <= 0 || $calculationRule === '') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'success' => false,
                    'message' => 'Employee code, appraisal cycle and calculation rule are required.',
                ]);
        }

        try {
            $result = $this->calculateFinalScoreCalculation(
                $employeeCode,
                $cycleId,
                $calculationRule
            );

            return $this->response->setJSON($result);
        } catch (Throwable $e) {
            log_message(
                'error',
                'Final score calculation error: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to calculate the final score.',
                ]);
        }
    }

    private function calculateFinalScoreCalculation(
        string $employeeCode,
        int $cycleId,
        string $calculationRule
    ): array {
        $employeeCode = trim($employeeCode);
        $calculationRule = trim($calculationRule);

        if ($employeeCode === '') {
            return [
                'success' => false,
                'message' => 'Employee code is required.',
            ];
        }

        if ($cycleId <= 0) {
            return [
                'success' => false,
                'message' => 'Appraisal cycle is required.',
            ];
        }

        $allowedRules = [
            'global_cap',
            'ctc_slab',
        ];

        if (!in_array($calculationRule, $allowedRules, true)) {
            return [
                'success' => false,
                'message' => 'Invalid calculation rule.',
            ];
        }

        $employeeData = $this->getPeopleDeskEmployeeCtc($employeeCode);

        if ($employeeData['success'] !== true) {
            return $employeeData;
        }

        switch ($calculationRule) {
            case 'global_cap':
                return $this->calculateUsingGlobalCap(
                    $employeeData,
                    $cycleId
                );

            case 'ctc_slab':
                return $this->calculateUsingCtcSlab(
                    $employeeData,
                    $cycleId
                );

            default:
                return [
                    'success' => false,
                    'message' => 'Unsupported calculation rule.',
                ];
        }
    }

    private function calculateReviewScore(array $employee, int $cycleId): array
    {
        $appraisalEmployeeId = (int) ($employee['id'] ?? 0);
        $organizationId = (int) ($employee['organization_id'] ?? 0);
        $revieweeRoleId = (int) ($employee['role_id'] ?? 0);

        if (
            $appraisalEmployeeId <= 0 ||
            $organizationId <= 0 ||
            $revieweeRoleId <= 0
        ) {
            return [
                'success' => false,
                'message' => 'Employee role configuration is incomplete.',
            ];
        }

        $reviews = $this->reviews->getCompletedReviewsForEmployeeCycle(
            $appraisalEmployeeId,
            $cycleId
        );

        if (empty($reviews)) {
            return [
                'success' => false,
                'message' => 'No completed reviews found for this appraisal cycle.',
            ];
        }

        $reviewMatrixRules = $this->reviewMatrix
            ->getActiveRulesForRevieweeRole(
                $organizationId,
                $revieweeRoleId
            );

        $selfReviewSetting = $this->reviewMatrix
            ->getSelfReviewSetting(
                $organizationId,
                $revieweeRoleId
            );

        $useReviewerWeightage = (int) $this->settings
            ->getGlobalFloat('use_reviewer_weightage', 1);

        $includedReviews = [];
        $selfScores = [];
        $matrixScores = [];

        foreach ($reviews as $review) {
            $reviewType = (string) ($review['review_type'] ?? '');
            $reviewScore = $review['overall_score'] ?? null;

            if ($reviewScore === null || $reviewScore === '') {
                continue;
            }

            $reviewScore = (float) $reviewScore;

            if ($reviewType === 'self') {
                if (
                    (int) ($selfReviewSetting['allow_self_review'] ?? 0) !== 1
                ) {
                    continue;
                }

                $selfScores[] = $reviewScore;

                $includedReviews[] = [
                    'review_type' => 'self',
                    'reviewer_role_id' => null,
                    'review_score' => $reviewScore,
                    'configured_weightage' => (float) (
                        $selfReviewSetting['weightage'] ?? 0
                    ),
                ];

                continue;
            }

            if ($reviewType === 'matrix') {
                $reviewerRoleId = (int) (
                    $review['reviewer_role_id'] ?? 0
                );

                $configuredWeightage = 0.0;

                foreach ($reviewMatrixRules as $rule) {
                    if (
                        (int) $rule['reviewer_role_id'] === $reviewerRoleId
                    ) {
                        $configuredWeightage = (float) $rule['weightage'];
                        break;
                    }
                }

                $matrixScores[] = $reviewScore;

                $includedReviews[] = [
                    'review_type' => 'matrix',
                    'reviewer_role_id' => $reviewerRoleId,
                    'review_score' => $reviewScore,
                    'configured_weightage' => $configuredWeightage,
                ];
            }
        }

        if (empty($includedReviews)) {
            return [
                'success' => false,
                'message' => 'No eligible completed reviews found for final score calculation.',
            ];
        }

        $reviewCount = count($includedReviews);
        $equalWeightage = 100 / $reviewCount;

        $finalScore = 0.0;
        $reviewDetails = [];

        foreach ($includedReviews as $review) {
            if ($useReviewerWeightage === 1) {
                $weightage = (float) $review['configured_weightage'];
            } else {
                $weightage = $equalWeightage;
            }

            if ($weightage <= 0) {
                continue;
            }

            $weightedContribution = (float) $review['review_score'] * ($weightage / 100);

            $finalScore += $weightedContribution;

            $reviewDetails[] = [
                'review_type' => $review['review_type'],
                'reviewer_role_id' => $review['reviewer_role_id'],
                'review_score' => (float) $review['review_score'],
                'weightage' => $weightage,
                'weighted_contribution' => $weightedContribution,
            ];
        }

        // $finalScore = round($finalScore, 2);

        // $selfScore = !empty($selfScores) ? round(array_sum($selfScores) / count($selfScores), 2) : null;
        // $matrixScore = !empty($matrixScores) ? round(array_sum($matrixScores) / count($matrixScores), 2) : null;

        $selfScore = !empty($selfScores) ? array_sum($selfScores) / count($selfScores) : null;
        $matrixScore = !empty($matrixScores) ? array_sum($matrixScores) / count($matrixScores) : null;

        if ($useReviewerWeightage === 1) {
            $configuredWeightTotal = 0.0;
            foreach ($includedReviews as $review) {
                $configuredWeightTotal += (float) $review['configured_weightage'];
            }

            if ($configuredWeightTotal <= 0) {
                return [
                    'success' => false,
                    'message' => 'No valid reviewer weightage is configured for the completed reviews.',
                ];
            }
        }

        return [
            'success' => true,
            'final_score' => $finalScore,
            'self_score' => $selfScore,
            'matrix_score' => $matrixScore,
            'review_count' => $reviewCount,
            'use_reviewer_weightage' => $useReviewerWeightage === 1,
            'equal_weightage' => $useReviewerWeightage === 0 ? $equalWeightage : null,
            'review_details' => $reviewDetails,
        ];
    }

    private function calculateFinalIncrement(float $currentCtc, float $finalScore, float $capPercentage): array
    {
        $normalizedScore = max(0, min(5, $finalScore)) / 5;
        $effectiveIncrementPercentage = $normalizedScore * $capPercentage;
        $incrementAmount = $currentCtc * ($effectiveIncrementPercentage / 100);
        $revisedCtc = $currentCtc + $incrementAmount;
        $actualIncrementPercentage = $currentCtc > 0
            ? (($revisedCtc - $currentCtc) / $currentCtc) * 100
            : 0.00;

        return [
            'current_ctc' => $currentCtc,
            'final_score' => $finalScore,
            'normalized_score' => $normalizedScore,
            'cap_percentage' => $capPercentage,
            'effective_increment_percentage' => $effectiveIncrementPercentage,
            'increment_amount' => $incrementAmount,
            'revised_ctc' => $revisedCtc,
            'actual_increment_percentage' => $actualIncrementPercentage,
        ];
    }

    private function getPeopleDeskEmployeeCtc(string $employeeCode): array
    {
        $peopleDeskDb = \Config\Database::connect([
            'DSN'        => '',
            'hostname'   => env('PEOPLEDESK_DB_HOST', 'localhost'),
            'username'   => env('PEOPLEDESK_DB_USER', 'root'),
            'password'   => env('PEOPLEDESK_DB_PASS', ''),
            'database'   => env('PEOPLEDESK_DB_NAME'),
            'DBDriver'   => 'MySQLi',
            'DBPrefix'   => '',
            'pConnect'   => false,
            'DBDebug'    => false,
            'charset'    => 'utf8mb4',
            'DBCollat'   => 'utf8mb4_general_ci',
            'swapPre'    => '',
            'encrypt'    => false,
            'compress'   => false,
            'strictOn'   => false,
            'failover'   => [],
            'port'       => (int) env('PEOPLEDESK_DB_PORT', 3306),
        ]);

        // 1. Find the employee using employee code.
        $employee = $peopleDeskDb
            ->table('employees')
            ->select('id, employee_code, name')
            ->where('employee_code', $employeeCode)
            ->where('status', 1)
            ->get()
            ->getRowArray();

        if (!$employee) {
            return [
                'success'       => false,
                'message'       => 'Employee not found in PeopleDesk.',
                'employee_code' => $employeeCode,
                'ctc'           => null,
            ];
        }

        // 2. Get the latest salary annexure.
        $salaryAnnexure = $peopleDeskDb
            ->table('salary_annexures')
            ->select('annual_ctc, effective_from')
            ->where('employee_id', $employee['id'])
            ->orderBy('effective_from', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!$salaryAnnexure) {
            return [
                'success'       => false,
                'message'       => 'No salary annexure found for the employee.',
                'employee_code' => $employeeCode,
                'employee_id'   => $employee['id'],
                'ctc'           => null,
            ];
        }

        return [
            'success'        => true,
            'employee_code'  => $employeeCode,
            'employee_id'    => (int) $employee['id'],
            'employee_name'  => $employee['name'],
            'ctc'            => (float) $salaryAnnexure['annual_ctc'],
            'effective_from' => $salaryAnnexure['effective_from'],
        ];
    }

    private function calculateUsingGlobalCap(array $employeeData, int $cycleId): array
    {
        $employee = $this->users->getEmployeeRoleContextByCode(
            $employeeData['employee_code']
        );

        if (!$employee) {
            return [
                'success' => false,
                'calculation_rule' => 'global_cap',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => null,
                'message' => 'Employee not found in the appraisal system.',
            ];
        }

        $appraisalEmployeeId = (int) $employee['id'];

        $scoreData = $this->calculateReviewScore(
            $employee,
            $cycleId
        );

        if ($scoreData['success'] !== true) {
            return [
                'success' => false,
                'calculation_rule' => 'global_cap',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => $scoreData['message'],
            ];
        }

        $globalCapPercentage = $this->settings->getGlobalFloat(
            'maximum_increment_percentage',
            0
        );

        if ($globalCapPercentage <= 0) {
            return [
                'success' => false,
                'calculation_rule' => 'global_cap',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => 'Maximum increment percentage is not configured.',
            ];
        }

        if ($globalCapPercentage > 100) {
            return [
                'success' => false,
                'calculation_rule' => 'global_cap',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => 'Maximum increment percentage cannot exceed 100%.',
            ];
        }

        $calculation = $this->calculateFinalIncrement(
            (float) $employeeData['ctc'],
            $scoreData['final_score'],
            $globalCapPercentage
        );

        $saved = $this->finalScores->saveCalculation(
            $cycleId,
            $appraisalEmployeeId,
            $scoreData['self_score'],
            $scoreData['matrix_score'],
            $scoreData['final_score']
        );

        if (!$saved) {
            return [
                'success' => false,
                'calculation_rule' => 'global_cap',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => 'Final score was calculated but could not be saved.',
            ];
        }

        return [
            'success' => true,
            'calculation_rule' => 'global_cap',
            'employee_code' => $employeeData['employee_code'],
            'employee_id' => $appraisalEmployeeId,
            'employee_name' => $employeeData['employee_name'],
            'current_ctc' => $calculation['current_ctc'],
            'self_score' => $scoreData['self_score'],
            'matrix_score' => $scoreData['matrix_score'],
            'final_score' => $scoreData['final_score'],
            'review_count' => $scoreData['review_count'],
            'use_reviewer_weightage' => $scoreData['use_reviewer_weightage'],
            'equal_weightage' => $scoreData['equal_weightage'],
            'global_cap_percentage' => $globalCapPercentage,
            'effective_increment_percentage' => $calculation['effective_increment_percentage'],
            'increment_amount' => $calculation['increment_amount'],
            'revised_ctc' => $calculation['revised_ctc'],
            'ctc_effective_from' => $employeeData['effective_from'],
            'review_details' => $scoreData['review_details'],
        ];
    }

    private function calculateUsingCtcSlab(array $employeeData, int $cycleId): array
    {
        $employee = $this->users->getEmployeeRoleContextByCode(
            $employeeData['employee_code']
        );

        if (!$employee) {
            return [
                'success' => false,
                'calculation_rule' => 'ctc_slab',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => null,
                'message' => 'Employee not found in the appraisal system.',
            ];
        }

        $appraisalEmployeeId = (int) $employee['id'];

        $scoreData = $this->calculateReviewScore(
            $employee,
            $cycleId
        );

        if ($scoreData['success'] !== true) {
            return [
                'success' => false,
                'calculation_rule' => 'ctc_slab',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => $scoreData['message'],
            ];
        }

        $ctcRules = $this->getCtcRangeIncrementRules();

        if (empty($ctcRules)) {
            return [
                'success' => false,
                'calculation_rule' => 'ctc_slab',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => 'No CTC range-based increment rules are configured.',
            ];
        }

        $currentCtc = (float) $employeeData['ctc'];
        $applicableRule = null;

        foreach ($ctcRules as $rule) {
            $ctcFrom = (float) ($rule['ctc_from'] ?? 0);
            $ctcTo = (float) ($rule['ctc_to'] ?? 0);

            if ($currentCtc >= $ctcFrom && $currentCtc < $ctcTo) {
                $applicableRule = $rule;
                break;
            }
        }

        if ($applicableRule === null) {
            return [
                'success' => false,
                'calculation_rule' => 'ctc_slab',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'current_ctc' => $currentCtc,
                'message' => 'No CTC range-based increment rule applies to the employee\'s current CTC.',
            ];
        }

        $maximumIncrementPercentage = (float) (
            $applicableRule['maximum_percentage'] ?? 0
        );

        if ($maximumIncrementPercentage <= 0) {
            return [
                'success' => false,
                'calculation_rule' => 'ctc_slab',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => 'The applicable CTC range does not have a valid increment percentage.',
            ];
        }

        if ($maximumIncrementPercentage > 100) {
            return [
                'success' => false,
                'calculation_rule' => 'ctc_slab',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => 'The applicable CTC range increment percentage cannot exceed 100%.',
            ];
        }

        $calculation = $this->calculateFinalIncrement(
            $currentCtc,
            $scoreData['final_score'],
            $maximumIncrementPercentage
        );

        $saved = $this->finalScores->saveCalculation(
            $cycleId,
            $appraisalEmployeeId,
            $scoreData['self_score'],
            $scoreData['matrix_score'],
            $scoreData['final_score']
        );

        if (!$saved) {
            return [
                'success' => false,
                'calculation_rule' => 'ctc_slab',
                'employee_code' => $employeeData['employee_code'],
                'employee_id' => $appraisalEmployeeId,
                'message' => 'Final score was calculated but could not be saved.',
            ];
        }

        return [
            'success' => true,
            'calculation_rule' => 'ctc_slab',
            'employee_code' => $employeeData['employee_code'],
            'employee_id' => $appraisalEmployeeId,
            'employee_name' => $employeeData['employee_name'],
            'current_ctc' => $currentCtc,
            'self_score' => $scoreData['self_score'],
            'matrix_score' => $scoreData['matrix_score'],
            'final_score' => $scoreData['final_score'],
            'review_count' => $scoreData['review_count'],
            'use_reviewer_weightage' => $scoreData['use_reviewer_weightage'],
            'equal_weightage' => $scoreData['equal_weightage'],
            'ctc_slab_from' => (float) $applicableRule['ctc_from'],
            'ctc_slab_to' => (float) $applicableRule['ctc_to'],
            'ctc_slab_maximum_percentage' => $maximumIncrementPercentage,
            'effective_increment_percentage' => $calculation['effective_increment_percentage'],
            'increment_amount' => $calculation['increment_amount'],
            'revised_ctc' => $calculation['revised_ctc'],
            'ctc_effective_from' => $employeeData['effective_from'],
            'review_details' => $scoreData['review_details'],
        ];
    }

    private function getCtcRangeIncrementRules(): array
    {
        $row = $this->settings
            ->where('scope_type', 'global')
            ->where('scope_id', 0)
            ->where('setting_key', 'ctc_range_increment_rules')
            ->first();

        if (!$row) {
            return [];
        }

        $value = $row['setting_value'] ?? '';

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        try {
            $rules = json_decode(
                $value,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (Throwable) {
            return [];
        }

        return is_array($rules)
            ? array_values($rules)
            : [];
    }
}
