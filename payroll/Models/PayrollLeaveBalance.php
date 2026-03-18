<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLeaveBalance extends Model
{
    protected $table = 'cims_payroll_leave_balances';

    protected $fillable = [
        'employee_id', 'leave_type_id', 'year',
        'entitled_days', 'taken_days', 'pending_days',
        'carried_forward',
    ];

    protected $casts = [
        'entitled_days' => 'decimal:2',
        'taken_days' => 'decimal:2',
        'pending_days' => 'decimal:2',
        'carried_forward' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(PayrollLeaveType::class, 'leave_type_id');
    }

    public function getRemainingAttribute(): float
    {
        return $this->entitled_days + $this->carried_forward - $this->taken_days - $this->pending_days;
    }
}
