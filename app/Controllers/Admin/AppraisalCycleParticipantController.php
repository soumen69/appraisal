<?php

namespace App\Controllers\Admin;

use App\Services\AppraisalCycleParticipantService;
use App\Controllers\BaseController;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AppraisalCycleParticipantController extends BaseController
{
    protected AppraisalCycleParticipantService $participantService;


    public function __construct()
    {
        $this->participantService =
            new AppraisalCycleParticipantService();
    }


    public function index(
        int $cycleId
    ) {
        return view(
            'appraisal/cycles/participants',
            [
                'title' =>
                'Cycle Participants',

                'cycleId' =>
                $cycleId
            ]
        );
    }


    public function list(int $cycleId)
    {
        try {

            $participants = $this
                ->participantService
                ->getParticipants($cycleId);

            return $this
                ->response
                ->setJSON([
                    'success' => true,

                    'data' => [
                        'data' => $participants,

                        'total' => count($participants),

                        'page' => 1,

                        'lastPage' => 1
                    ]
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


    public function availableEmployees(
        int $cycleId
    ) {
        try {

            $employees =
                $this
                ->participantService
                ->getAvailableEmployees(
                    $cycleId
                );


            return $this
                ->response
                ->setJSON([
                    'success' =>
                    true,

                    'data' =>
                    $employees
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

            $participantId =
                $this
                ->participantService
                ->addParticipant(
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
                    'Participant added successfully.',

                    'data' => [
                        'id' =>
                        $participantId
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
                'Participant create error: '
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
                    'Unable to add participant.'
                ]);
        }
    }


    public function bulkStore(int $cycleId)
    {
        try {
            $employeeIds = $this->request->getPost('employee_ids') ?? [];

            if (!is_array($employeeIds)) {
                $employeeIds = [];
            }

            $result = $this->participantService->addParticipantsBulk($cycleId, $employeeIds);

            return $this->response->setJSON([
                'success' => true,
                'message' => $result['inserted'] . ' participant(s) added successfully.',
                'data' => $result
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            log_message('error', 'Bulk participant create error: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Unable to add participants.'
            ]);
        }
    }


    public function update(
        int $participantId
    ) {
        try {

            $this
                ->participantService
                ->updateParticipant(
                    $participantId,
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
                    'Participant updated successfully.'
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
                    'Unable to update participant.'
                ]);
        }
    }


    public function delete(
        int $participantId
    ) {
        try {

            $this
                ->participantService
                ->removeParticipant(
                    $participantId
                );


            return $this
                ->response
                ->setJSON([
                    'success' =>
                    true,

                    'message' =>
                    'Participant removed successfully.'
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
}
