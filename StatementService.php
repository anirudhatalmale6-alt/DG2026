<?php

namespace Modules\CustomerStatements\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatementService
{
    /**
     * Invoice statuses to exclude (Draft=1, Cancelled=6)
     */
    protected array $excludedStatuses = [1, 6];

    /**
     * Get all clients for the dropdown selector.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClients()
    {
        $fieldId = config('customer_statements.client_code_field_id', 38);

        return DB::table('clients')
            ->leftJoin('tblcustomfieldsvalues', function ($join) use ($fieldId) {
                $join->on('clients.client_id', '=', 'tblcustomfieldsvalues.relid')
                    ->where('tblcustomfieldsvalues.fieldid', '=', $fieldId);
            })
            ->select('clients.client_id', 'clients.client_company_name', 'tblcustomfieldsvalues.value as client_code')
            ->orderBy('clients.client_company_name', 'asc')
            ->get();
    }

    /**
     * Get client details by ID.
     *
     * @param int $clientId
     * @return object|null
     */
    public function getClient(int $clientId)
    {
        return DB::table('clients')
            ->where('client_id', $clientId)
            ->first();
    }

    /**
     * Get the primary contact email for a client.
     *
     * @param int $clientId
     * @return string|null
     */
    public function getClientEmail(int $clientId): ?string
    {
        $user = DB::table('users')
            ->where('clientid', $clientId)
            ->where('account_owner', 'yes')
            ->first();

        return $user ? $user->email : null;
    }

    /**
     * Get the primary contact name for a client.
     *
     * @param int $clientId
     * @return string
     */
    public function getClientContactName(int $clientId): string
    {
        $user = DB::table('users')
            ->where('clientid', $clientId)
            ->where('account_owner', 'yes')
            ->first();

        if ($user) {
            return trim($user->first_name . ' ' . $user->last_name);
        }

        return '';
    }

    /**
     * Get the Client Code custom field value.
     *
     * @param int $clientId
     * @return string|null
     */
    public function getClientCode(int $clientId): ?string
    {
        $fieldId = config('customer_statements.client_code_field_id', 38);

        $record = DB::table('tblcustomfieldsvalues')
            ->where('relid', $clientId)
            ->where('fieldid', $fieldId)
            ->first();

        return $record ? $record->value : null;
    }

    /**
     * Get company settings from the settings table.
     *
     * @return array
     */
    public function getCompanySettings(): array
    {
        $keys = [
            'settings_company_name',
            'settings_company_address_line_1',
            'settings_company_city',
            'settings_company_state',
            'settings_company_zipcode',
            'settings_company_country',
            'settings_company_customfield_1',
            'settings_system_currency_symbol',
            'settings_system_date_format',
            'settings_invoices_prefix',
        ];

        $settings = [];
        $row = DB::table('settings')->where('settings_id', 1)->first();
        if ($row) {
            foreach ($keys as $key) {
                if (isset($row->$key)) {
                    $settings[$key] = $row->$key;
                }
            }
        }

        // Defaults
        return array_merge([
            'settings_company_name' => 'Accounting Taxation and Payroll (Pty) Ltd',
            'settings_company_address_line_1' => '29 Coedmore Road, Bellair',
            'settings_company_city' => 'Durban',
            'settings_company_state' => 'Kwa-Zulu Natal',
            'settings_company_zipcode' => '4094',
            'settings_company_country' => 'South Africa',
            'settings_company_customfield_1' => 'VAT No. 406 030 6646',
            'settings_system_currency_symbol' => 'R ',
            'settings_system_date_format' => 'd-m-Y',
            'settings_invoices_prefix' => 'INV-',
        ], $settings);
    }

    /**
     * Calculate the opening balance for a client before the date range.
     * Opening balance = sum of invoice amounts - sum of payment amounts before from_date.
     *
     * @param int $clientId
     * @param string $fromDate (Y-m-d format)
     * @return float
     */
    public function calculateOpeningBalance(int $clientId, string $fromDate): float
    {
        // Sum of invoices before the from date (debits)
        $invoiceTotal = DB::table('invoices')
            ->where('bill_clientid', $clientId)
            ->whereNotIn('bill_status', $this->excludedStatuses)
            ->where('bill_date', '<', $fromDate)
            ->sum('bill_final_amount');

        // Sum of payments before the from date (credits)
        $paymentTotal = DB::table('payments')
            ->where('payment_clientid', $clientId)
            ->where('payment_date', '<', $fromDate)
            ->sum('payment_amount');

        return (float) $invoiceTotal - (float) $paymentTotal;
    }

    /**
     * Get all invoices for a client within the date range.
     *
     * @param int $clientId
     * @param string $fromDate (Y-m-d format)
     * @param string $toDate (Y-m-d format)
     * @return \Illuminate\Support\Collection
     */
    public function getInvoices(int $clientId, string $fromDate, string $toDate)
    {
        return DB::table('invoices')
            ->where('bill_clientid', $clientId)
            ->whereNotIn('bill_status', $this->excludedStatuses)
            ->whereBetween('bill_date', [$fromDate, $toDate])
            ->orderBy('bill_date', 'asc')
            ->orderBy('bill_invoiceid', 'asc')
            ->get();
    }

    /**
     * Get all payments for a client within the date range.
     *
     * @param int $clientId
     * @param string $fromDate (Y-m-d format)
     * @param string $toDate (Y-m-d format)
     * @return \Illuminate\Support\Collection
     */
    public function getPayments(int $clientId, string $fromDate, string $toDate)
    {
        return DB::table('payments')
            ->where('payment_clientid', $clientId)
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->orderBy('payment_date', 'asc')
            ->orderBy('payment_id', 'asc')
            ->get();
    }

    /**
     * Generate the full statement data: combined chronological transactions with running balance.
     *
     * @param int $clientId
     * @param string $fromDate (Y-m-d format)
     * @param string $toDate (Y-m-d format)
     * @return array
     */
    public function generateStatement(int $clientId, string $fromDate, string $toDate): array
    {
        $settings = $this->getCompanySettings();
        $client = $this->getClient($clientId);
        $clientCode = $this->getClientCode($clientId);
        $clientEmail = $this->getClientEmail($clientId);
        $contactName = $this->getClientContactName($clientId);

        $openingBalance = $this->calculateOpeningBalance($clientId, $fromDate);
        $invoices = $this->getInvoices($clientId, $fromDate, $toDate);
        $payments = $this->getPayments($clientId, $fromDate, $toDate);

        $prefix = $settings['settings_invoices_prefix'] ?? 'INV-';

        // Build transactions array
        $transactions = [];

        foreach ($invoices as $invoice) {
            $transactions[] = [
                'date' => $invoice->bill_date,
                'type' => 'Invoice',
                'id' => $invoice->bill_invoiceid,
                'reference' => $prefix . str_pad($invoice->bill_invoiceid, 6, '0', STR_PAD_LEFT),
                'description' => 'Invoice ' . $prefix . str_pad($invoice->bill_invoiceid, 6, '0', STR_PAD_LEFT),
                'debit' => (float) $invoice->bill_final_amount,
                'credit' => 0.00,
                'sort_key' => $invoice->bill_date . '_0_' . str_pad($invoice->bill_invoiceid, 10, '0', STR_PAD_LEFT),
            ];
        }

        foreach ($payments as $payment) {
            $reference = $payment->payment_transaction_id ?: 'PAY-' . str_pad($payment->payment_id, 6, '0', STR_PAD_LEFT);
            $description = 'Payment received';
            if ($payment->payment_gateway) {
                $description .= ' via ' . $payment->payment_gateway;
            }

            // Try to link to an invoice
            if ($payment->payment_invoiceid) {
                $description .= ' (Invoice ' . $prefix . str_pad($payment->payment_invoiceid, 6, '0', STR_PAD_LEFT) . ')';
            }

            $transactions[] = [
                'date' => $payment->payment_date,
                'type' => 'Payment',
                'id' => $payment->payment_id,
                'reference' => $reference,
                'description' => $description,
                'debit' => 0.00,
                'credit' => (float) $payment->payment_amount,
                'sort_key' => $payment->payment_date . '_1_' . str_pad($payment->payment_id, 10, '0', STR_PAD_LEFT),
            ];
        }

        // Sort chronologically (invoices before payments on same date)
        usort($transactions, function ($a, $b) {
            return strcmp($a['sort_key'], $b['sort_key']);
        });

        // Calculate running balance
        $runningBalance = $openingBalance;
        foreach ($transactions as &$transaction) {
            $runningBalance += $transaction['debit'] - $transaction['credit'];
            $transaction['balance'] = $runningBalance;
            unset($transaction['sort_key']); // Remove sort key from output
        }
        unset($transaction);

        // Calculate totals
        $totalDebits = array_sum(array_column($transactions, 'debit'));
        $totalCredits = array_sum(array_column($transactions, 'credit'));
        $closingBalance = $openingBalance + $totalDebits - $totalCredits;

        return [
            'client' => $client,
            'client_code' => $clientCode,
            'client_email' => $clientEmail,
            'contact_name' => $contactName,
            'settings' => $settings,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'closing_balance' => $closingBalance,
        ];
    }

    /**
     * Format a monetary amount with the currency symbol.
     *
     * @param float $amount
     * @param string $currencySymbol
     * @return string
     */
    public static function formatCurrency(float $amount, string $currencySymbol = 'R '): string
    {
        return $currencySymbol . number_format($amount, 2, '.', ' ');
    }

    /**
     * Format a date according to the system date format.
     *
     * @param string|null $date
     * @param string $format
     * @return string
     */
    public static function formatDate(?string $date, string $format = 'd-m-Y'): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }
}
