<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; line-height: 1.4; }

    .layout-table { width: 100%; border-collapse: collapse; }
    .layout-table td { vertical-align: top; border: none; padding: 0; }

    .body-content { padding: 16px 20px; }

    table.data-table { width: 100%; border-collapse: collapse; font-size: 9px; }

    .no-break { page-break-inside: avoid; }

    .stmt-header-table {
        width: 100%;
        border-collapse: collapse;
        background: #17A2B8;
        color: #fff;
    }
    .stmt-header-table td {
        padding: 10px 20px;
        vertical-align: middle;
        border: none;
    }

    .stmt-footer-table {
        width: 100%;
        border-collapse: collapse;
        border-top: 2px solid #17A2B8;
        background: #f5f7fa;
    }
    .stmt-footer-table td {
        padding: 8px 20px;
        font-size: 7px;
        color: #666;
        border: none;
    }
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

    {{-- HEADER: Logo left, Company center, Statement badge right --}}
    <table class="stmt-header-table">
        <tr>
            <td style="width:30%;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 35px; max-width: 110px;">
                @endif
            </td>
            <td style="width:40%; text-align:center;">
                <div style="font-size:9px; font-weight:bold; color:#fff;">{{ $settings['settings_company_name'] }}</div>
                <div style="font-size:7px; color:#e0f7fa; margin-top:2px;">
                    {{ $settings['settings_company_address_line_1'] }}<br>
                    {{ $settings['settings_company_city'] }}, {{ $settings['settings_company_state'] }}, {{ $settings['settings_company_zipcode'] }}<br>
                    {{ $settings['settings_company_customfield_1'] }}
                </div>
            </td>
            <td style="width:30%; text-align:right;">
                <div style="font-size:14px; font-weight:bold; letter-spacing:2px; color:#fff;">STATEMENT</div>
            </td>
        </tr>
    </table>

    {{-- BODY --}}
    <div class="body-content">

        {{-- Title Banner --}}
        <div style="text-align:center; padding:6px 0; margin-bottom:10px; border-bottom:2px solid #17A2B8;">
            <div style="font-size:13px; font-weight:bold; letter-spacing:2px; color:#0d3d56;">STATEMENT OF ACCOUNT</div>
            <div style="font-size:8px; color:#666; margin-top:2px;">Period: {{ $fmtDate($from_date) }} to {{ $fmtDate($to_date) }}</div>
        </div>

        {{-- Client info + Statement meta --}}
        <table class="layout-table" style="margin-bottom:12px;">
            <tr>
                <td style="width:50%; padding-right:12px;">
                    <div style="font-size:8px; font-weight:bold; color:#17A2B8; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Bill To:</div>
                    <div style="font-size:10px; font-weight:bold; color:#0d3d56; margin-bottom:3px;">{{ $client->client_company_name }}</div>
                    <div style="font-size:8px; color:#555; line-height:1.6;">
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
                </td>
                <td style="width:50%; padding-left:12px;">
                    <table class="layout-table" style="border:1px solid #c0d0e0;">
                        <tr>
                            <td style="padding:4px 10px; font-size:8px; font-weight:bold; color:#555; border-bottom:1px solid #e8eef4;">Statement Date:</td>
                            <td style="padding:4px 10px; font-size:8px; text-align:right; color:#333; font-weight:600; border-bottom:1px solid #e8eef4;">{{ $fmtDate(now()->format('Y-m-d')) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 10px; font-size:8px; font-weight:bold; color:#555; border-bottom:1px solid #e8eef4;">From:</td>
                            <td style="padding:4px 10px; font-size:8px; text-align:right; color:#333; font-weight:600; border-bottom:1px solid #e8eef4;">{{ $fmtDate($from_date) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 10px; font-size:8px; font-weight:bold; color:#555; border-bottom:1px solid #e8eef4;">To:</td>
                            <td style="padding:4px 10px; font-size:8px; text-align:right; color:#333; font-weight:600; border-bottom:1px solid #e8eef4;">{{ $fmtDate($to_date) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:5px 10px; font-size:9px; font-weight:bold; background:#0d3d56; color:#fff; text-align:right;">Balance Due:</td>
                            <td style="padding:5px 10px; font-size:9px; font-weight:bold; background:#0d3d56; color:#fff; text-align:right;">{{ $fmt($closing_balance) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Transaction Table --}}
        <table class="data-table" style="margin-bottom:0;">
            <thead>
                <tr>
                    <th style="width:60px; background:#1a1a2e; color:#fff; padding:5px 4px; font-size:7px; font-weight:bold; text-transform:uppercase; text-align:left; border:1px solid #111;">Date</th>
                    <th style="width:45px; background:#1a1a2e; color:#fff; padding:5px 4px; font-size:7px; font-weight:bold; text-transform:uppercase; text-align:left; border:1px solid #111;">Type</th>
                    <th style="width:70px; background:#1a1a2e; color:#fff; padding:5px 4px; font-size:7px; font-weight:bold; text-transform:uppercase; text-align:left; border:1px solid #111;">Reference</th>
                    <th style="background:#1a1a2e; color:#fff; padding:5px 4px; font-size:7px; font-weight:bold; text-transform:uppercase; text-align:left; border:1px solid #111;">Description</th>
                    <th style="width:65px; background:#1a1a2e; color:#fff; padding:5px 4px; font-size:7px; font-weight:bold; text-transform:uppercase; text-align:right; border:1px solid #111;">Debit</th>
                    <th style="width:65px; background:#1a1a2e; color:#fff; padding:5px 4px; font-size:7px; font-weight:bold; text-transform:uppercase; text-align:right; border:1px solid #111;">Credit</th>
                    <th style="width:70px; background:#1a1a2e; color:#fff; padding:5px 4px; font-size:7px; font-weight:bold; text-transform:uppercase; text-align:right; border:1px solid #111;">Balance</th>
                </tr>
            </thead>
            <tbody>
                {{-- Opening Balance --}}
                <tr style="background:#e8f4f8;">
                    <td style="padding:4px 4px; font-size:8px; font-weight:bold; border-bottom:2px solid #17A2B8; border-left:1px solid #dde4ec;">{{ $fmtDate($from_date) }}</td>
                    <td style="padding:4px 4px; font-size:8px; font-weight:bold; border-bottom:2px solid #17A2B8;"></td>
                    <td style="padding:4px 4px; font-size:8px; font-weight:bold; border-bottom:2px solid #17A2B8;"></td>
                    <td style="padding:4px 4px; font-size:8px; font-weight:bold; border-bottom:2px solid #17A2B8; color:#0d3d56;">Opening Balance</td>
                    <td style="padding:4px 4px; font-size:8px; font-weight:bold; border-bottom:2px solid #17A2B8; text-align:right;"></td>
                    <td style="padding:4px 4px; font-size:8px; font-weight:bold; border-bottom:2px solid #17A2B8; text-align:right;"></td>
                    <td style="padding:4px 4px; font-size:8px; font-weight:bold; border-bottom:2px solid #17A2B8; text-align:right; border-right:1px solid #dde4ec;">{{ $fmt($opening_balance) }}</td>
                </tr>

                @php $rowIndex = 0; @endphp
                @foreach($transactions as $txn)
                <tr style="{{ $rowIndex % 2 == 0 ? 'background:#fafbfc;' : '' }}">
                    <td style="padding:4px 4px; font-size:8px; border-bottom:1px solid #e9ecef; border-left:1px solid #dde4ec;">{{ $fmtDate($txn['date']) }}</td>
                    <td style="padding:4px 4px; font-size:8px; border-bottom:1px solid #e9ecef;">
                        <span style="color:{{ $txn['type'] === 'Invoice' ? '#17A2B8' : '#28a745' }}; font-weight:bold;">{{ $txn['type'] }}</span>
                    </td>
                    <td style="padding:4px 4px; font-size:8px; border-bottom:1px solid #e9ecef;">{{ $txn['reference'] }}</td>
                    <td style="padding:4px 4px; font-size:8px; border-bottom:1px solid #e9ecef;">{{ $txn['description'] }}</td>
                    <td style="padding:4px 4px; font-size:8px; border-bottom:1px solid #e9ecef; text-align:right; white-space:nowrap;">{{ $txn['debit'] > 0 ? $fmt($txn['debit']) : '' }}</td>
                    <td style="padding:4px 4px; font-size:8px; border-bottom:1px solid #e9ecef; text-align:right; white-space:nowrap;">{{ $txn['credit'] > 0 ? $fmt($txn['credit']) : '' }}</td>
                    <td style="padding:4px 4px; font-size:8px; border-bottom:1px solid #e9ecef; text-align:right; white-space:nowrap; border-right:1px solid #dde4ec;">{{ $fmt($txn['balance']) }}</td>
                </tr>
                @php $rowIndex++; @endphp
                @endforeach

                {{-- Totals --}}
                <tr style="background:#f0f0f0;">
                    <td colspan="4" style="padding:5px 4px; font-size:8px; font-weight:bold; text-align:right; border-top:2px solid #333; border-left:1px solid #dde4ec;">Totals:</td>
                    <td style="padding:5px 4px; font-size:8px; font-weight:bold; text-align:right; border-top:2px solid #333; white-space:nowrap;">{{ $fmt($total_debits) }}</td>
                    <td style="padding:5px 4px; font-size:8px; font-weight:bold; text-align:right; border-top:2px solid #333; white-space:nowrap;">{{ $fmt($total_credits) }}</td>
                    <td style="padding:5px 4px; font-size:8px; border-top:2px solid #333; border-right:1px solid #dde4ec;"></td>
                </tr>

                {{-- Closing Balance --}}
                <tr style="background:#0d3d56;">
                    <td colspan="6" style="padding:6px 10px; font-size:9px; font-weight:bold; color:#fff; text-align:right; border:1px solid #0a2e40;">Closing Balance:</td>
                    <td style="padding:6px 10px; font-size:9px; font-weight:bold; color:#fff; text-align:right; white-space:nowrap; border:1px solid #0a2e40;">{{ $fmt($closing_balance) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Aging Summary --}}
        @if(isset($aging))
        <div class="no-break" style="margin-top:12px;">
            <div style="background:#17A2B8; color:#fff; padding:5px 10px; font-size:9px; font-weight:bold; margin-bottom:0;">Aging Summary</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="background:#17A2B8; color:#fff; padding:5px 4px; text-align:center; font-weight:bold; font-size:7px; text-transform:uppercase; border:1px solid #138496;">90+ Days</th>
                        <th style="background:#17A2B8; color:#fff; padding:5px 4px; text-align:center; font-weight:bold; font-size:7px; text-transform:uppercase; border:1px solid #138496;">60 Days</th>
                        <th style="background:#17A2B8; color:#fff; padding:5px 4px; text-align:center; font-weight:bold; font-size:7px; text-transform:uppercase; border:1px solid #138496;">30 Days</th>
                        <th style="background:#17A2B8; color:#fff; padding:5px 4px; text-align:center; font-weight:bold; font-size:7px; text-transform:uppercase; border:1px solid #138496;">Current</th>
                        <th style="background:#17A2B8; color:#fff; padding:5px 4px; text-align:center; font-weight:bold; font-size:7px; text-transform:uppercase; border:1px solid #138496;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:6px 4px; text-align:center; font-weight:bold; font-size:8px; border:1px solid #e0e0e0; background:#f8d7da; color:#721c24;">{{ $fmt($aging['buckets']['90_plus']) }}</td>
                        <td style="padding:6px 4px; text-align:center; font-weight:bold; font-size:8px; border:1px solid #e0e0e0; background:#fce4d6; color:#c0392b;">{{ $fmt($aging['buckets']['60_days']) }}</td>
                        <td style="padding:6px 4px; text-align:center; font-weight:bold; font-size:8px; border:1px solid #e0e0e0; background:#fff3cd; color:#856404;">{{ $fmt($aging['buckets']['30_days']) }}</td>
                        <td style="padding:6px 4px; text-align:center; font-weight:bold; font-size:8px; border:1px solid #e0e0e0; background:#d4edda; color:#155724;">{{ $fmt($aging['buckets']['current']) }}</td>
                        <td style="padding:6px 4px; text-align:center; font-weight:bold; font-size:9px; border:1px solid #e0e0e0; background:#0d3d56; color:#fff;">{{ $fmt($aging['total']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        {{-- Banking Details --}}
        <div class="no-break" style="margin-top:12px; padding-top:6px; border-top:2px solid #17A2B8;">
            <div style="font-size:9px; font-weight:bold; color:#0d3d56; margin-bottom:4px;">Banking Details</div>
            @if(!empty($bankingBase64))
                <img src="{{ $bankingBase64 }}" alt="Banking Details" style="max-width:180px;">
            @else
                <p style="font-size:8px; color:#666;">Please contact us for banking details.</p>
            @endif
        </div>

    </div>{{-- end body-content --}}

    {{-- FOOTER --}}
    <table class="stmt-footer-table">
        <tr>
            <td>{{ $settings['settings_company_name'] }}</td>
            <td style="text-align:center;">{{ $settings['settings_company_customfield_1'] }}</td>
            <td style="text-align:right;">Generated: {{ $fmtDate(now()->format('Y-m-d')) }}. E&amp;OE.</td>
        </tr>
    </table>

</body>
</html>
