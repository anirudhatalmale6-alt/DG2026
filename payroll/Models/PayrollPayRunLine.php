<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPayRunLine extends Model
{
    protected $table = 'cims_payroll_pay_run_lines';

    protected $fillable = [
        'pay_run_id', 'employee_id',
        'basic_salary', 'hourly_rate',
        'normal_hours', 'overtime_15x_hours', 'overtime_2x_hours',
        'sunday_hours', 'public_holiday_hours',
        'gross_pay', 'total_income', 'total_deductions',
        'total_employer_contributions', 'net_pay',
        'paye_tax', 'uif_employee', 'uif_employer', 'sdl_employer',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_employer_contributions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'paye_tax' => 'decimal:2',
        'uif_employee' => 'decimal:2',
        'uif_employer' => 'decimal:2',
        'sdl_employer' => 'decimal:2',
    ];

    public function payRun()
    {
        return $this->belongsTo(PayrollPayRun::class, 'pay_run_id');
    }

    public function employee()
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(PayrollPayRunLineItem::class, 'pay_run_line_id');
    }
}
