<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['key', 'value'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Helper to get a setting value by key.
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function getValue(string $key, ?string $default = null): ?string
    {
        $setting = $this->where('key', $key)->first();
        return $setting ? $setting['value'] : $default;
    }

    /**
     * Helper to update or insert a setting value.
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function setValue(string $key, string $value): bool
    {
        $setting = $this->where('key', $key)->first();
        if ($setting) {
            return $this->update($setting['id'], ['value' => $value]);
        }
        return $this->insert(['key' => $key, 'value' => $value]) ? true : false;
    }
}
