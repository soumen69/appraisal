<?php

namespace App\Controllers;

use App\Services\MyReviewService;

class MyReviewController extends BaseController
{
    protected MyReviewService $myReviewService;

    public function __construct()
    {
        $this->myReviewService = new MyReviewService();
    }

    public function index()
    {
        return view('appraisal/my_reviews/index');
    }

    public function list()
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $this->myReviewService->getMyReviews($userId)
        ]);
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
        return view('appraisal/my_reviews/review', [
            'reviewId' => (int) $reviewId
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
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Review submission is not implemented yet.'
        ]);
    }
}
