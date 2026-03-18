<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDeductionType extends Model
{
    protected $table = 'cims_payroll_deduction_types';

    protected $fillable = [
        'name', 'sars_code', 'calc_type', 'default_value',
        'is_statutory', 'is_auto_calculated',
        'description', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'default_value' => 'decimal:2',
        'is_statutory' => 'boolean',
        'is_auto_calculated' => 'boolean',
        'is_active' => 'boolean',
    ];
}
