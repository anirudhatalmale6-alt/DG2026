<?php

namespace Modules\DG2026\Models;

use Illuminate\Database\Eloquent\Model;

class DocgenSetting extends Model
{
    protected $table = 'docgen_settings';

    protected $fillable = [
        'setting_key', 'setting_value', 'setting_group',
        'setting_type', 'label', 'description',
    ];

    public static function getVal(string $key, $default = null)
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function setVal(string $key, $value)
    {
        return static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }
}
