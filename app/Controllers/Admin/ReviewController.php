<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReviewModel;
use InvalidArgumentException;
use Throwable;

class ReviewController extends BaseController
{
    protected ReviewModel $reviews;

    public function __construct()
    {
        $this->reviews = new ReviewModel();
    }

    public function index()
    {
        return view('reviews/index', [
            'title' => 'Reviews',
            'page_title' => 'Appraisal Reviews',
            'page_subtitle' => 'Monitor and review all appraisal activity.',
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
}
