<?php

namespace Modules\CustomerAgedAnalysis\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgedAnalysisService
{
    /**
     * Invoice statuses considered unpaid: Due(2), Overdue(3), Partial(4)
     */
    protected array $unpaidStatuses = [2, 3, 4];

    /**
     * Get all clients who have at least one unpaid invoice, with their client codes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getClientsWithBalances()
    {
        $fieldId = config('customer_aged_analysis.client_code_field_id', 38);

        return DB::table('clients')
            ->join('invoices', 'clients.client_id', '=', 'invoices.bill_clientid')
            ->leftJoin('tblcustomfieldsvalues', function ($join) use ($fieldId) {
                $join->on('clients.client_id', '=', 'tblcustomfieldsvalues.relid')
                    ->where('tblcustomfieldsvalues.fieldid', '=', $fieldId);
            })
            ->whereIn('invoices.bill_status', $this->unpaidStatuses)
            ->select(
                'clients.client_id',
                'clients.client_company_name',
                'tblcustomfieldsvalues.value as client_code'
            )
            ->groupBy('clients.client_id', 'clients.client_company_name', 'tblcustomfieldsvalues.value')
            ->orderBy('clients.client_company_name', 'asc')
            ->get();
    }

    /**
     * Generate the full aged analysis report for all clients.
     *
     * Returns an array with:
     * - clients: array of client rows with aging buckets
     * - grand_totals: sum of each bucket across all clients
     * - as_of_date: the date used for aging calculation
     *
     * @param string|null $asOfDate (Y-m-d format, defaults to today)
     * @return array
     */
    public function generateReport(?string $asOfDate = null): array
    {
        $asOfDate = $asOfDate ? Carbon::parse($asOfDate) : Carbon::today();

        // Get all unpaid invoices across all clients in one query
        $invoices = DB::table('invoices')
            ->whereIn('bill_status', $this->unpaidStatuses)
            ->orderBy('bill_clientid')
            ->orderBy('bill_due_date', 'asc')
            ->get();

        if ($invoices->isEmpty()) {
            return [
                'clients' => [],
                'grand_totals' => [
                    'current' => 0.00,
                    '30_days' => 0.00,
                    '60_days' => 0.00,
                    '90_plus' => 0.00,
                    'total'   => 0.00,
                ],
                'as_of_date' => $asOfDate->format('Y-m-d'),
            ];
        }

        // Get all invoice IDs for bulk payment lookup
        $invoiceIds = $invoices->pluck('bill_invoiceid')->toArray();

        // Get all payments for these invoices in one query (summed)
        $payments = DB::table('payments')
            ->whereIn('payment_invoiceid', $invoiceIds)
            ->select('payment_invoiceid', DB::raw('SUM(payment_amount) as total_paid'))
            ->groupBy('payment_invoiceid')
            ->pluck('total_paid', 'payment_invoiceid');

        // Get individual payment records for tooltip details
        $paymentDetails = DB::table('payments')
            ->whereIn('payment_invoiceid', $invoiceIds)
            ->select('payment_id', 'payment_invoiceid', 'payment_amount', 'payment_date', 'payment_gateway', 'payment_transaction_id')
            ->orderBy('payment_date', 'asc')
            ->get()
            ->groupBy('payment_invoiceid');

        // Get client info (client_code + company name)
        $fieldId = config('customer_aged_analysis.client_code_field_id', 38);
        $clientIds = $invoices->pluck('bill_clientid')->unique()->toArray();

        $clientInfo = DB::table('clients')
            ->leftJoin('tblcustomfieldsvalues', function ($join) use ($fieldId) {
                $join->on('clients.client_id', '=', 'tblcustomfieldsvalues.relid')
                    ->where('tblcustomfieldsvalues.fieldid', '=', $fieldId);
            })
            ->whereIn('clients.client_id', $clientIds)
            ->select('clients.client_id', 'clients.client_company_name', 'tblcustomfieldsvalues.value as client_code')
            ->get()
            ->keyBy('client_id');

        // Build per-client aging data
        $clientData = [];

        foreach ($invoices as $invoice) {
            $clientId = $invoice->bill_clientid;
            $dueDate = Carbon::parse($invoice->bill_due_date);
            $daysOverdue = $dueDate->diffInDays($asOfDate, false);

            if ($daysOverdue < 0) {
                $daysOverdue = 0;
            }

            $paymentsReceived = isset($payments[$invoice->bill_invoiceid])
                ? (float) $payments[$invoice->bill_invoiceid]
                : 0.00;

            $outstanding = (float) $invoice->bill_final_amount - $paymentsReceived;

            if ($outstanding <= 0) {
                continue;
            }

            $bucket = $this->getBucket($daysOverdue);

            // Initialize client entry if needed
            if (!isset($clientData[$clientId])) {
                $info = $clientInfo->get($clientId);
                $clientData[$clientId] = [
                    'client_id'   => $clientId,
                    'client_code' => $info ? $info->client_code : '',
                    'client_name' => $info ? $info->client_company_name : 'Unknown',
                    'current'     => 0.00,
                    '30_days'     => 0.00,
                    '60_days'     => 0.00,
                    '90_plus'     => 0.00,
                    'total'       => 0.00,
                    'invoices'    => [],
                ];
            }

            $clientData[$clientId][$bucket] += $outstanding;
            $clientData[$clientId]['total'] += $outstanding;

            // Build individual payment details for tooltip
            $invPaymentDetails = [];
            if (isset($paymentDetails[$invoice->bill_invoiceid])) {
                foreach ($paymentDetails[$invoice->bill_invoiceid] as $pd) {
                    $invPaymentDetails[] = [
                        'id'     => $pd->payment_id,
                        'amount' => (float) $pd->payment_amount,
                        'date'   => $pd->payment_date,
                        'method' => $pd->payment_gateway ?: 'N/A',
                        'ref'    => $pd->payment_transaction_id ?: '',
                    ];
                }
            }

            // Store invoice detail for the detailed view
            $clientData[$clientId]['invoices'][] = [
                'invoice_id'        => $invoice->bill_invoiceid,
                'invoice_reference' => $invoice->bill_invoiceid,
                'invoice_date'      => $invoice->bill_date,
                'due_date'          => $invoice->bill_due_date,
                'amount'            => (float) $invoice->bill_final_amount,
                'payments'          => $paymentsReceived,
                'payment_details'   => $invPaymentDetails,
                'outstanding'       => $outstanding,
                'days_overdue'      => (int) $daysOverdue,
                'bucket'            => $bucket,
            ];
        }

        // Sort by client name
        usort($clientData, function ($a, $b) {
            return strcasecmp($a['client_name'], $b['client_name']);
        });

        // Calculate grand totals
        $grandTotals = [
            'current' => 0.00,
            '30_days' => 0.00,
            '60_days' => 0.00,
            '90_plus' => 0.00,
            'total'   => 0.00,
        ];

        foreach ($clientData as $row) {
            $grandTotals['current'] += $row['current'];
            $grandTotals['30_days'] += $row['30_days'];
            $grandTotals['60_days'] += $row['60_days'];
            $grandTotals['90_plus'] += $row['90_plus'];
            $grandTotals['total']   += $row['total'];
        }

        // Get company settings for currency
        $settings = $this->getCompanySettings();

        return [
            'clients'      => array_values($clientData),
            'grand_totals' => $grandTotals,
            'as_of_date'   => $asOfDate->format('Y-m-d'),
            'settings'     => $settings,
        ];
    }

    /**
     * Determine which aging bucket a number of days falls into.
     */
    protected function getBucket(int $daysOverdue): string
    {
        if ($daysOverdue <= 30) {
            return 'current';
        } elseif ($daysOverdue <= 60) {
            return '30_days';
        } elseif ($daysOverdue <= 90) {
            return '60_days';
        } else {
            return '90_plus';
        }
    }

    /**
     * Get company settings from the settings table.
     */
    public function getCompanySettings(): array
    {
        $keys = [
            'settings_company_name',
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

        return array_merge([
            'settings_company_name' => 'Accounting Taxation and Payroll (Pty) Ltd',
            'settings_system_currency_symbol' => 'R ',
            'settings_system_date_format' => 'd-m-Y',
            'settings_invoices_prefix' => 'INV-',
        ], $settings);
    }
}
