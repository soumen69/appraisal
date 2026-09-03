<?php

namespace App\Services;

use App\Models\AppraisalCycleModel;
use App\Models\AppraisalCycleParticipantModel;
use App\Models\AppraisalTemplateModel;

use InvalidArgumentException;
use RuntimeException;

class AppraisalCycleParticipantService
{
    protected AppraisalCycleParticipantModel $participants;

    protected AppraisalCycleModel $cycles;

    public function __construct()
    {
        $this->participants = new AppraisalCycleParticipantModel();
        $this->cycles = new AppraisalCycleModel();
    }


    public function getParticipants(int $cycleId): array
    {
        $this->validateCycle($cycleId);
        return $this->participants->getParticipants($cycleId);
    }

    public function getAvailableEmployees(int $cycleId): array
    {

        $cycle =
            $this->validateCycle(
                $cycleId
            );

        return $this
            ->participants
            ->getAvailableEmployees(
                $cycleId,
                (int) $cycle['organization_id']
            );
    }

    public function addParticipant(int $cycleId, array $data): int
    {
        $cycle = $this->validateCycle($cycleId);

        $employeeId = (int) ($data['employee_id'] ?? 0);

        if ($employeeId <= 0) {
            throw new InvalidArgumentException('Employee is required.');
        }

        $this->validateEmployee($employeeId, (int) $cycle['organization_id']);

        if ($this->participants->participantExists($cycleId, $employeeId)) {
            throw new InvalidArgumentException('This employee is already added to the appraisal cycle.');
        }

        $participantId = $this->participants->insert([
            'appraisal_cycle_id' => $cycleId,
            'employee_id' => $employeeId,
            'status' => 'active'
        ], true);

        if (!$participantId) {
            throw new RuntimeException('Unable to add participant.');
        }

        return (int) $participantId;
    }

    public function addParticipantsBulk(int $cycleId, array $employeeIds): array
    {
        $cycle = $this->validateCycle($cycleId);

        if (empty($employeeIds)) {
            throw new InvalidArgumentException('Please select at least one employee.');
        }

        $inserted = [];
        $skipped = [];

        foreach ($employeeIds as $employeeId) {
            $employeeId = (int) $employeeId;

            if ($employeeId <= 0) {
                continue;
            }

            try {
                $this->validateEmployee($employeeId, (int) $cycle['organization_id']);

                if ($this->participants->participantExists($cycleId, $employeeId)) {
                    $skipped[] = $employeeId;
                    continue;
                }

                $id = $this->participants->insert([
                    'appraisal_cycle_id' => $cycleId,
                    'employee_id' => $employeeId,
                    'status' => 'active'
                ], true);

                if ($id) {
                    $inserted[] = (int) $id;
                }
            } catch (\Throwable $e) {
                $skipped[] = $employeeId;
            }
        }

        return [
            'inserted' => count($inserted),
            'skipped' => count($skipped),
            'participant_ids' => $inserted
        ];
    }

    public function updateParticipant(int $participantId, array $data): void
    {
        $participant = $this->participants->find($participantId);

        if (!$participant) {
            throw new RuntimeException('Participant not found.');
        }

        $updateData = [];

        if (array_key_exists('status', $data)) {
            if (!in_array($data['status'], ['active', 'excluded'], true)) {
                throw new InvalidArgumentException('Invalid participant status.');
            }

            $updateData['status'] = $data['status'];
        }

        if (empty($updateData)) {
            throw new InvalidArgumentException('Nothing to update.');
        }

        if (!$this->participants->update($participantId, $updateData)) {
            throw new RuntimeException('Unable to update participant.');
        }
    }

    public function removeParticipant(int $participantId): void
    {

        $participant =
            $this
            ->participants
            ->find(
                $participantId
            );


        if (
            !$participant
        ) {

            throw new RuntimeException(
                'Participant not found.'
            );
        }


        if (
            !$this
                ->participants
                ->delete(
                    $participantId
                )
        ) {

            throw new RuntimeException(
                'Unable to remove participant.'
            );
        }
    }

    protected function validateCycle(int $cycleId): array
    {

        $cycle =
            $this
            ->cycles
            ->find(
                $cycleId
            );


        if (
            !$cycle
        ) {

            throw new RuntimeException(
                'Appraisal cycle not found.'
            );
        }


        return
            $cycle;
    }

    protected function validateEmployee(int $employeeId, int $organizationId): void
    {

        $employee =
            db_connect()
            ->table('users')

            ->select([
                'id',
                'organization_id'
            ])

            ->where(
                'id',
                $employeeId
            )

            ->where(
                'organization_id',
                $organizationId
            )

            ->get()

            ->getRowArray();


        if (
            !$employee
        ) {

            throw new InvalidArgumentException(
                'Selected employee does not belong to this organization.'
            );
        }
    }
}
