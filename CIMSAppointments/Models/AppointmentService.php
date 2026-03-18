<?php

namespace Modules\CIMSAppointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentService extends Model
{
    use SoftDeletes;

    protected $table = 'cims_appointments_services';

    protected $fillable = [
        'name',
        'description',
        'default_duration_minutes',
        'min_duration_minutes',
        'max_duration_minutes',
        'is_chargeable',
        'price_per_hour',
        'color',
        'sort_order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_chargeable' => 'boolean',
        'is_active' => 'boolean',
        'price_per_hour' => 'decimal:2',
    ];

    // --- Relationships ---

    public function staff()
    {
        return $this->belongsToMany(
            AppointmentStaff::class,
            'cims_appointments_staff_services',
            'service_id',
            'staff_id'
        );
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'service_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    // --- Scopes ---

    public static function getActive()
    {
        return self::where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    // --- Helpers ---

    public function getMaxHours()
    {
        return intval($this->max_duration_minutes / 60);
    }

    public function getMinHours()
    {
        return intval($this->min_duration_minutes / 60);
    }

    public function calculatePrice($durationHours)
    {
        if (!$this->is_chargeable) {
            return 0;
        }
        return round($this->price_per_hour * $durationHours, 2);
    }
}
