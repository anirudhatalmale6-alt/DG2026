<?php

namespace Modules\CIMS_PAYROLL\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLoan extends Model
{
    protected $table = 'cims_payroll_loans';

    protected $fillable = [
        'employee_id', 'loan_type', 'loan_amount', 'outstanding_balance',
        'monthly_repayment', 'start_date', 'end_date',
        'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'monthly_repayment' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(PayrollEmployee::class, 'employee_id');
    }

    public function repayments()
    {
        return $this->hasMany(PayrollLoanRepayment::class, 'loan_id');
    }
}
