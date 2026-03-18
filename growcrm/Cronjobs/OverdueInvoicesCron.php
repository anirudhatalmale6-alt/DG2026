<?php

/** -------------------------------------------------------------------------------------------------
 * TEMPLATE
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * It marks invoices as overdue and also send overdue reminder email
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;
use App\Repositories\InvoiceRepository;
use App\Repositories\UserRepository;
use Log;

class OverdueInvoicesCron {

    /**
     * Main entry point invoked by task scheduler
     */
    public function __invoke(
        UserRepository $userrepo,
        InvoiceRepository $invoicerepo
    ) {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        //boot system settings
        middlewareBootSettings();

        //[MT] boot mail settings
        env('MT_TPYE') ? middlewareSaaSBootMail() : middlewareBootMail();

        //boot theme for pdf css
        middlewareBootTheme();

        Log::info("starting to process overdue invoice reminders", ['cronjobs.overdue.invoices.reminder', config('app.debug_ref'), basename(__FILE__), __line__]);

        //process first reminder
        $this->processFirstReminder($userrepo, $invoicerepo);

        //process second reminder (if enabled)
        $settings2 = \App\Models\Settings2::find(1);
        if ($settings2->settings2_invoices_second_reminder_days > 0) {
            $this->processSecondReminder($userrepo, $invoicerepo, $settings2);
        }

        //process third reminder (if enabled)
        if ($settings2->settings2_invoices_third_reminder_days > 0) {
            $this->processThirdReminder($userrepo, $invoicerepo, $settings2);
        }

        //reset last cron run data
        \App\Models\Settings::where('settings_id', 1)
            ->update([
                'settings_cronjob_has_run' => 'yes',
                'settings_cronjob_last_run' => now(),
            ]);

    }

    /**
     * Process first overdue reminder (existing behavior)
     */
    protected function processFirstReminder($userrepo, $invoicerepo) {

        Log::info("processing first invoice reminder", ['cronjobs.overdue.invoices.reminder', config('app.debug_ref'), basename(__FILE__), __line__]);

        /*------------------------------------------------------------------------------
         * Process invoices that are already mareked as overdue, overdue, or part-paid
         * *---------------------------------------------------------------------------*/
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $invoices = \App\Models\Invoice::Where('bill_due_date', '<', $today)
            ->where('bill_overdue_reminder_sent', 'no')
            ->whereIn('bill_status', [2, 3, 4])
            ->take(5)->get();

        //process each one
        foreach ($invoices as $invoice) {

            //get full invoice
            if ($bills = $invoicerepo->search($invoice->bill_invoiceid)) {
                $bill = $bills->first();
            }

            //send email - only do this for invoices with an amount due
            if ($bill->invoice_balance > 0) {
                if ($user = $userrepo->getClientAccountOwner($invoice->bill_clientid)) {
                    $mail = new \App\Mail\OverdueInvoice($user, [], $bill);
                    $mail->build();
                }
            }

            //mark invoice as overdue and email sent
            $invoice->bill_overdue_reminder_sent = 'yes';
            $invoice->save();
        }
    }

    /**
     * Process second overdue reminder
     */
    protected function processSecondReminder($userrepo, $invoicerepo, $settings2) {

        Log::info("processing second invoice reminder", ['cronjobs.overdue.invoices.reminder', config('app.debug_ref'), basename(__FILE__), __line__]);

        //determine the cut off date
        $reminder_days = $settings2->settings2_invoices_second_reminder_days;
        $cutoff_date = \Carbon\Carbon::now()->subDays($reminder_days)->format('Y-m-d');

        /*------------------------------------------------------------------------------
         * Process invoices that are already mareked as overdue, overdue, or part-paid
         * They must already have had the first reminder sent
         * *---------------------------------------------------------------------------*/
        $invoices = \App\Models\Invoice::where('bill_due_date', '<', $cutoff_date)
            ->where('bill_overdue_reminder_sent', 'yes')
            ->where('bill_overdue_reminder_second_sent', 'no')
            ->whereIn('bill_status', [2, 3, 4])
            ->take(5)
            ->get();

        foreach ($invoices as $invoice) {

            if ($bills = $invoicerepo->search($invoice->bill_invoiceid)) {
                $bill = $bills->first();
            }

            //send email - only do this for invoices with an amount due
            if ($bill->invoice_balance > 0) {
                if ($user = $userrepo->getClientAccountOwner($invoice->bill_clientid)) {
                    $mail = new \App\Mail\OverdueInvoice($user, [], $bill);
                    $mail->build();
                }
            }

            $invoice->bill_overdue_reminder_second_sent = 'yes';
            $invoice->save();
        }
    }

    /**
     * Process third overdue reminder
     */
    protected function processThirdReminder($userrepo, $invoicerepo, $settings2) {

        Log::info("processing third invoice reminder", ['cronjobs.overdue.invoices.reminder', config('app.debug_ref'), basename(__FILE__), __line__]);

        $reminder_days = $settings2->settings2_invoices_third_reminder_days;
        $cutoff_date = \Carbon\Carbon::now()->subDays($reminder_days)->format('Y-m-d');


        /*------------------------------------------------------------------------------
         * Process invoices that are already mareked as overdue, overdue, or part-paid
         * They must already have had the first and second reminder sent
         * *---------------------------------------------------------------------------*/
        $invoices = \App\Models\Invoice::where('bill_due_date', '<', $cutoff_date)
            ->where('bill_overdue_reminder_sent', 'yes')
            ->where('bill_overdue_reminder_second_sent', 'yes')
            ->where('bill_overdue_reminder_third_sent', 'no')
            ->where('bill_status', 3)
            ->take(5)
            ->get();

        foreach ($invoices as $invoice) {

            if ($bills = $invoicerepo->search($invoice->bill_invoiceid)) {
                $bill = $bills->first();
            }

            //send email - only do this for invoices with an amount due
            if ($bill->invoice_balance > 0) {
                if ($user = $userrepo->getClientAccountOwner($invoice->bill_clientid)) {
                    $mail = new \App\Mail\OverdueInvoice($user, [], $bill);
                    $mail->build();
                }
            }

            $invoice->bill_overdue_reminder_third_sent = 'yes';
            $invoice->save();
        }
    }
}