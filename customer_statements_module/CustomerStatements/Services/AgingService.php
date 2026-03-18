<?php

namespace Modules\CustomerStatements\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgingService
{
    /**
     * Invoice statuses considered unpaid: Due(2), Overdue(3), Partial(4)
     */
    protected array $unpaidStatuses = [2, 3, 4];

    /**
     * Calculate aging buckets for a client.
     *
     * Buckets:
     * - Current: 0-30 days overdue
     * - 30 Days: 31-60 days overdue
     * - 60 Days: 61-90 days overdue
     * - 90+ Days: 91+ days overdue
     *
     * @param int $clientId
     * @param string|null $asOfDate (Y-m-d format, defaults to today)
     * @return array
     */
    public function calculateAging(int $clientId, ?string $asOfDate = null): array
    {
        $asOfDate = $asOfDate ? Carbon::parse($asOfDate) : Carbon::today();

        // Get all unpaid/partially paid invoices for the client
        $invoices = DB::table('invoices')
            ->where('bill_clientid', $clientId)
            ->whereIn('bill_status', $this->unpaidStatuses)
            ->orderBy('bill_due_date', 'asc')
            ->get();

        $buckets = [
            'current' => 0.00,   // 0-30 days
            '30_days' => 0.00,   // 31-60 days
            '60_days' => 0.00,   // 61-90 days
            '90_plus' => 0.00,   // 91+ days
        ];

        $invoiceDetails = [
            'current' => [],
            '30_days' => [],
            '60_days' => [],
            '90_plus' => [],
        ];

        foreach ($invoices as $invoice) {
            $dueDate = Carbon::parse($invoice->bill_due_date);
            $daysOverdue = $dueDate->diffInDays($asOfDate, false);

            // If not yet due, days will be negative - treat as current
            if ($daysOverdue < 0) {
                $daysOverdue = 0;
            }

            // Calculate outstanding amount
            // Outstanding = invoice amount - payments received for this invoice
            $paymentsReceived = DB::table('payments')
                ->where('payment_invoiceid', $invoice->bill_invoiceid)
                ->sum('payment_amount');

            $outstanding = (float) $invoice->bill_final_amount - (float) $paymentsReceived;

            if ($outstanding <= 0) {
                continue; // Fully paid, skip
            }

            // Determine bucket
            $bucket = $this->getBucket($daysOverdue);

            $buckets[$bucket] += $outstanding;

            $invoiceDetails[$bucket][] = [
                'invoice_id' => $invoice->bill_invoiceid,
                'invoice_reference' => $invoice->bill_uniqueid ?? $invoice->bill_invoiceid,
                'due_date' => $invoice->bill_due_date,
                'amount' => (float) $invoice->bill_final_amount,
                'payments' => (float) $paymentsReceived,
                'outstanding' => $outstanding,
                'days_overdue' => (int) $daysOverdue,
            ];
        }

        $total = $buckets['current'] + $buckets['30_days'] + $buckets['60_days'] + $buckets['90_plus'];

        return [
            'buckets' => $buckets,
            'total' => $total,
            'details' => $invoiceDetails,
            'as_of_date' => $asOfDate->format('Y-m-d'),
        ];
    }

    /**
     * Determine which aging bucket a number of days falls into.
     *
     * @param int $daysOverdue
     * @return string
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
}
