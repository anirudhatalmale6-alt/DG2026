<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollEmployeePayslipDefault extends Model
{
    protected $table = 'cims_payroll_employee_payslip_defaults';

    protected $fillable = [
        'employee_id',
        'section',
        'name',
        'hours',
        'rate',
        'sort_order',
    ];

    protected $casts = [
        'hours' => 'decimal:4',
        'rate' => 'decimal:4',
    ];

    public function employee()
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }
}
