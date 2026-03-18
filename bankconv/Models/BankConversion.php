<?php

namespace Modules\CIMS_BANKCONV\Models;

use Illuminate\Database\Eloquent\Model;

class BankConversion extends Model
{
    protected $table = 'cims_bank_conversions';

    protected $fillable = [
        'client_id',
        'client_code',
        'company_name',
        'bank_type',
        'account_number',
        'statement_period',
        'opening_balance',
        'closing_balance',
        'total_credits',
        'total_debits',
        'credit_count',
        'debit_count',
        'transaction_count',
        'original_filename',
        'csv_filename',
        'converted_by',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'total_debits' => 'decimal:2',
    ];
}
