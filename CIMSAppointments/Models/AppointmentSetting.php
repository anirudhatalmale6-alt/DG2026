<?php

namespace Modules\CIMSAppointments\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSetting extends Model
{
    protected $table = 'cims_appointments_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_group',
    ];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function setValue($key, $value, $group = 'general')
    {
        return self::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value, 'setting_group' => $group]
        );
    }

    public static function getByGroup($group)
    {
        return self::where('setting_group', $group)->pluck('setting_value', 'setting_key')->toArray();
    }

    public static function getAllSettings()
    {
        return self::pluck('setting_value', 'setting_key')->toArray();
    }
}
