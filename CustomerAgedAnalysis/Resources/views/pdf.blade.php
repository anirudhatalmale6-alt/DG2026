<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Customer Aged Analysis</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: sans-serif; font-size: 9px; color: #333;
        padding: 15px 20px;
    }

    /* Header */
    .header-table { width: 100%; margin-bottom: 12px; }
    .header-table td { vertical-align: middle; }
    .header-logo img { max-height: 45px; }
    .header-title {
        text-align: center; font-size: 16px; font-weight: bold;
        color: #0d3d56; letter-spacing: 2px;
    }
    .header-sub { text-align: center; font-size: 9px; color: #666; margin-top: 2px; }
    .header-company {
        text-align: right; font-size: 9px; color: #555; line-height: 1.5;
    }
    .header-company strong { font-size: 10px; color: #0d3d56; }

    /* Info bar */
    .info-bar {
        background: #f0fafb; border-left: 3px solid #17A2B8;
        padding: 6px 12px; margin-bottom: 10px; font-size: 9px;
    }
    .info-bar table { width: 100%; }
    .info-bar td { padding: 2px 8px; }

    /* Section header */
    .section-header {
        background: linear-gradient(135deg, #0d3d56, #17A2B8);
        color: #fff; padding: 5px 10px; font-size: 10px;
        font-weight: bold; letter-spacing: 0.5px; margin-bottom: 0;
    }

    /* Main table */
    .aged-table { width: 100%; border-collapse: collapse; font-size: 8px; }
    .aged-table th {
        background: #0d3d56; color: #fff; font-weight: bold;
        padding: 6px 8px; border: 1px solid #0a2e40;
        text-align: left; font-size: 7px;
        text-transform: uppercase; letter-spacing: 0.3px;
    }
    .aged-table th.text-right { text-align: right; }
    .aged-table td {
        padding: 5px 8px; border: 1px solid #dde4ec;
        vertical-align: middle;
    }
    .aged-table td.amount {
        text-align: right; font-weight: 500; white-space: nowrap;
    }
    .aged-table tr:nth-child(even) { background: #fafbfc; }
    .aged-table tr.client-row td { font-weight: 600; }

    /* Detail rows */
    .aged-table tr.detail-header td {
        background: #e8f0f8; font-weight: bold; font-size: 7px;
        text-transform: uppercase; letter-spacing: 0.3px; color: #0d3d56;
        padding: 4px 8px 4px 20px;
    }
    .aged-table tr.detail-row td {
        font-size: 7px; color: #555; padding: 3px 8px 3px 20px;
        border-bottom: 1px solid #eef2f7;
    }

    /* Grand total */
    .aged-table tr.grand-total td {
        background: #0d3d56; color: #fff; font-weight: bold;
        font-size: 9px; padding: 8px; border-color: #0a2e40;
    }

    /* Color classes */
    .amt-current { color: #155724; }
    .amt-30 { color: #856404; }
    .amt-60 { color: #c0392b; }
    .amt-90 { color: #721c24; font-weight: bold; }

    /* Footer */
    .footer {
        margin-top: 12px; padding-top: 6px;
        border-top: 2px solid #17A2B8;
        font-size: 7px; color: #999;
    }
    .footer table { width: 100%; }
</style>
</head>
<body>

@php
    $currency = $settings['settings_system_currency_symbol'] ?? 'R ';
    $prefix = $settings['settings_invoices_prefix'] ?? 'INV-';
    $isDetailed = ($mode ?? 'summary') === 'detailed';

    function fmtCurrency($amount, $symbol = 'R ') {
        return $symbol . number_format(abs($amount), 2, '.', ' ');
    }
    function fmtDate($date) {
        if (empty($date)) return '';
        try { return \Carbon\Carbon::parse($date)->format('d/m/Y'); }
        catch (\Exception $e) { return $date; }
    }
@endphp

{{-- HEADER --}}
<table class="header-table">
<tr>
    <td style="width:150px;" class="header-logo">
        @if(!empty($logoBase64))
            <img src="{{ $logoBase64 }}" alt="Logo">
        @endif
    </td>
    <td>
        <div class="header-title">CUSTOMER AGED ANALYSIS</div>
        <div class="header-sub">Outstanding Balances by Aging Bucket</div>
    </td>
    <td style="width:200px;" class="header-company">
        <strong>{{ $settings['settings_company_name'] ?? '' }}</strong>
    </td>
</tr>
</table>

{{-- INFO BAR --}}
<div class="info-bar">
<table>
<tr>
    <td><strong>As of Date:</strong> {{ fmtDate($as_of_date) }}</td>
    <td><strong>Clients with Balances:</strong> {{ count($clients) }}</td>
    <td style="text-align:right;"><strong>Total Outstanding:</strong> {{ fmtCurrency($grand_totals['total'], $currency) }}</td>
</tr>
</table>
</div>

{{-- SECTION HEADER --}}
<div class="section-header">Aged Balances{{ $isDetailed ? ' (Detailed)' : ' (Summary)' }}</div>

{{-- MAIN TABLE --}}
<table class="aged-table">
<thead>
<tr>
    <th style="width:90px;">Client Code</th>
    <th>Client Name</th>
    <th class="text-right" style="width:100px;">Current</th>
    <th class="text-right" style="width:100px;">30 Days</th>
    <th class="text-right" style="width:100px;">60 Days</th>
    <th class="text-right" style="width:100px;">90+ Days</th>
    <th class="text-right" style="width:110px;">Total</th>
</tr>
</thead>
<tbody>
@forelse($clients as $c)
    <tr class="client-row">
        <td style="color:#17A2B8;font-weight:600;">{{ $c['client_code'] ?: '-' }}</td>
        <td style="font-weight:600;">{{ $c['client_name'] }}</td>
        <td class="amount {{ $c['current'] > 0 ? 'amt-current' : '' }}">{{ $c['current'] > 0 ? fmtCurrency($c['current'], $currency) : '-' }}</td>
        <td class="amount {{ $c['30_days'] > 0 ? 'amt-30' : '' }}">{{ $c['30_days'] > 0 ? fmtCurrency($c['30_days'], $currency) : '-' }}</td>
        <td class="amount {{ $c['60_days'] > 0 ? 'amt-60' : '' }}">{{ $c['60_days'] > 0 ? fmtCurrency($c['60_days'], $currency) : '-' }}</td>
        <td class="amount {{ $c['90_plus'] > 0 ? 'amt-90' : '' }}">{{ $c['90_plus'] > 0 ? fmtCurrency($c['90_plus'], $currency) : '-' }}</td>
        <td class="amount" style="font-weight:700;">{{ fmtCurrency($c['total'], $currency) }}</td>
    </tr>

    @if($isDetailed && !empty($c['invoices']))
        <tr class="detail-header">
            <td>Invoice #</td>
            <td>Invoice Date</td>
            <td>Due Date</td>
            <td>Amount</td>
            <td>Paid</td>
            <td>Outstanding</td>
            <td>Days O/D</td>
        </tr>
        @foreach($c['invoices'] as $inv)
            @php
                $invRef = $inv['invoice_reference'];
                if (is_numeric($invRef)) {
                    $invRef = $prefix . str_pad($invRef, 6, '0', STR_PAD_LEFT);
                }
                $bucketClass = '';
                if ($inv['bucket'] === 'current') $bucketClass = 'amt-current';
                elseif ($inv['bucket'] === '30_days') $bucketClass = 'amt-30';
                elseif ($inv['bucket'] === '60_days') $bucketClass = 'amt-60';
                elseif ($inv['bucket'] === '90_plus') $bucketClass = 'amt-90';
            @endphp
            <tr class="detail-row">
                <td style="color:#17A2B8;">{{ $invRef }}</td>
                <td>{{ fmtDate($inv['invoice_date']) }}</td>
                <td>{{ fmtDate($inv['due_date']) }}</td>
                <td class="amount">{{ fmtCurrency($inv['amount'], $currency) }}</td>
                <td class="amount" style="color:#28a745;">{{ fmtCurrency($inv['payments'], $currency) }}</td>
                <td class="amount {{ $bucketClass }}">{{ fmtCurrency($inv['outstanding'], $currency) }}</td>
                <td style="text-align:right;">{{ $inv['days_overdue'] }}</td>
            </tr>
        @endforeach
    @endif
@empty
    <tr><td colspan="7" style="text-align:center;padding:20px;color:#999;">No clients with outstanding balances found.</td></tr>
@endforelse

{{-- GRAND TOTALS --}}
<tr class="grand-total">
    <td colspan="2" style="text-align:right;">Grand Total ({{ count($clients) }} clients):</td>
    <td class="amount">{{ fmtCurrency($grand_totals['current'], $currency) }}</td>
    <td class="amount">{{ fmtCurrency($grand_totals['30_days'], $currency) }}</td>
    <td class="amount">{{ fmtCurrency($grand_totals['60_days'], $currency) }}</td>
    <td class="amount">{{ fmtCurrency($grand_totals['90_plus'], $currency) }}</td>
    <td class="amount">{{ fmtCurrency($grand_totals['total'], $currency) }}</td>
</tr>
</tbody>
</table>

{{-- FOOTER --}}
<div class="footer">
<table>
<tr>
    <td>{{ $settings['settings_company_name'] ?? '' }}</td>
    <td style="text-align:center;">Customer Aged Analysis</td>
    <td style="text-align:right;">Generated: {{ fmtDate(now()->format('Y-m-d')) }}</td>
</tr>
</table>
</div>

</body>
</html>
