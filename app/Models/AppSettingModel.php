<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettingModel extends Model
{
    protected $table            = 'app_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'scope_type',
        'scope_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get a setting.
     *
     * If an organization ID is supplied, return its override when one
     * exists; otherwise fall back to the global value.
     */
    public function getSetting(string $key, ?int $organizationId = null): ?string
    {
        if ($organizationId !== null && $organizationId > 0) {
            $organizationSetting = $this
                ->where('setting_key', $key)
                ->where('scope_type', 'organization')
                ->where('scope_id', $organizationId)
                ->first();

            if ($organizationSetting !== null) {
                return $organizationSetting['setting_value'];
            }
        }

        $globalSetting = $this
            ->where('setting_key', $key)
            ->where('scope_type', 'global')
            ->where('scope_id', 0)
            ->first();

        return $globalSetting['setting_value'] ?? null;
    }

    /**
     * Save or update a setting.
     *
     * JSON-based settings should be JSON-encoded by the caller before
     * being passed to this method.
     */
    public function saveSetting(string $key, ?string $value, string $scopeType = 'global', int $scopeId = 0): bool
    {
        if (!in_array($scopeType, ['global', 'organization'], true)) {
            return false;
        }

        if ($key === '') {
            return false;
        }

        if ($scopeType === 'global') {
            $scopeId = 0;
        } elseif ($scopeId <= 0) {
            return false;
        }

        $existing = $this
            ->where('setting_key', $key)
            ->where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->first();

        $data = [
            'setting_key'   => $key,
            'setting_value' => $value,
            'scope_type'    => $scopeType,
            'scope_id'      => $scopeId,
        ];

        if ($existing !== null) {
            return $this->update($existing['id'], $data) !== false;
        }

        return $this->insert($data) !== false;
    }

    /**
     * Retrieve all settings for a particular scope as key => value.
     */
    public function getSettingsForScope(string $scopeType = 'global', int $scopeId = 0): array
    {
        if (!in_array($scopeType, ['global', 'organization'], true)) {
            return [];
        }

        if ($scopeType === 'global') {
            $scopeId = 0;
        } elseif ($scopeId <= 0) {
            return [];
        }

        $rows = $this
            ->where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->findAll();

        $settings = [];

        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }

    /**
     * Get a global setting, returning a fallback if it has not been saved.
     */
    public function getGlobalValue(string $key, string $default = ''): string
    {
        return $this->getSetting($key) ?? $default;
    }

    /**
     * Get a global setting as a boolean.
     */
    public function getGlobalBoolean(string $key, bool $default = false): bool
    {
        $value = $this->getSetting($key);

        if ($value === null || $value === '') {
            return $default;
        }

        return $value === '1';
    }

    /**
     * Get a global setting as an integer.
     */
    public function getGlobalInteger(string $key, int $default = 0): int
    {
        $value = $this->getSetting($key);

        return ($value !== null && is_numeric($value))
            ? (int) $value
            : $default;
    }

    /**
     * Get a global setting as a float.
     */
    public function getGlobalFloat(string $key, float $default = 0): float
    {
        $value = $this->getSetting($key);

        return ($value !== null && is_numeric($value))
            ? (float) $value
            : $default;
    }

    /**
     * Get a global JSON setting as an array.
     */
    public function getGlobalJson(string $key, array $default = []): array
    {
        $value = $this->getSetting($key);

        if ($value === null || $value === '') {
            return $default;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $default;
    }
}
