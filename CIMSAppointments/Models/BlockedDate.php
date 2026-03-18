<?php

namespace Modules\CIMSAppointments\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedDate extends Model
{
    protected $table = 'cims_appointments_blocked_dates';

    protected $fillable = [
        'staff_id',
        'blocked_date',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'blocked_date' => 'date',
    ];

    // --- Relationships ---

    public function staff()
    {
        return $this->belongsTo(AppointmentStaff::class, 'staff_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    // --- Scopes ---

    public static function getForStaff($staffId, $fromDate = null, $toDate = null)
    {
        $query = self::where(function ($q) use ($staffId) {
            $q->where('staff_id', $staffId)
              ->orWhereNull('staff_id');
        });

        if ($fromDate) {
            $query->where('blocked_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('blocked_date', '<=', $toDate);
        }

        return $query->orderBy('blocked_date', 'asc')->get();
    }
}
