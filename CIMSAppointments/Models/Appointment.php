<?php

namespace Modules\CIMSAppointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $table = 'cims_appointments';

    protected $fillable = [
        'client_id',
        'client_code',
        'client_name',
        'client_email',
        'client_phone',
        'staff_id',
        'service_id',
        'appointment_date',
        'start_time',
        'end_time',
        'duration_hours',
        'status',
        'notes',
        'internal_notes',
        'is_chargeable',
        'amount',
        'payment_status',
        'invoice_id',
        'confirmation_sent_at',
        'reminder_sent_at',
        'cancelled_at',
        'cancellation_reason',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'is_chargeable' => 'boolean',
        'amount' => 'decimal:2',
        'confirmation_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_WAIVED = 'waived';
    public const PAYMENT_INVOICED = 'invoiced';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_NO_SHOW => 'No Show',
    ];

    public const PAYMENT_STATUSES = [
        self::PAYMENT_UNPAID => 'Unpaid',
        self::PAYMENT_PAID => 'Paid',
        self::PAYMENT_WAIVED => 'Waived',
        self::PAYMENT_INVOICED => 'Invoiced',
    ];

    // --- Relationships ---

    public function staff()
    {
        return $this->belongsTo(AppointmentStaff::class, 'staff_id');
    }

    public function service()
    {
        return $this->belongsTo(AppointmentService::class, 'service_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
    }

    // --- Scopes ---

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc');
    }

    public function scopeToday($query)
    {
        return $query->where('appointment_date', now()->toDateString())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            ->orderBy('start_time', 'asc');
    }

    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('appointment_date', $date);
    }

    public function scopeForDateRange($query, $from, $to)
    {
        return $query->whereBetween('appointment_date', [$from, $to]);
    }

    public function scopeNeedingReminder($query, $hoursBeforeDefault = 24)
    {
        $reminderTime = now()->addHours($hoursBeforeDefault);

        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            ->whereNull('reminder_sent_at')
            ->whereNotNull('client_email')
            ->where('appointment_date', '<=', $reminderTime->toDateString())
            ->where(function ($q) use ($reminderTime) {
                $q->where('appointment_date', '<', $reminderTime->toDateString())
                  ->orWhere(function ($q2) use ($reminderTime) {
                      $q2->where('appointment_date', $reminderTime->toDateString())
                         ->where('start_time', '<=', $reminderTime->format('H:i:s'));
                  });
            });
    }

    // --- Helpers ---

    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeClass()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_CONFIRMED => 'badge-primary',
            self::STATUS_COMPLETED => 'badge-success',
            self::STATUS_CANCELLED => 'badge-danger',
            self::STATUS_NO_SHOW => 'badge-secondary',
            default => 'badge-light',
        };
    }

    public function getPaymentStatusLabel()
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function getPaymentBadgeClass()
    {
        return match ($this->payment_status) {
            self::PAYMENT_UNPAID => 'badge-danger',
            self::PAYMENT_PAID => 'badge-success',
            self::PAYMENT_WAIVED => 'badge-secondary',
            self::PAYMENT_INVOICED => 'badge-info',
            default => 'badge-light',
        };
    }

    public function getFormattedDate()
    {
        return $this->appointment_date ? $this->appointment_date->format('d M Y') : 'N/A';
    }

    public function getFormattedTime()
    {
        $start = date('H:i', strtotime($this->start_time));
        $end = date('H:i', strtotime($this->end_time));
        return $start . ' - ' . $end;
    }

    public function canCancel()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function canComplete()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function canConfirm()
    {
        return $this->status === self::STATUS_PENDING;
    }
}
