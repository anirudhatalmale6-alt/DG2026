<?php

namespace Modules\CIMSAppointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentStaff extends Model
{
    use SoftDeletes;

    protected $table = 'cims_appointments_staff';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'position',
        'color',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function services()
    {
        return $this->belongsToMany(
            AppointmentService::class,
            'cims_appointments_staff_services',
            'staff_id',
            'service_id'
        );
    }

    public function availability()
    {
        return $this->hasMany(StaffAvailability::class, 'staff_id');
    }

    public function blockedDates()
    {
        return $this->hasMany(BlockedDate::class, 'staff_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'staff_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    // --- Scopes ---

    public static function getActive()
    {
        return self::where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();
    }

    public static function getActiveForService($serviceId)
    {
        return self::where('is_active', 1)
            ->whereHas('services', function ($q) use ($serviceId) {
                $q->where('cims_appointments_services.id', $serviceId);
            })
            ->orderBy('name', 'asc')
            ->get();
    }

    // --- Helpers ---

    public function getAvailabilityForDay($dayOfWeek)
    {
        return $this->availability()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', 1)
            ->first();
    }

    public function isBlockedOn($date)
    {
        // Check staff-specific blocks
        $staffBlocked = BlockedDate::where('staff_id', $this->id)
            ->where('blocked_date', $date)
            ->exists();

        // Check global blocks (staff_id = null)
        $globalBlocked = BlockedDate::whereNull('staff_id')
            ->where('blocked_date', $date)
            ->exists();

        return $staffBlocked || $globalBlocked;
    }
}
