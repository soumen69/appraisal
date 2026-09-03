<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AppraisalCycleService;
use CodeIgniter\Exceptions\PageNotFoundException;
use InvalidArgumentException;
use Throwable;

class AppraisalCycleController extends BaseController
{
    protected AppraisalCycleService $appraisalCycleService;

    public function __construct()
    {
        $this->appraisalCycleService =
            new AppraisalCycleService();
    }

    /*
     * Appraisal Cycle Listing Page
     */
    public function index()
    {
        return view(
            'appraisal/cycles/index',
            [
                'title' => 'Appraisal Cycles',
                'page_title' => 'Appraisal Cycles',
                'page_subtitle' =>
                'Manage appraisal cycles and review periods.'
            ]
        );
    }

    /*
     * Cycle List
     */
    public function list()
    {
        try {

            $data =
                $this->appraisalCycleService
                ->getCycles(
                    $this->request->getGet()
                );

            return $this->response
                ->setJSON([
                    'success' => true,
                    'data' => $data
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal cycle list error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load appraisal cycles.'
                ]);
        }
    }

    /*
     * Get Single Cycle
     *
     * Used by reusable CRUD edit/view functionality.
     */
    public function edit(int $id)
    {
        try {

            $cycle =
                $this->appraisalCycleService
                ->getCycle($id);

            if (!$cycle) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Appraisal cycle not found.'
                    ]);
            }

            return $this->response
                ->setJSON([
                    'success' => true,
                    'data' => $cycle
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal cycle details error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load appraisal cycle.'
                ]);
        }
    }

    /*
     * Create Cycle
     */
    public function store()
    {
        try {

            $id =
                $this->appraisalCycleService
                ->createCycle(
                    $this->request->getPost()
                );

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Appraisal cycle created successfully.',
                    'data' => [
                        'id' => $id
                    ]
                ]);
        } catch (InvalidArgumentException $e) {

            $errors =
                json_decode(
                    $e->getMessage(),
                    true
                );

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Please correct the highlighted fields.',
                    'errors' =>
                    is_array($errors)
                        ? $errors
                        : []
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal cycle create error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to create appraisal cycle.'
                ]);
        }
    }

    /*
     * Update Cycle
     */
    public function update(int $id)
    {
        try {

            $this->appraisalCycleService
                ->updateCycle(
                    $id,
                    $this->request->getPost()
                );

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Appraisal cycle updated successfully.'
                ]);
        } catch (InvalidArgumentException $e) {

            $errors =
                json_decode(
                    $e->getMessage(),
                    true
                );

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Please correct the highlighted fields.',
                    'errors' =>
                    is_array($errors)
                        ? $errors
                        : []
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal cycle update error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to update appraisal cycle.'
                ]);
        }
    }

    /*
     * Delete Cycle
     */
    public function delete(int $id)
    {
        try {

            $this->appraisalCycleService
                ->deleteCycle($id);

            return $this->response
                ->setJSON([
                    'success' => true,
                    'message' =>
                    'Appraisal cycle deleted successfully.'
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal cycle delete error: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    $e->getMessage()
                ]);
        }
    }

    /*
     * Cycle Details
     *
     * Used by CrudView / drawer.
     */
    public function details(int $id)
    {
        try {

            $cycle =
                $this->appraisalCycleService
                ->getCycle($id);

            if (!$cycle) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'success' => false,
                        'message' =>
                        'Appraisal cycle not found.'
                    ]);
            }

            return $this->response
                ->setJSON([
                    'success' => true,
                    'data' => $cycle
                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal cycle details failed: ' .
                    $e->getMessage()
            );

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' =>
                    'Unable to load appraisal cycle details.'
                ]);
        }
    }
}
