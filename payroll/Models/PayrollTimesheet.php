<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollTimesheet extends Model
{
    protected $table = 'cims_payroll_timesheets';

    protected $fillable = [
        'employee_id', 'period_start', 'period_end',
        'normal_hours', 'overtime_15x_hours', 'overtime_2x_hours',
        'sunday_hours', 'public_holiday_hours',
        'days_worked', 'days_absent', 'days_leave',
        'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'normal_hours' => 'decimal:2',
        'overtime_15x_hours' => 'decimal:2',
        'overtime_2x_hours' => 'decimal:2',
        'sunday_hours' => 'decimal:2',
        'public_holiday_hours' => 'decimal:2',
        'days_worked' => 'decimal:2',
        'days_absent' => 'decimal:2',
        'days_leave' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function getTotalHoursAttribute(): float
    {
        return $this->normal_hours + $this->overtime_15x_hours + $this->overtime_2x_hours
            + $this->sunday_hours + $this->public_holiday_hours;
    }
}
