<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollMedicalAid extends Model
{
    protected $table = 'cims_payroll_medical_aid';

    protected $fillable = [
        'employee_id', 'tax_year', 'month',
        'scheme_name', 'plan_type', 'member_number',
        'main_member', 'adult_dependants', 'child_dependants',
        'employee_contribution', 'employer_contribution', 'tax_credit',
    ];

    protected $casts = [
        'main_member' => 'boolean',
        'employee_contribution' => 'decimal:2',
        'employer_contribution' => 'decimal:2',
        'tax_credit' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }
}
