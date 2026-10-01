<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AppSettingModel;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use RuntimeException;
use Throwable;

class SettingsController extends BaseController
{
    protected AppSettingModel $settings;

    private array $allowedKeys = [
        'email_notifications',
        'appraisal_reminders',
        'reminder_frequency',
        'overdue_reminders',
        'overdue_reminder_frequency',
        'minimum_service_period',
        'probation_eligibility',
        'maximum_increment_percentage',
        'use_reviewer_weightage',
        'ctc_range_increment_rules',
        'exceptional_increment_approval',
        'appraisal_deadline_grace_period',
    ];

    public function __construct()
    {
        $this->settings = new AppSettingModel();
    }

    /**
     * Display the Settings page.
     */
    public function index()
    {
        return view('settings/index', [
            'title'         => 'Settings',
            'page_title'    => 'Settings',
            'page_subtitle' => 'Configure application notifications and appraisal policies.',
        ]);
    }

    public function getSettings(): ResponseInterface
    {  
        $input = $this->request->getJSON(true) ?? [];

        if (!is_array($input) || empty($input)) {
            $input = $this->request->getPost() ?? [];
        }

        $scopeType = $input['scope_type'] ?? 'global';
        $scopeId   = (int) ($input['scope_id'] ?? 0);

        if ($scopeType !== 'global' || $scopeId !== 0) {
            return $this->errorResponse(
                'Only global settings are currently supported.',
                400
            );
        }

        $rows = $this->settings
            ->where('scope_type', 'global')
            ->where('scope_id', 0)
            ->findAll();

        $data = [];

        foreach ($rows as $row) {
            if (in_array($row['setting_key'], $this->allowedKeys, true)) {
                $data[$row['setting_key']] = $row['setting_value'];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Settings loaded successfully.',
            'settings' => $data,
            'data' => $data,
            'errors' => [],
        ]);
    }

    public function saveSettings(): ResponseInterface
    {
        $input = $this->request->getJSON(true) ?? [];

        if (!is_array($input)) {
            return $this->errorResponse(
                'Invalid request payload.',
                400
            );
        }

        $scopeType = $input['scope_type'] ?? 'global';
        $scopeId   = (int) ($input['scope_id'] ?? 0);
        $settings  = $input['settings'] ?? null;

        if ($scopeType !== 'global' || $scopeId !== 0) {
            return $this->errorResponse(
                'Only global settings are currently supported.',
                400
            );
        }

        if (!is_array($settings) || empty($settings)) {
            return $this->errorResponse(
                'No settings were submitted.',
                400
            );
        }

        $validatedSettings = [];

        foreach ($settings as $key => $value) {
            if (
                !is_string($key) ||
                !in_array($key, $this->allowedKeys, true)
            ) {
                return $this->errorResponse(
                    'An unsupported setting was submitted.',
                    400
                );
            }

            $validated = $this->validateSetting($key, $value);

            if ($validated['valid'] !== true) {
                return $this->errorResponse(
                    $validated['message'],
                    400
                );
            }

            $validatedSettings[$key] = $validated['value'];
        }

        $db = Database::connect();
        $db->transBegin();

        try {
            foreach ($validatedSettings as $key => $value) {
                if (!$this->settings->saveSetting(
                    $key,
                    $value,
                    'global',
                    0
                )) {
                    throw new RuntimeException(
                        "Failed to save setting: {$key}"
                    );
                }
            }

            if ($db->transStatus() === false) {
                throw new RuntimeException(
                    'The settings transaction failed.'
                );
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Settings saved successfully.',
                'data' => null,
                'errors' => [],
                'csrfHash' => csrf_hash(),
            ]);
        } catch (Throwable $e) {
            $db->transRollback();

            log_message(
                'error',
                'Settings save failed: ' . $e->getMessage()
            );

            return $this->errorResponse(
                'Unable to save settings. Please try again.',
                500
            );
        }
    }

    private function validateSetting(string $key, mixed $value): array
    {
        $booleanKeys = [
            'email_notifications',
            'appraisal_reminders',
            'overdue_reminders',
            'probation_eligibility',
            'use_reviewer_weightage',
            'exceptional_increment_approval',
        ];

        if (in_array($key, $booleanKeys, true)) {
            if (!in_array((string) $value, ['0', '1'], true)) {
                return $this->invalidSetting($key);
            }

            return $this->validSetting((string) $value);
        }

        $integerRanges = [
            'reminder_frequency' => [1, 365],
            'overdue_reminder_frequency' => [1, 365],
            'minimum_service_period' => [0, 600],
            'appraisal_deadline_grace_period' => [0, 365],
        ];

        if (isset($integerRanges[$key])) {
            if (
                !is_scalar($value) ||
                !ctype_digit((string) $value)
            ) {
                return $this->invalidSetting($key);
            }

            [$min, $max] = $integerRanges[$key];
            $number = (int) $value;

            if ($number < $min || $number > $max) {
                return $this->invalidSetting($key);
            }

            return $this->validSetting((string) $number);
        }

        if ($key === 'maximum_increment_percentage') {
            if (
                !is_numeric($value) ||
                (float) $value < 0 ||
                (float) $value > 100
            ) {
                return $this->invalidSetting($key);
            }

            return $this->validSetting(
                $this->normalizeDecimal((float) $value)
            );
        }

        if ($key === 'ctc_range_increment_rules') {
            return $this->validateCtcRangeRules($value);
        }

        return $this->invalidSetting($key);
    }

    private function validateCtcRangeRules(mixed $value): array
    {
        $rows = $this->decodeRows($value);

        if ($rows === null) {
            return $this->invalidSetting('ctc_range_increment_rules');
        }

        $ranges = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                return $this->invalidSetting('ctc_range_increment_rules');
            }

            $ctcFrom = $row['ctc_from'] ?? null;
            $ctcTo = $row['ctc_to'] ?? null;
            $maximumPercentage = $row['maximum_percentage'] ?? null;

            if (
                $ctcFrom === null ||
                $ctcFrom === '' ||
                $ctcTo === null ||
                $ctcTo === '' ||
                $maximumPercentage === null ||
                $maximumPercentage === ''
            ) {
                return $this->invalidSetting('ctc_range_increment_rules');
            }

            if (
                !is_numeric($ctcFrom) ||
                !is_numeric($ctcTo) ||
                !is_numeric($maximumPercentage)
            ) {
                return $this->invalidSetting('ctc_range_increment_rules');
            }

            $ctcFrom = (float) $ctcFrom;
            $ctcTo = (float) $ctcTo;
            $maximumPercentage = (float) $maximumPercentage;

            if (
                $ctcFrom < 0 ||
                $ctcTo <= $ctcFrom ||
                $maximumPercentage < 0 ||
                $maximumPercentage > 100
            ) {
                return $this->invalidSetting('ctc_range_increment_rules');
            }

            $ranges[] = [
                'ctc_from' => $ctcFrom,
                'ctc_to' => $ctcTo,
                'maximum_percentage' => $maximumPercentage,
            ];
        }

        usort(
            $ranges,
            static fn(array $a, array $b): int =>
            $a['ctc_from'] <=> $b['ctc_from']
        );

        for ($i = 1, $count = count($ranges); $i < $count; $i++) {
            if (
                $ranges[$i]['ctc_from'] <
                $ranges[$i - 1]['ctc_to']
            ) {
                return $this->invalidSetting(
                    'ctc_range_increment_rules'
                );
            }
        }

        foreach ($ranges as &$range) {
            $range['ctc_from'] = $this->normalizeDecimal(
                $range['ctc_from']
            );
            $range['ctc_to'] = $this->normalizeDecimal(
                $range['ctc_to']
            );
            $range['maximum_percentage'] = $this->normalizeDecimal(
                $range['maximum_percentage']
            );
        }

        unset($range);

        return $this->validSetting(
            json_encode(
                $ranges,
                JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES |
                    JSON_THROW_ON_ERROR
            )
        );
    }

    /**
     * Accept either a JSON string or an already-decoded array.
     */
    private function decodeRows(mixed $value): ?array
    {
        if (is_string($value)) {
            try {
                $value = json_decode(
                    $value,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            } catch (Throwable) {
                return null;
            }
        }

        return is_array($value)
            ? array_values($value)
            : null;
    }

    private function normalizeDecimal(float $value): string
    {
        return rtrim(
            rtrim(
                number_format($value, 2, '.', ''),
                '0'
            ),
            '.'
        );
    }

    private function validSetting(string $value): array
    {
        return [
            'valid' => true,
            'value' => $value,
            'message' => '',
        ];
    }

    private function invalidSetting(string $key): array
    {
        return [
            'valid' => false,
            'value' => null,
            'message' => "Please provide a valid value for {$key}.",
        ];
    }

    private function errorResponse(
        string $message,
        int $statusCode
    ): ResponseInterface {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON([
                'success' => false,
                'message' => $message,
                'data' => null,
                'errors' => [],
            ]);
    }
}
