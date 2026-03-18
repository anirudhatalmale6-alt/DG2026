<?php

namespace Modules\CIMSAppointments\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Modules\CIMSAppointments\Models\Appointment;
use Modules\CIMSAppointments\Models\AppointmentSetting;

class AppointmentEmailService
{
    /**
     * Send confirmation email to client when appointment is booked.
     */
    public function sendConfirmation(Appointment $appointment): bool
    {
        if (!AppointmentSetting::getValue('confirmation_email_enabled', '1')) {
            return false;
        }

        if (empty($appointment->client_email)) {
            Log::info('AppointmentEmail: No client email for appointment #' . $appointment->id);
            return false;
        }

        $settings = AppointmentSetting::getAllSettings();
        $companyName = $settings['company_name'] ?? 'ATP Services';
        $companyPhone = $settings['company_phone'] ?? '';
        $companyAddress = $settings['company_address'] ?? '';

        $subject = 'Appointment Confirmation - ' . $companyName;

        $bodyHtml = $this->buildEmailHtml(
            'Appointment Confirmed',
            $this->buildConfirmationBody($appointment, $companyName, $companyPhone, $companyAddress),
            $companyName
        );

        return $this->sendEmail(
            $appointment->client_email,
            $appointment->client_name ?? 'Client',
            $subject,
            $bodyHtml,
            $appointment
        );
    }

    /**
     * Send reminder email to client before appointment.
     */
    public function sendReminder(Appointment $appointment): bool
    {
        if (!AppointmentSetting::getValue('reminder_email_enabled', '1')) {
            return false;
        }

        if (empty($appointment->client_email)) {
            return false;
        }

        $settings = AppointmentSetting::getAllSettings();
        $companyName = $settings['company_name'] ?? 'ATP Services';
        $companyPhone = $settings['company_phone'] ?? '';
        $companyAddress = $settings['company_address'] ?? '';

        $subject = 'Appointment Reminder - ' . $companyName;

        $bodyHtml = $this->buildEmailHtml(
            'Appointment Reminder',
            $this->buildReminderBody($appointment, $companyName, $companyPhone, $companyAddress),
            $companyName
        );

        return $this->sendEmail(
            $appointment->client_email,
            $appointment->client_name ?? 'Client',
            $subject,
            $bodyHtml,
            $appointment,
            'reminder'
        );
    }

    /**
     * Send cancellation email to client.
     */
    public function sendCancellation(Appointment $appointment): bool
    {
        if (!AppointmentSetting::getValue('cancellation_email_enabled', '1')) {
            return false;
        }

        if (empty($appointment->client_email)) {
            return false;
        }

        $settings = AppointmentSetting::getAllSettings();
        $companyName = $settings['company_name'] ?? 'ATP Services';
        $companyPhone = $settings['company_phone'] ?? '';

        $subject = 'Appointment Cancelled - ' . $companyName;

        $bodyHtml = $this->buildEmailHtml(
            'Appointment Cancelled',
            $this->buildCancellationBody($appointment, $companyName, $companyPhone),
            $companyName
        );

        return $this->sendEmail(
            $appointment->client_email,
            $appointment->client_name ?? 'Client',
            $subject,
            $bodyHtml,
            $appointment,
            'cancellation'
        );
    }

    /**
     * Process all pending reminders.
     */
    public function processReminders(): int
    {
        $hoursBeforeDefault = (int) AppointmentSetting::getValue('reminder_hours_before', 24);
        $appointments = Appointment::needingReminder($hoursBeforeDefault)->get();

        $sentCount = 0;
        foreach ($appointments as $appointment) {
            $appointment->load(['staff', 'service']);
            if ($this->sendReminder($appointment)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    // --- Private Helpers ---

    private function sendEmail(string $toEmail, string $toName, string $subject, string $bodyHtml, Appointment $appointment, string $type = 'confirmation'): bool
    {
        try {
            Mail::html($bodyHtml, function ($message) use ($toEmail, $subject) {
                $message->to($toEmail);
                $message->subject($subject);
            });

            // Update tracking timestamp
            if ($type === 'confirmation') {
                $appointment->update(['confirmation_sent_at' => now()]);
            } elseif ($type === 'reminder') {
                $appointment->update(['reminder_sent_at' => now()]);
            }

            Log::info('AppointmentEmail: ' . ucfirst($type) . ' sent to ' . $toEmail . ' for appointment #' . $appointment->id);
            return true;
        } catch (\Exception $e) {
            Log::error('AppointmentEmail: Failed to send ' . $type . ' to ' . $toEmail . ': ' . $e->getMessage());
            return false;
        }
    }

    private function buildConfirmationBody(Appointment $appointment, string $companyName, string $companyPhone, string $companyAddress): string
    {
        $staffName = $appointment->staff ? $appointment->staff->name : 'Our Team';
        $serviceName = $appointment->service ? $appointment->service->name : 'Consultation';

        $html = '<p>Dear ' . htmlspecialchars($appointment->client_name ?? 'Client') . ',</p>';
        $html .= '<p>Your appointment has been confirmed. Here are the details:</p>';
        $html .= '<table style="width:100%;border-collapse:collapse;margin:20px 0;">';
        $html .= $this->tableRow('Service', $serviceName);
        $html .= $this->tableRow('Date', $appointment->getFormattedDate());
        $html .= $this->tableRow('Time', $appointment->getFormattedTime());
        $html .= $this->tableRow('Duration', $appointment->duration_hours . ' hour(s)');
        $html .= $this->tableRow('Consultant', $staffName);

        if ($appointment->is_chargeable && $appointment->amount > 0) {
            $html .= $this->tableRow('Amount', 'R ' . number_format($appointment->amount, 2));
        }

        $html .= '</table>';

        if ($companyAddress) {
            $html .= '<p><strong>Location:</strong> ' . htmlspecialchars($companyAddress) . '</p>';
        }

        if ($appointment->notes) {
            $html .= '<p><strong>Notes:</strong> ' . htmlspecialchars($appointment->notes) . '</p>';
        }

        $html .= '<p>If you need to reschedule or cancel, please contact us at ' . htmlspecialchars($companyPhone) . '.</p>';
        $html .= '<p>We look forward to meeting with you.</p>';
        $html .= '<p>Kind regards,<br>' . htmlspecialchars($companyName) . '</p>';

        return $html;
    }

    private function buildReminderBody(Appointment $appointment, string $companyName, string $companyPhone, string $companyAddress): string
    {
        $staffName = $appointment->staff ? $appointment->staff->name : 'Our Team';
        $serviceName = $appointment->service ? $appointment->service->name : 'Consultation';

        $html = '<p>Dear ' . htmlspecialchars($appointment->client_name ?? 'Client') . ',</p>';
        $html .= '<p>This is a friendly reminder about your upcoming appointment:</p>';
        $html .= '<table style="width:100%;border-collapse:collapse;margin:20px 0;">';
        $html .= $this->tableRow('Service', $serviceName);
        $html .= $this->tableRow('Date', $appointment->getFormattedDate());
        $html .= $this->tableRow('Time', $appointment->getFormattedTime());
        $html .= $this->tableRow('Consultant', $staffName);
        $html .= '</table>';

        if ($companyAddress) {
            $html .= '<p><strong>Location:</strong> ' . htmlspecialchars($companyAddress) . '</p>';
        }

        $html .= '<p>If you need to reschedule or cancel, please contact us at ' . htmlspecialchars($companyPhone) . '.</p>';
        $html .= '<p>We look forward to seeing you.</p>';
        $html .= '<p>Kind regards,<br>' . htmlspecialchars($companyName) . '</p>';

        return $html;
    }

    private function buildCancellationBody(Appointment $appointment, string $companyName, string $companyPhone): string
    {
        $serviceName = $appointment->service ? $appointment->service->name : 'Consultation';

        $html = '<p>Dear ' . htmlspecialchars($appointment->client_name ?? 'Client') . ',</p>';
        $html .= '<p>We regret to inform you that your appointment has been cancelled:</p>';
        $html .= '<table style="width:100%;border-collapse:collapse;margin:20px 0;">';
        $html .= $this->tableRow('Service', $serviceName);
        $html .= $this->tableRow('Date', $appointment->getFormattedDate());
        $html .= $this->tableRow('Time', $appointment->getFormattedTime());
        $html .= '</table>';

        if ($appointment->cancellation_reason) {
            $html .= '<p><strong>Reason:</strong> ' . htmlspecialchars($appointment->cancellation_reason) . '</p>';
        }

        $html .= '<p>If you would like to rebook, please contact us at ' . htmlspecialchars($companyPhone) . '.</p>';
        $html .= '<p>We apologise for any inconvenience.</p>';
        $html .= '<p>Kind regards,<br>' . htmlspecialchars($companyName) . '</p>';

        return $html;
    }

    private function buildEmailHtml(string $title, string $body, string $companyName): string
    {
        return '<!DOCTYPE html>'
            . '<html lang="en">'
            . '<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
            . '<title>' . htmlspecialchars($title) . '</title></head>'
            . '<body style="margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#333;background-color:#f5f5f5;">'
            . '<div style="max-width:600px;margin:20px auto;background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);">'
            // Header
            . '<div style="background:linear-gradient(135deg, #0d3d56, #17A2B8);padding:25px 30px;text-align:center;">'
            . '<h1 style="color:#ffffff;margin:0;font-size:22px;">' . htmlspecialchars($title) . '</h1>'
            . '<p style="color:rgba(255,255,255,0.8);margin:5px 0 0;font-size:13px;">' . htmlspecialchars($companyName) . '</p>'
            . '</div>'
            // Body
            . '<div style="padding:30px;">'
            . $body
            . '</div>'
            // Footer
            . '<div style="background:#f8f9fa;padding:15px 30px;text-align:center;font-size:12px;color:#888;">'
            . '<p style="margin:0;">This is an automated email from ' . htmlspecialchars($companyName) . '. Please do not reply to this email.</p>'
            . '</div>'
            . '</div>'
            . '</body></html>';
    }

    private function tableRow(string $label, string $value): string
    {
        return '<tr>'
            . '<td style="padding:10px 15px;border-bottom:1px solid #eee;font-weight:600;color:#555;width:140px;">' . htmlspecialchars($label) . '</td>'
            . '<td style="padding:10px 15px;border-bottom:1px solid #eee;color:#333;">' . htmlspecialchars($value) . '</td>'
            . '</tr>';
    }
}
