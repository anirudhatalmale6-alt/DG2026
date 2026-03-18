<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollIncomeType extends Model
{
    protected $table = 'cims_payroll_income_types';

    protected $fillable = [
        'name', 'sars_code', 'is_taxable', 'is_uif_applicable',
        'description', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_taxable' => 'boolean',
        'is_uif_applicable' => 'boolean',
        'is_active' => 'boolean',
    ];
}
