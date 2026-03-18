<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPayRun extends Model
{
    protected $table = 'cims_payroll_pay_runs';

    protected $fillable = [
        'company_id', 'pay_period', 'period_start', 'period_end',
        'description', 'status',
        'total_gross', 'total_deductions', 'total_employer_cost', 'total_net_pay',
        'employee_count', 'processed_at', 'approved_at', 'approved_by', 'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'processed_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_employer_cost' => 'decimal:2',
        'total_net_pay' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(PayrollCompany::class, 'company_id');
    }

    public function lines()
    {
        return $this->hasMany(PayrollPayRunLine::class, 'pay_run_id');
    }
}
