<?php

namespace App\Services;

use App\Models\AppraisalCycleModel;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AppraisalCycleService
{
    protected AppraisalCycleModel $cycles;

    public function __construct()
    {
        $this->cycles = new AppraisalCycleModel();
    }

    public function getCycles(
        array $filters = []
    ): array {

        $page = max(
            1,
            (int) ($filters['page'] ?? 1)
        );

        $pageSize =
            (int) ($filters['pageSize'] ?? 10);

        if (
            !in_array(
                $pageSize,
                [10, 25, 50, 100],
                true
            )
        ) {
            $pageSize = 10;
        }

        return $this->cycles->getCycles(
            $page,
            $pageSize,
            trim($filters['search'] ?? ''),
            trim($filters['status'] ?? ''),
            trim($filters['orderBy'] ?? 'id'),
            trim($filters['direction'] ?? 'desc')
        );
    }

    public function getCycle(
        int $id
    ): ?array {

        return $this->cycles->getCycle(
            $id
        );
    }

    public function createCycle(
        array $data
    ): int {

        $errors =
            $this->validateCycleData(
                $data
            );

        if (!empty($errors)) {
            throw new InvalidArgumentException(
                json_encode($errors)
            );
        }

        $cycleCode =
            trim(
                $data['cycle_code'] ?? ''
            );

        if (
            $cycleCode !== '' &&
            $this->cycles->cycleCodeExists(
                $cycleCode
            )
        ) {

            throw new InvalidArgumentException(
                json_encode([
                    'cycle_code' =>
                    'This cycle code is already in use.'
                ])
            );
        }

        $this->validateOrganization(
            (int) $data['organization_id']
        );

        $cycleData =
            $this->buildCycleData(
                $data
            );

        $cycleId =
            $this->cycles->insert(
                $cycleData,
                true
            );

        if (!$cycleId) {
            throw new RuntimeException(
                'Unable to create appraisal cycle.'
            );
        }

        return (int) $cycleId;
    }

    public function updateCycle(
        int $id,
        array $data
    ): void {

        $cycle =
            $this->cycles->find($id);

        if (!$cycle) {
            throw new RuntimeException(
                'Appraisal cycle not found.'
            );
        }

        $errors =
            $this->validateCycleData(
                $data,
                $id
            );

        if (!empty($errors)) {
            throw new InvalidArgumentException(
                json_encode($errors)
            );
        }

        $cycleCode =
            trim(
                $data['cycle_code'] ?? ''
            );

        if (
            $cycleCode !== '' &&
            $this->cycles->cycleCodeExists(
                $cycleCode,
                $id
            )
        ) {

            throw new InvalidArgumentException(
                json_encode([
                    'cycle_code' =>
                    'This cycle code is already in use.'
                ])
            );
        }

        $this->validateOrganization(
            (int) $data['organization_id']
        );

        $cycleData =
            $this->buildCycleData(
                $data,
                true
            );

        $updated =
            $this->cycles->update(
                $id,
                $cycleData
            );

        if (!$updated) {
            throw new RuntimeException(
                'Unable to update appraisal cycle.'
            );
        }
    }

    public function deleteCycle(
        int $id
    ): void {

        $cycle =
            $this->cycles->find($id);

        if (!$cycle) {
            throw new RuntimeException(
                'Appraisal cycle not found.'
            );
        }

        /*
         * For now direct deletion is allowed.
         *
         * Later, when appraisal instances,
         * participants, reviews, etc. are linked
         * to a cycle, we will block deletion
         * when dependent records exist.
         */

        if (
            !$this->cycles->delete($id)
        ) {
            throw new RuntimeException(
                'Unable to delete appraisal cycle.'
            );
        }
    }

    protected function buildCycleData(
        array $data,
        bool $isUpdate = false
    ): array {

        $cycleData = [

            'organization_id' =>
            (int) $data['organization_id'],

            'cycle_name' =>
            trim(
                $data['cycle_name']
            ),

            'cycle_code' =>
            !empty($data['cycle_code'])
                ? trim(
                    $data['cycle_code']
                )
                : null,

            'description' =>
            !empty($data['description'])
                ? trim(
                    $data['description']
                )
                : null,

            'start_date' =>
            $data['start_date'],

            'end_date' =>
            $data['end_date'],

            'status' =>
            $data['status'] ?? 'draft',
        ];

        /*
         * created_by must only be assigned
         * during creation.
         */
        if (!$isUpdate) {

            $cycleData['created_by'] =
                (int) session()->get(
                    'user_id'
                );
        }

        return $cycleData;
    }

    protected function validateCycleData(
        array $data,
        ?int $cycleId = null
    ): array {

        $errors = [];

        if (
            empty($data['organization_id']) ||
            !is_numeric(
                $data['organization_id']
            )
        ) {

            $errors['organization_id'] =
                'Organization is required.';
        }

        if (
            empty($data['cycle_name']) ||
            trim(
                $data['cycle_name']
            ) === ''
        ) {

            $errors['cycle_name'] =
                'Cycle name is required.';
        }

        if (
            !empty($data['cycle_name']) &&
            strlen(
                trim(
                    $data['cycle_name']
                )
            ) > 150
        ) {

            $errors['cycle_name'] =
                'Cycle name cannot exceed 150 characters.';
        }

        if (
            !empty($data['cycle_code']) &&
            strlen(
                trim(
                    $data['cycle_code']
                )
            ) > 50
        ) {

            $errors['cycle_code'] =
                'Cycle code cannot exceed 50 characters.';
        }

        if (
            empty($data['start_date'])
        ) {

            $errors['start_date'] =
                'Start date is required.';
        } elseif (
            !strtotime(
                $data['start_date']
            )
        ) {

            $errors['start_date'] =
                'Invalid start date.';
        }

        if (
            empty($data['end_date'])
        ) {

            $errors['end_date'] =
                'End date is required.';
        } elseif (
            !strtotime(
                $data['end_date']
            )
        ) {

            $errors['end_date'] =
                'Invalid end date.';
        }

        if (
            !empty($data['start_date']) &&
            !empty($data['end_date']) &&
            strtotime(
                $data['end_date']
            ) <
            strtotime(
                $data['start_date']
            )
        ) {

            $errors['end_date'] =
                'End date cannot be earlier than start date.';
        }

        $allowedStatuses = [
            'draft',
            'active',
            'completed',
            'closed'
        ];

        if (
            !empty($data['status']) &&
            !in_array(
                $data['status'],
                $allowedStatuses,
                true
            )
        ) {

            $errors['status'] =
                'Invalid appraisal cycle status.';
        }

        return $errors;
    }

    protected function validateOrganization(
        int $organizationId
    ): void {

        if ($organizationId <= 0) {

            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Organization is required.'
                ])
            );
        }

        $organization =
            db_connect()
            ->table('organizations')
            ->select([
                'id',
                'status'
            ])
            ->where(
                'id',
                $organizationId
            )
            ->get()
            ->getRowArray();

        if (!$organization) {

            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Selected organization is invalid.'
                ])
            );
        }

        if (
            ($organization['status'] ?? null)
            !== 'active'
        ) {

            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Selected organization is inactive.'
                ])
            );
        }
    }
}
