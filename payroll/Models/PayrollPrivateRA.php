<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPrivateRA extends Model
{
    protected $table = 'cims_payroll_private_ra';

    protected $fillable = [
        'employee_id', 'provider_name', 'policy_number',
        'contribution_amount', 'contribution_type', 'percentage_of_salary',
        'start_date', 'end_date', 'is_active', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contribution_amount' => 'decimal:2',
        'percentage_of_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }
}
