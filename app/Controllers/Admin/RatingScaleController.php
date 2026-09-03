<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RatingScaleModel;
use App\Models\RatingScaleValueModel;
use Throwable;

class RatingScaleController extends BaseController
{
    protected RatingScaleModel $ratingScaleModel;
    protected RatingScaleValueModel $ratingScaleValueModel;

    public function __construct()
    {
        $this->ratingScaleModel = new RatingScaleModel();
        $this->ratingScaleValueModel = new RatingScaleValueModel();
    }

    public function index()
    {
        return view('appraisal/rating-scales/index', [
            'title' => 'Rating Scales',
            'page_title' => 'Rating Scales',
            'page_subtitle' => 'Create and manage performance rating scales.'
        ]);
    }

    public function list()
    {
        try {
            $organizationId = (int) session()->get('organization_id');

            $scales = $this->ratingScaleModel
                ->where('organization_id', $organizationId)
                ->orderBy('id', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $scales
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Rating scale list error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load rating scales.'
                ]);
        }
    }

    public function store()
    {
        try {
            $organizationId = (int) session()->get('organization_id');

            $rules = [
                'scale_name' => 'required|max_length[100]',
                'min_value' => 'required|integer',
                'max_value' => 'required|integer'
            ];

            if (!$this->validate($rules)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Please correct the highlighted fields.',
                        'errors' => $this->validator->getErrors()
                    ]);
            }

            $minValue = (int) $this->request->getPost('min_value');
            $maxValue = (int) $this->request->getPost('max_value');

            if ($minValue >= $maxValue) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Maximum value must be greater than minimum value.'
                    ]);
            }

            $id = $this->ratingScaleModel->insert([
                'organization_id' => $organizationId,
                'scale_name' => trim($this->request->getPost('scale_name')),
                'min_value' => $minValue,
                'max_value' => $maxValue
            ], true);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rating scale created successfully.',
                'data' => ['id' => $id]
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Rating scale create error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to create rating scale.'
                ]);
        }
    }

    public function edit(int $id)
    {
        try {
            $organizationId = (int) session()->get('organization_id');

            $scale = $this->ratingScaleModel
                ->where('id', $id)
                ->where('organization_id', $organizationId)
                ->first();

            if (!$scale) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Rating scale not found.'
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $scale
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Rating scale edit error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to load rating scale.'
                ]);
        }
    }

    public function update(int $id)
    {
        try {
            $organizationId = (int) session()->get('organization_id');

            $scale = $this->ratingScaleModel
                ->where('id', $id)
                ->where('organization_id', $organizationId)
                ->first();

            if (!$scale) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Rating scale not found.'
                    ]);
            }

            $rules = [
                'scale_name' => 'required|max_length[100]',
                'min_value' => 'required|integer',
                'max_value' => 'required|integer'
            ];

            if (!$this->validate($rules)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Please correct the highlighted fields.',
                        'errors' => $this->validator->getErrors()
                    ]);
            }

            $minValue = (int) $this->request->getPost('min_value');
            $maxValue = (int) $this->request->getPost('max_value');

            if ($minValue >= $maxValue) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Maximum value must be greater than minimum value.'
                    ]);
            }

            $this->ratingScaleModel->update($id, [
                'scale_name' => trim($this->request->getPost('scale_name')),
                'min_value' => $minValue,
                'max_value' => $maxValue
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rating scale updated successfully.'
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Rating scale update error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to update rating scale.'
                ]);
        }
    }

    public function delete(int $id)
    {
        try {
            $organizationId = (int) session()->get('organization_id');

            $scale = $this->ratingScaleModel
                ->where('id', $id)
                ->where('organization_id', $organizationId)
                ->first();

            if (!$scale) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Rating scale not found.'
                    ]);
            }

            $this->ratingScaleModel->delete($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Rating scale deleted successfully.'
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Rating scale delete error: ' . $e->getMessage());

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Unable to delete rating scale.'
                ]);
        }
    }
}
