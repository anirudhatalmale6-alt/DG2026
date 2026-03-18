<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollCompanyContributionType extends Model
{
    protected $table = 'cims_payroll_company_contribution_types';

    protected $fillable = [
        'name', 'sars_code', 'calc_type', 'default_value',
        'linked_deduction_id', 'is_statutory', 'is_auto_calculated',
        'description', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'default_value' => 'decimal:2',
        'is_statutory' => 'boolean',
        'is_auto_calculated' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function linkedDeduction()
    {
        return $this->belongsTo(PayrollDeductionType::class, 'linked_deduction_id');
    }
}
