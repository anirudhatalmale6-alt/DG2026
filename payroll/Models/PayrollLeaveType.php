<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLeaveType extends Model
{
    protected $table = 'cims_payroll_leave_types';

    protected $fillable = [
        'name', 'code', 'days_per_year', 'cycle_years',
        'is_paid', 'is_statutory', 'is_active',
        'description', 'sort_order',
    ];

    protected $casts = [
        'days_per_year' => 'decimal:2',
        'is_paid' => 'boolean',
        'is_statutory' => 'boolean',
        'is_active' => 'boolean',
    ];
}
