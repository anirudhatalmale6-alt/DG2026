<?php

namespace Modules\CIMS_EMP201\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Emp201Declaration extends Model
{
    use SoftDeletes;

    protected $table = 'cims_emp201_declarations';

    protected $fillable = [
        // Client reference
        'client_id', 'client_code', 'company_name', 'trading_name',
        'company_number', 'vat_number', 'income_tax_number',
        'paye_number', 'sdl_number', 'uif_number',

        // Public Officer / Contact
        'first_name', 'surname', 'position',
        'telephone_number', 'mobile_number', 'email',

        // Period
        'pay_period', 'financial_year', 'period_combo', 'payment_period',

        // Payroll Tax
        'paye_liability', 'sdl_liability', 'uif_liability', 'payroll_liability',

        // ETI
        'eti_indicator', 'eti_brought_forward', 'eti_calculated',
        'eti_utilised', 'eti_carry_forward',

        // Total Payable
        'paye_payable', 'sdl_payable', 'uif_payable',
        'penalty_interest', 'tax_payable',

        // Penalties (legacy)
        'penalty', 'interest', 'other',

        // Payment Reference
        'payment_reference', 'payment_reference_number',

        // Payment (legacy)
        'payment_date', 'payment_type', 'payment_amount', 'balance_outstanding',

        // Payment Information (new)
        'payment_method', 'amount_paid', 'payment_ref_no',
        'file_proof_of_payment', 'payment_notes',

        // VDP
        'vdp_agreement', 'vdp_application_no',

        // Tax Practitioner
        'tax_practitioner_reg_no', 'tax_practitioner_tel_no',

        // Declaration
        'declaration_date', 'prepared_by',

        // Notes
        'notes',

        // User
        'user_id',

        // Files
        'file_emp201_return', 'file_emp201_statement',
        'file_working_papers', 'file_emp201_pack', 'emp_201_file',

        // Status
        'status',
    ];

    protected $casts = [
        'paye_liability' => 'decimal:2',
        'sdl_liability' => 'decimal:2',
        'uif_liability' => 'decimal:2',
        'payroll_liability' => 'decimal:2',
        'eti_brought_forward' => 'decimal:2',
        'eti_calculated' => 'decimal:2',
        'eti_utilised' => 'decimal:2',
        'eti_carry_forward' => 'decimal:2',
        'paye_payable' => 'decimal:2',
        'sdl_payable' => 'decimal:2',
        'uif_payable' => 'decimal:2',
        'penalty_interest' => 'decimal:2',
        'penalty' => 'decimal:2',
        'interest' => 'decimal:2',
        'other' => 'decimal:2',
        'tax_payable' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'balance_outstanding' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date',
        'declaration_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(\Modules\cims_pm_pro\Models\ClientMaster::class, 'client_id', 'client_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    public function getTotalPenaltiesAttribute()
    {
        return ($this->penalty ?? 0) + ($this->interest ?? 0) + ($this->other ?? 0);
    }
}
