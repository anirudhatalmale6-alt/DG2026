<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Statement of Account - {{ $client->client_company_name ?? 'Client' }}</title>
    <style>
        /* ============================================================
           PDF Statement - Following EMPSA table-based layout pattern
           Uses tables for all layout (DomPDF handles tables reliably)
           ============================================================ */

        @page {
            margin: 15mm 25mm 15mm 25mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Verdana, Geneva, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        /* Layout tables - no borders, just for positioning */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }
        .layout-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        /* Data tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        /* Page break prevention */
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>

    @php
        $currency = $settings['settings_system_currency_symbol'] ?? 'R ';

        $fmt = function($amount) use ($currency) {
            return $currency . number_format($amount, 2, '.', ' ');
        };

        $fmtDate = function($date) {
            if (empty($date)) return '';
            try {
                return \Carbon\Carbon::parse($date)->format('d/m/Y');
            } catch (\Exception $e) {
                return $date;
            }
        };
    @endphp

    <!-- ============================================================
         HEADER: Logo left, Company right (table-based like EMPSA)
         ============================================================ -->
    <table class="layout-table" style="margin-bottom: 8px;">
        <tr>
            <td style="width: 30%; vertical-align: top;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 40px; max-width: 120px;">
                @endif
            </td>
            <td style="width: 70%; text-align: right; word-wrap: break-word;">
                <div style="font-size: 9px; font-weight: bold; color: #0d3d56; margin-bottom: 2px;">{{ $settings['settings_company_name'] }}</div>
                <div style="font-size: 7px; color: #555; line-height: 1.5;">
                    {{ $settings['settings_company_address_line_1'] }}<br>
                    {{ $settings['settings_company_city'] }}, {{ $settings['settings_company_state'] }}<br>
                    {{ $settings['settings_company_zipcode'] }}, {{ $settings['settings_company_country'] }}<br>
                    {{ $settings['settings_company_customfield_1'] }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Divider -->
    <div style="border-top: 3px solid #17A2B8; margin: 6px 0 10px 0;"></div>

    <!-- ============================================================
         TITLE BANNER
         ============================================================ -->
    <table class="layout-table" style="background: #17A2B8; margin-bottom: 12px;">
        <tr>
            <td style="text-align: center; padding: 8px 10px;">
                <div style="font-size: 16px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: #fff;">Statement of Account</div>
                <div style="font-size: 9px; color: #e0f7fa; margin-top: 3px;">Period: {{ $fmtDate($from_date) }} to {{ $fmtDate($to_date) }}</div>
            </td>
        </tr>
    </table>

    <!-- ============================================================
         CLIENT INFO + STATEMENT META (table-based like EMPSA)
         ============================================================ -->
    <table class="layout-table" style="margin-bottom: 12px;">
        <tr>
            <td style="width: 55%; padding-right: 10px;">
                <div style="background: #f8f9fa; border: 1px solid #e0e0e0; padding: 10px 12px;">
                    <div style="font-size: 8px; font-weight: bold; color: #17A2B8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Bill To:</div>
                    <div style="font-size: 10px; font-weight: bold; color: #0d3d56; margin-bottom: 3px;">{{ $client->client_company_name }}</div>
                    <div style="font-size: 8px; color: #555; line-height: 1.5;">
                        @if($client_code)
                            Client Code: {{ $client_code }}<br>
                        @endif
                        @php
                            $line1Parts = [];
                            if($client->client_billing_street) $line1Parts[] = $client->client_billing_street;
                            if($client->client_billing_zip) $line1Parts[] = $client->client_billing_zip;
                            $line2Parts = [];
                            if($client->client_billing_city) $line2Parts[] = $client->client_billing_city;
                            if($client->client_billing_state) $line2Parts[] = $client->client_billing_state;
                            if($client->client_billing_country) $line2Parts[] = $client->client_billing_country;
                        @endphp
                        @if(count($line1Parts))
                            {{ implode(', ', $line1Parts) }}<br>
                        @endif
                        @if(count($line2Parts))
                            {{ implode(', ', $line2Parts) }}
                        @endif
                        @if($client->client_vat)
                            <br>VAT: {{ $client->client_vat }}
                        @endif
                        @if($client->client_phone)
                            <br>Tel: {{ $client->client_phone }}
                        @endif
                        @if($client_email)
                            <br>Email: {{ $client_email }}
                        @endif
                    </div>
                </div>
            </td>
            <td style="width: 45%; padding-left: 10px;">
                <table class="layout-table" style="border: 1px solid #c0d0e0;">
                    <tr>
                        <td style="padding: 4px 8px; font-size: 8px; font-weight: bold; color: #555; border-bottom: 1px solid #e8eef4;">Statement Date:</td>
                        <td style="padding: 4px 8px; font-size: 8px; text-align: right; color: #333; font-weight: 600; border-bottom: 1px solid #e8eef4;">{{ $fmtDate(now()->format('Y-m-d')) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 8px; font-size: 8px; font-weight: bold; color: #555; border-bottom: 1px solid #e8eef4;">From:</td>
                        <td style="padding: 4px 8px; font-size: 8px; text-align: right; color: #333; font-weight: 600; border-bottom: 1px solid #e8eef4;">{{ $fmtDate($from_date) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 8px; font-size: 8px; font-weight: bold; color: #555; border-bottom: 1px solid #e8eef4;">To:</td>
                        <td style="padding: 4px 8px; font-size: 8px; text-align: right; color: #333; font-weight: 600; border-bottom: 1px solid #e8eef4;">{{ $fmtDate($to_date) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 8px; font-size: 9px; font-weight: bold; background: #0d3d56; color: #fff; text-align: right;">Balance Due:</td>
                        <td style="padding: 5px 8px; font-size: 9px; font-weight: bold; background: #0d3d56; color: #fff; text-align: right;">{{ $fmt($closing_balance) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ============================================================
         TRANSACTION TABLE (following EMPSA txn-table pattern)
         ============================================================ -->
    <table class="data-table" style="margin-bottom: 0;">
        <thead>
            <tr>
                <th style="width: 65px; background: #1a1a2e; color: #fff; padding: 6px 5px; font-size: 7px; font-weight: bold; text-transform: uppercase; text-align: left; border: 1px solid #111;">Date</th>
                <th style="width: 50px; background: #1a1a2e; color: #fff; padding: 6px 5px; font-size: 7px; font-weight: bold; text-transform: uppercase; text-align: left; border: 1px solid #111;">Type</th>
                <th style="width: 80px; background: #1a1a2e; color: #fff; padding: 6px 5px; font-size: 7px; font-weight: bold; text-transform: uppercase; text-align: left; border: 1px solid #111;">Reference</th>
                <th style="background: #1a1a2e; color: #fff; padding: 6px 5px; font-size: 7px; font-weight: bold; text-transform: uppercase; text-align: left; border: 1px solid #111;">Description</th>
                <th style="width: 70px; background: #1a1a2e; color: #fff; padding: 6px 5px; font-size: 7px; font-weight: bold; text-transform: uppercase; text-align: right; border: 1px solid #111;">Debit</th>
                <th style="width: 70px; background: #1a1a2e; color: #fff; padding: 6px 5px; font-size: 7px; font-weight: bold; text-transform: uppercase; text-align: right; border: 1px solid #111;">Credit</th>
                <th style="width: 75px; background: #1a1a2e; color: #fff; padding: 6px 5px; font-size: 7px; font-weight: bold; text-transform: uppercase; text-align: right; border: 1px solid #111;">Balance</th>
            </tr>
        </thead>
        <tbody>
            <!-- Opening Balance -->
            <tr style="background: #e8f4f8;">
                <td style="padding: 5px 5px; font-size: 9px; font-weight: bold; border-bottom: 2px solid #17A2B8; border-left: 1px solid #dde4ec;">{{ $fmtDate($from_date) }}</td>
                <td style="padding: 5px 5px; font-size: 9px; font-weight: bold; border-bottom: 2px solid #17A2B8;"></td>
                <td style="padding: 5px 5px; font-size: 9px; font-weight: bold; border-bottom: 2px solid #17A2B8;"></td>
                <td style="padding: 5px 5px; font-size: 9px; font-weight: bold; border-bottom: 2px solid #17A2B8; color: #0d3d56;">Opening Balance</td>
                <td style="padding: 5px 5px; font-size: 9px; font-weight: bold; border-bottom: 2px solid #17A2B8; text-align: right;"></td>
                <td style="padding: 5px 5px; font-size: 9px; font-weight: bold; border-bottom: 2px solid #17A2B8; text-align: right;"></td>
                <td style="padding: 5px 5px; font-size: 9px; font-weight: bold; border-bottom: 2px solid #17A2B8; text-align: right; border-right: 1px solid #dde4ec;">{{ $fmt($opening_balance) }}</td>
            </tr>

            @php $rowIndex = 0; @endphp
            @foreach($transactions as $txn)
            <tr style="{{ $rowIndex % 2 == 0 ? 'background: #fafbfc;' : '' }}">
                <td style="padding: 5px 5px; font-size: 9px; border-bottom: 1px solid #e9ecef; border-left: 1px solid #dde4ec;">{{ $fmtDate($txn['date']) }}</td>
                <td style="padding: 5px 5px; font-size: 9px; border-bottom: 1px solid #e9ecef;">
                    <span style="color: {{ $txn['type'] === 'Invoice' ? '#17A2B8' : '#28a745' }}; font-weight: bold;">{{ $txn['type'] }}</span>
                </td>
                <td style="padding: 5px 5px; font-size: 9px; border-bottom: 1px solid #e9ecef;">{{ $txn['reference'] }}</td>
                <td style="padding: 5px 5px; font-size: 9px; border-bottom: 1px solid #e9ecef;">{{ $txn['description'] }}</td>
                <td style="padding: 5px 5px; font-size: 9px; border-bottom: 1px solid #e9ecef; text-align: right; white-space: nowrap;">{{ $txn['debit'] > 0 ? $fmt($txn['debit']) : '' }}</td>
                <td style="padding: 5px 5px; font-size: 9px; border-bottom: 1px solid #e9ecef; text-align: right; white-space: nowrap;">{{ $txn['credit'] > 0 ? $fmt($txn['credit']) : '' }}</td>
                <td style="padding: 5px 5px; font-size: 9px; border-bottom: 1px solid #e9ecef; text-align: right; white-space: nowrap; border-right: 1px solid #dde4ec;">{{ $fmt($txn['balance']) }}</td>
            </tr>
            @php $rowIndex++; @endphp
            @endforeach

            <!-- Totals row -->
            <tr style="background: #f0f0f0; border-top: 2px solid #333;">
                <td colspan="4" style="padding: 6px 5px; font-size: 9px; font-weight: bold; text-align: right; border-top: 2px solid #333; border-left: 1px solid #dde4ec;">Totals:</td>
                <td style="padding: 6px 5px; font-size: 9px; font-weight: bold; text-align: right; border-top: 2px solid #333; white-space: nowrap;">{{ $fmt($total_debits) }}</td>
                <td style="padding: 6px 5px; font-size: 9px; font-weight: bold; text-align: right; border-top: 2px solid #333; white-space: nowrap;">{{ $fmt($total_credits) }}</td>
                <td style="padding: 6px 5px; font-size: 9px; border-top: 2px solid #333; border-right: 1px solid #dde4ec;"></td>
            </tr>

            <!-- Closing balance -->
            <tr style="background: #0d3d56;">
                <td colspan="6" style="padding: 8px 10px; font-size: 10px; font-weight: bold; color: #fff; text-align: right; border: 1px solid #0a2e40;">Closing Balance:</td>
                <td style="padding: 8px 10px; font-size: 10px; font-weight: bold; color: #fff; text-align: right; white-space: nowrap; border: 1px solid #0a2e40;">{{ $fmt($closing_balance) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- ============================================================
         AGING SUMMARY (following EMPSA aging-table pattern)
         ============================================================ -->
    @if(isset($aging))
    <div class="no-break" style="margin-top: 14px;">
        <div style="background: #17A2B8; color: #fff; padding: 6px 12px; font-size: 10px; font-weight: bold; margin-bottom: 0;">Aging Summary</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="background: #17A2B8; color: #fff; padding: 6px 6px; text-align: center; font-weight: bold; font-size: 7px; text-transform: uppercase; border: 1px solid #138496;">90+ Days</th>
                    <th style="background: #17A2B8; color: #fff; padding: 6px 6px; text-align: center; font-weight: bold; font-size: 7px; text-transform: uppercase; border: 1px solid #138496;">60 Days (61-90)</th>
                    <th style="background: #17A2B8; color: #fff; padding: 6px 6px; text-align: center; font-weight: bold; font-size: 7px; text-transform: uppercase; border: 1px solid #138496;">30 Days (31-60)</th>
                    <th style="background: #17A2B8; color: #fff; padding: 6px 6px; text-align: center; font-weight: bold; font-size: 7px; text-transform: uppercase; border: 1px solid #138496;">Current (0-30 Days)</th>
                    <th style="background: #17A2B8; color: #fff; padding: 6px 6px; text-align: center; font-weight: bold; font-size: 7px; text-transform: uppercase; border: 1px solid #138496;">Total Outstanding</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px 6px; text-align: center; font-weight: bold; font-size: 9px; border: 1px solid #e0e0e0; background: #f8d7da; color: #721c24;">{{ $fmt($aging['buckets']['90_plus']) }}</td>
                    <td style="padding: 8px 6px; text-align: center; font-weight: bold; font-size: 9px; border: 1px solid #e0e0e0; background: #fce4d6; color: #c0392b;">{{ $fmt($aging['buckets']['60_days']) }}</td>
                    <td style="padding: 8px 6px; text-align: center; font-weight: bold; font-size: 9px; border: 1px solid #e0e0e0; background: #fff3cd; color: #856404;">{{ $fmt($aging['buckets']['30_days']) }}</td>
                    <td style="padding: 8px 6px; text-align: center; font-weight: bold; font-size: 9px; border: 1px solid #e0e0e0; background: #d4edda; color: #155724;">{{ $fmt($aging['buckets']['current']) }}</td>
                    <td style="padding: 8px 6px; text-align: center; font-weight: bold; font-size: 11px; border: 1px solid #e0e0e0; background: #0d3d56; color: #fff;">{{ $fmt($aging['total']) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- ============================================================
         BANKING DETAILS
         ============================================================ -->
    <div class="no-break" style="margin-top: 14px; padding-top: 8px; border-top: 2px solid #17A2B8;">
        <div style="font-size: 10px; font-weight: bold; color: #0d3d56; margin-bottom: 6px;">Banking Details</div>
        @if(!empty($bankingBase64))
            <img src="{{ $bankingBase64 }}" alt="Banking Details" style="max-width: 220px;">
        @else
            <p style="font-size: 9px; color: #666;">Please contact us for banking details.</p>
        @endif
    </div>

    <!-- ============================================================
         FOOTER (table-based like EMPSA)
         ============================================================ -->
    <table class="layout-table" style="margin-top: 14px; border-top: 1px solid #e0e0e0;">
        <tr>
            <td style="padding: 6px 0; font-size: 7px; color: #888;">{{ $settings['settings_company_name'] }}</td>
            <td style="padding: 6px 0; font-size: 7px; color: #888; text-align: center;">{{ $settings['settings_company_customfield_1'] }}</td>
            <td style="padding: 6px 0; font-size: 7px; color: #888; text-align: right;">Generated: {{ $fmtDate(now()->format('Y-m-d')) }}. E&amp;OE.</td>
        </tr>
    </table>

</body>
</html>
