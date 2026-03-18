<?php

namespace Modules\CustomerStatements\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class StatementMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $pdfContent;
    public string $pdfFilename;
    public string $clientName;
    public string $companyName;
    public string $periodFrom;
    public string $periodTo;
    public float $closingBalance;
    public string $currencySymbol;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $pdfContent,
        string $pdfFilename,
        string $clientName,
        string $companyName,
        string $periodFrom,
        string $periodTo,
        float $closingBalance,
        string $currencySymbol = 'R '
    ) {
        $this->pdfContent = $pdfContent;
        $this->pdfFilename = $pdfFilename;
        $this->clientName = $clientName;
        $this->companyName = $companyName;
        $this->periodFrom = $periodFrom;
        $this->periodTo = $periodTo;
        $this->closingBalance = $closingBalance;
        $this->currencySymbol = $currencySymbol;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Statement of Account - ' . $this->companyName . ' - ' . $this->periodFrom . ' to ' . $this->periodTo,
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $formattedBalance = $this->currencySymbol . number_format($this->closingBalance, 2, '.', ' ');

        return $this->subject('Statement of Account - ' . $this->companyName . ' - ' . $this->periodFrom . ' to ' . $this->periodTo)
            ->html($this->buildEmailBody($formattedBalance))
            ->attachData($this->pdfContent, $this->pdfFilename, [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Build the email HTML body.
     * Uses inline styles compatible with most email clients.
     *
     * @param string $formattedBalance
     * @return string
     */
    protected function buildEmailBody(string $formattedBalance): string
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Verdana, Geneva, sans-serif;">

    <!-- Outer container -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 30px 0;">
        <tr>
            <td align="center">

                <!-- Email card -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #17A2B8; padding: 25px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: bold; letter-spacing: 2px;">
                                STATEMENT OF ACCOUNT
                            </h1>
                            <p style="color: #e0f7fa; margin: 5px 0 0 0; font-size: 12px;">
                                ' . htmlspecialchars($this->periodFrom) . ' to ' . htmlspecialchars($this->periodTo) . '
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">

                            <p style="color: #333; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                                Dear ' . htmlspecialchars($this->clientName) . ',
                            </p>

                            <p style="color: #333; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                                Please find attached your Statement of Account from
                                <strong>' . htmlspecialchars($this->companyName) . '</strong>
                                for the period <strong>' . htmlspecialchars($this->periodFrom) . '</strong>
                                to <strong>' . htmlspecialchars($this->periodTo) . '</strong>.
                            </p>

                            <!-- Balance Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 20px 0;">
                                <tr>
                                    <td style="background-color: #0d3d56; border-radius: 6px; padding: 20px; text-align: center;">
                                        <p style="color: #e0f7fa; font-size: 11px; margin: 0 0 5px 0; text-transform: uppercase; letter-spacing: 1px;">
                                            Balance Due
                                        </p>
                                        <p style="color: #ffffff; font-size: 24px; font-weight: bold; margin: 0;">
                                            ' . htmlspecialchars($formattedBalance) . '
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #333; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                                The statement includes a detailed breakdown of all invoices and payments
                                during the selected period, along with an aging summary of any outstanding amounts.
                            </p>

                            <p style="color: #333; font-size: 13px; line-height: 1.6; margin-bottom: 20px;">
                                Should you have any queries regarding this statement, please do not hesitate to contact us.
                            </p>

                            <p style="color: #333; font-size: 13px; line-height: 1.6; margin-bottom: 5px;">
                                Kind regards,
                            </p>
                            <p style="color: #0d3d56; font-size: 13px; font-weight: bold; margin: 0;">
                                ' . htmlspecialchars($this->companyName) . '
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="color: #888; font-size: 10px; margin: 0; line-height: 1.6;">
                                ' . htmlspecialchars($this->companyName) . '<br>
                                This is an automated email. Please do not reply directly to this message.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>';
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->pdfFilename)
                ->withMime('application/pdf'),
        ];
    }
}
