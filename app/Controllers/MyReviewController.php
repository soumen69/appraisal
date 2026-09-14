<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\MyReviewService;
use Throwable;

class MyReviewController extends BaseController
{
    protected MyReviewService $myReviewService;

    public function __construct()
    {
        $this->myReviewService = new MyReviewService();
    }

    public function index()
    {
        return view('appraisal/my_reviews/index', [
            'title' => 'My Reviews',
            'page_title' => 'My Reviews',
            'page_subtitle' => 'View and complete your assigned appraisal reviews.'
        ]);
    }

    public function list()
    {
        try {
            $userId = (int) session()->get('user_id');

            if ($userId <= 0) {
                return $this->response
                    ->setStatusCode(401)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Unauthorized access.'
                    ]);
            }

            $data = $this->myReviewService->getMyReviews($userId);

            return $this->response
                ->setJSON([
                    'success' => true,
                    'data' => $data
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'My reviews list error: ' . $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load reviews.'
                ]);
        }
    }

    public function start($cycleId)
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ]);
        }

        return $this->response->setJSON(
            $this->myReviewService->startReview((int) $cycleId, $userId)
        );
    }

    public function review($reviewId)
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to(base_url('login'));
        }

        return view('appraisal/my_reviews/review', [
            'reviewId' => (int) $reviewId,
            'title' => 'My Reviews',
            'page_title' => 'My Reviews',
            'page_subtitle' => 'View appraisal review'
        ]);
    }

    public function reviewData($reviewId)
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ]);
        }

        return $this->response->setJSON(
            $this->myReviewService->getReviewData((int) $reviewId, $userId)
        );
    }

    public function saveDraft($reviewId)
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ]);
        }

        $payload = $this->request->getJSON(true);

        if (!is_array($payload)) {
            $payload = $this->request->getPost() ?: [];
        }

        return $this->response->setJSON(
            $this->myReviewService->saveDraft((int) $reviewId, $userId, $payload)
        );
    }

    public function submit($reviewId)
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ]);
        }

        $payload = $this->request->getJSON(true);

        if (!is_array($payload)) {
            $payload = $this->request->getPost() ?: [];
        }

        return $this->response->setJSON(
            $this->myReviewService->submitReview(
                (int) $reviewId,
                $userId,
                $payload
            )
        );
    }
}
