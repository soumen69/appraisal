<?php

namespace App\Controllers\Admin;

use App\Services\AppraisalCycleTemplateAssignmentService;
use App\Controllers\BaseController;
use InvalidArgumentException;
use Throwable;

class AppraisalCycleTemplateAssignmentController extends BaseController
{
    protected AppraisalCycleTemplateAssignmentService
        $assignmentService;


    public function __construct()
    {
        $this->assignmentService =
            new AppraisalCycleTemplateAssignmentService();
    }


    public function index(int $cycleId)
    {
        return view(
            'appraisal/cycles/template_assignments',
            [
                'title' =>
                'Template Assignment',

                'cycleId' =>
                $cycleId
            ]
        );
    }


    public function list(
        int $cycleId
    ) {

        try {

            $assignments =
                $this
                ->assignmentService
                ->getAssignments(
                    $cycleId
                );


            return $this
                ->response
                ->setJSON([
                    'success' =>
                    true,

                    'data' =>
                    $assignments
                ]);
        } catch (
            Throwable $e
        ) {

            return $this
                ->response
                ->setStatusCode(
                    500
                )
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    $e->getMessage()
                ]);
        }
    }


    public function store(
        int $cycleId
    ) {

        try {

            $assignmentId =
                $this
                ->assignmentService
                ->createAssignment(
                    $cycleId,
                    $this
                        ->request
                        ->getPost()
                );


            return $this
                ->response
                ->setJSON([
                    'success' =>
                    true,

                    'message' =>
                    'Template assignment created successfully.',

                    'data' => [
                        'id' =>
                        $assignmentId
                    ]
                ]);
        } catch (
            InvalidArgumentException $e
        ) {

            return $this
                ->response
                ->setStatusCode(
                    422
                )
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    $e->getMessage()
                ]);
        } catch (
            Throwable $e
        ) {

            log_message(
                'error',
                'Template assignment create error: '
                    . $e->getMessage()
            );


            return $this
                ->response
                ->setStatusCode(
                    500
                )
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    'Unable to create template assignment.'
                ]);
        }
    }


    public function update(
        int $assignmentId
    ) {

        try {

            $this
                ->assignmentService
                ->updateAssignment(
                    $assignmentId,
                    $this
                        ->request
                        ->getPost()
                );


            return $this
                ->response
                ->setJSON([
                    'success' =>
                    true,

                    'message' =>
                    'Template assignment updated successfully.'
                ]);
        } catch (
            InvalidArgumentException $e
        ) {

            return $this
                ->response
                ->setStatusCode(
                    422
                )
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    $e->getMessage()
                ]);
        } catch (
            Throwable $e
        ) {

            return $this
                ->response
                ->setStatusCode(
                    500
                )
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    'Unable to update template assignment.'
                ]);
        }
    }


    public function delete(
        int $assignmentId
    ) {

        try {

            $this
                ->assignmentService
                ->deleteAssignment(
                    $assignmentId
                );


            return $this
                ->response
                ->setJSON([
                    'success' =>
                    true,

                    'message' =>
                    'Template assignment removed successfully.'
                ]);
        } catch (
            Throwable $e
        ) {

            return $this
                ->response
                ->setStatusCode(
                    500
                )
                ->setJSON([
                    'success' =>
                    false,

                    'message' =>
                    'Unable to remove template assignment.'
                ]);
        }
    }

    public function options(int $cycleId)
    {
        try {

            $options =
                $this
                ->assignmentService
                ->getAssignmentOptions(
                    $cycleId
                );

            return $this
                ->response
                ->setJSON([
                    'success' => true,
                    'data' => $options
                ]);
        } catch (Throwable $e) {

            return $this
                ->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
        }
    }
}
