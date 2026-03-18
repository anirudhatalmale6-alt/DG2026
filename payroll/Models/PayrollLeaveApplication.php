<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLeaveApplication extends Model
{
    protected $table = 'cims_payroll_leave_applications';

    protected $fillable = [
        'employee_id', 'leave_type_id', 'start_date', 'end_date',
        'days_requested', 'reason', 'status',
        'approved_by', 'approved_at', 'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'days_requested' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(PayrollLeaveType::class, 'leave_type_id');
    }
}
