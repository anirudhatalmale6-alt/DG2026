<?php

namespace Modules\CIMSAppointments\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAvailability extends Model
{
    protected $table = 'cims_appointments_availability';

    protected $fillable = [
        'staff_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const DAYS = [
        0 => 'Monday',
        1 => 'Tuesday',
        2 => 'Wednesday',
        3 => 'Thursday',
        4 => 'Friday',
        5 => 'Saturday',
    ];

    // --- Relationships ---

    public function staff()
    {
        return $this->belongsTo(AppointmentStaff::class, 'staff_id');
    }

    // --- Helpers ---

    public function getDayName()
    {
        return self::DAYS[$this->day_of_week] ?? 'Unknown';
    }

    public function getTimeSlots()
    {
        $slots = [];
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);

        while ($start < $end) {
            $slots[] = date('H:i', $start);
            $start = strtotime('+1 hour', $start);
        }

        return $slots;
    }
}
