<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #333; line-height: 1.4; }

    /* Use tables for layout - DomPDF handles tables better than floats */

    .layout-table { width: 100%; border-collapse: collapse; }
    .layout-table td { vertical-align: top; border: none; padding: 0; }

    .sars-header-table {
        width: 100%;
        border-collapse: collapse;
        background: #003366;
        color: #fff;
    }
    .sars-header-table td {
        padding: 14px 20px;
        vertical-align: middle;
        border: none;
    }

    .body-content { padding: 16px 20px; }

    .client-name { font-weight: bold; font-size: 12px; color: #003366; margin-bottom: 6px; }
    .client-addr { font-size: 10px; color: #333; line-height: 1.6; }

    .details-box { border: 1px solid #c0d0e0; padding: 8px 12px; font-size: 10px; }
    .details-box table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .details-box table td { border: none; padding: 3px 0; border-bottom: 1px solid #e8eef4; }
    .details-box table tr:last-child td { border-bottom: none; }
    .detail-label { color: #003366; font-weight: bold; width: 130px; }
    .detail-value { text-align: right; }

    .section-header {
        background: #003366;
        color: #fff;
        padding: 6px 12px;
        font-size: 10px;
        font-weight: bold;
        margin-top: 12px;
        margin-bottom: 0;
    }

    table.data-table { width: 100%; border-collapse: collapse; font-size: 9.5px; }

    .summary-table td { padding: 4px 10px; border: 1px solid #c0d0e0; }
    .summary-table .amount { text-align: right; font-weight: normal; }
    .summary-table .closing td { background: #e8eef4; font-weight: bold; color: #003366; border-top: 2px solid #003366; }

    .status-table th { background: #e8eef4; color: #003366; font-weight: bold; padding: 4px 8px; border: 1px solid #c0d0e0; text-align: center; font-size: 9px; }
    .status-table td { padding: 4px 8px; border: 1px solid #c0d0e0; text-align: center; }
    .status-active { color: #28a745; font-weight: bold; }
    .status-not-reg { color: #dc3545; font-weight: bold; font-size: 8px; }

    .txn-table th { background: #003366; color: #fff; font-weight: bold; padding: 5px 6px; border: 1px solid #002244; text-align: center; font-size: 9px; }
    .txn-table th.sub { background: #e8eef4; color: #003366; }
    .txn-table td { padding: 3px 6px; border: 1px solid #dde4ec; font-weight: normal; }
    .txn-table td.amount { text-align: right; font-weight: normal; white-space: nowrap; }
    .txn-table tr.period-bal td { background: #e8eef4; font-weight: bold; color: #003366; border-top: 2px solid #003366; border-bottom: 2px solid #003366; }
    .txn-table tr.cumulative td { background: #d4e5f7; font-weight: bold; color: #003366; border-top: 3px solid #003366; font-size: 10px; }
    .txn-table tr.total-liab td { background: #f5f7fa; font-weight: bold; border-top: 1px solid #99aabb; }
    .txn-table tr.fin-move td { color: #666; }

    .aging-table th { background: #003366; color: #fff; font-weight: bold; padding: 5px 10px; border: 1px solid #002244; text-align: center; }
    .aging-table td { padding: 4px 10px; border: 1px solid #c0d0e0; text-align: right; font-weight: normal; }

    .compliance-table td { padding: 6px 10px; border: 1px solid #c0d0e0; }
    .compliance-label { font-weight: bold; color: #003366; width: 180px; }

    .neg { color: #dc3545; }

    .sars-footer-table {
        width: 100%;
        border-collapse: collapse;
        border-top: 2px solid #003366;
        background: #f5f7fa;
    }
    .sars-footer-table td {
        padding: 8px 20px;
        font-size: 8px;
        color: #666;
        border: none;
    }
</style>
</head>
<body>

{{-- HEADER using table layout --}}
<table class="sars-header-table">
    <tr>
        <td style="width:30%;">
            <div style="font-size:20px; font-weight:bold; letter-spacing:3px; color:#fff;">SARS</div>
            <div style="font-size:9px; color:#aaccee;">South African Revenue Service</div>
        </td>
        <td style="width:40%; text-align:center;">
            <div style="font-size:13px; font-weight:bold; letter-spacing:2px; color:#fff;">EMPLOYMENT TAXES</div>
            <div style="font-size:9px; color:#aaccee;">Statement of Account</div>
        </td>
        <td style="width:30%; text-align:right;">
            <div style="font-size:18px; font-weight:bold; letter-spacing:2px; color:#fff;">EMPSA</div>
        </td>
    </tr>
</table>

{{-- BODY --}}
<div class="body-content">

    {{-- Client info using table layout --}}
    <table class="layout-table" style="margin-bottom:14px;">
        <tr>
            <td style="width:50%; padding-right:12px;">
                <div class="client-name">{{ $data['client']['company_name'] }}</div>
                <div class="client-addr">
                    @if(!empty($data['client']['address']))
                        @php $addr = $data['client']['address']; @endphp
                        @if(!empty($addr['street_number']) || !empty($addr['street_name']))
                            {{ trim(($addr['street_number'] ?? '') . ' ' . ($addr['street_name'] ?? '')) }}<br>
                        @endif
                        @if(!empty($addr['suburb']) || !empty($addr['postal_code']))
                            {{ $addr['suburb'] ?? '' }}{{ !empty($addr['suburb']) && !empty($addr['postal_code']) ? ', ' : '' }}{{ $addr['postal_code'] ?? '' }}<br>
                        @endif
                        @if(!empty($addr['city']) || !empty($addr['province']))
                            {{ $addr['city'] ?? '' }}{{ !empty($addr['city']) && !empty($addr['province']) ? ', ' : '' }}{{ $addr['province'] ?? '' }}
                        @endif
                    @endif
                </div>
            </td>
            <td style="width:50%; padding-left:12px;">
                <div class="details-box">
                    <table>
                        <tr><td class="detail-label">Reference number:</td><td class="detail-value">{{ $data['client']['paye_number'] ?: 'N/A' }}</td></tr>
                        <tr><td class="detail-label">Date:</td><td class="detail-value">{{ $data['today'] }}</td></tr>
                        <tr><td class="detail-label">Statement period:</td><td class="detail-value">{{ ($data['tax_year'] - 1) }}/03/01 to {{ $data['tax_year'] }}/02/28</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- SUMMARY --}}
    <div class="section-header">Summary Information: Employer Reconciliation</div>
    <table class="data-table summary-table">
        <tr><td>PAYE/SDL/UIF YEAR {{ $data['tax_year'] - 1 }}</td><td class="amount">{{ number_format($data['summary']['prev_year_balance'], 2, '.', ' ') }}</td></tr>
        <tr><td>PAYE/SDL/UIF YEAR {{ $data['tax_year'] }}</td><td class="amount">{{ number_format($data['summary']['current_year_balance'], 2, '.', ' ') }}</td></tr>
        <tr><td>UNALLOCATED PAYMENTS</td><td class="amount">0.00</td></tr>
        <tr class="closing"><td>CLOSING BALANCE</td><td class="amount">{{ number_format($data['summary']['closing_balance'], 2, '.', ' ') }}</td></tr>
    </table>

    {{-- STATUS --}}
    <div class="section-header">Status Information</div>
    <table class="data-table status-table">
        <thead>
            <tr><th colspan="2">PAYE</th><th colspan="2">SDL</th><th colspan="2">UIF</th></tr>
            <tr><th>Status</th><th>Effective Date</th><th>Status</th><th>Effective Date</th><th>Status</th><th>Effective Date</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="{{ !empty($data['client']['paye_number']) ? 'status-active' : 'status-not-reg' }}">{{ !empty($data['client']['paye_number']) ? 'ACTIVE' : 'NOT REGISTERED' }}</span></td>
                <td>{{ $data['client']['tax_reg_date'] ?? '' }}</td>
                <td><span class="{{ !empty($data['client']['sdl_number']) ? 'status-active' : 'status-not-reg' }}">{{ !empty($data['client']['sdl_number']) ? 'ACTIVE' : 'NOT REGISTERED' }}</span></td>
                <td>{{ $data['client']['tax_reg_date'] ?? '' }}</td>
                <td><span class="{{ !empty($data['client']['uif_number']) ? 'status-active' : 'status-not-reg' }}">{{ !empty($data['client']['uif_number']) ? 'ACTIVE' : 'NOT REGISTERED' }}</span></td>
                <td>{{ $data['client']['tax_reg_date'] ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- TRANSACTION DETAILS --}}
    <div class="section-header">Transaction Details</div>
    <table class="data-table txn-table">
        <thead>
            <tr>
                <th style="width:60px;">Date</th>
                <th style="width:95px;">Transaction<br>Reference</th>
                <th>Transaction Description</th>
                <th style="width:75px;">Transaction<br>Value</th>
                <th style="width:60px;">PAYE</th>
                <th style="width:60px;">SDL</th>
                <th style="width:60px;">UIF</th>
                <th style="width:60px;">OTHER</th>
                <th style="width:70px;">Account<br>Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $cumPaye = 0; $cumSdl = 0; $cumUif = 0; $cumOther = 0; $cumBal = 0;
            @endphp
            @foreach($data['periods'] as $period)
                @foreach($period['transactions'] as $txn)
                    @php
                        $rowClass = '';
                        if ($txn['type'] === 'total_liability') $rowClass = 'total-liab';
                        if ($txn['type'] === 'financial_movement') $rowClass = 'fin-move';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $txn['date'] ?? '' }}</td>
                        <td style="font-size:8px;">{{ $txn['reference'] ?? '' }}</td>
                        <td @if(stripos($txn['description'], 'PAYMENT') !== false) style="color:#d32f2f;font-weight:600;" @endif>{{ $txn['description'] }}</td>
                        <td class="amount">{!! $txn['value'] < 0 ? '<span class="neg">-' . number_format(abs($txn['value']), 2, '.', ' ') . '</span>' : number_format($txn['value'], 2, '.', ' ') !!}</td>
                        <td class="amount">{!! $txn['paye'] < 0 ? '<span class="neg">-' . number_format(abs($txn['paye']), 2, '.', ' ') . '</span>' : number_format($txn['paye'], 2, '.', ' ') !!}</td>
                        <td class="amount">{!! $txn['sdl'] < 0 ? '<span class="neg">-' . number_format(abs($txn['sdl']), 2, '.', ' ') . '</span>' : number_format($txn['sdl'], 2, '.', ' ') !!}</td>
                        <td class="amount">{!! $txn['uif'] < 0 ? '<span class="neg">-' . number_format(abs($txn['uif']), 2, '.', ' ') . '</span>' : number_format($txn['uif'], 2, '.', ' ') !!}</td>
                        <td class="amount">{!! ($txn['other'] ?? 0) < 0 ? '<span class="neg">-' . number_format(abs($txn['other'] ?? 0), 2, '.', ' ') . '</span>' : number_format($txn['other'] ?? 0, 2, '.', ' ') !!}</td>
                        <td class="amount">{!! $txn['balance'] < 0 ? '<span class="neg">-' . number_format(abs($txn['balance']), 2, '.', ' ') . '</span>' : number_format($txn['balance'], 2, '.', ' ') !!}</td>
                    </tr>
                @endforeach
                {{-- Period balance --}}
                <tr class="period-bal">
                    <td colspan="4">BALANCE: TAX PERIOD {{ $period['period_label'] }}</td>
                    <td class="amount">{!! $period['balance_paye'] < 0 ? '<span class="neg">-' . number_format(abs($period['balance_paye']), 2, '.', ' ') . '</span>' : number_format($period['balance_paye'], 2, '.', ' ') !!}</td>
                    <td class="amount">{!! $period['balance_sdl'] < 0 ? '<span class="neg">-' . number_format(abs($period['balance_sdl']), 2, '.', ' ') . '</span>' : number_format($period['balance_sdl'], 2, '.', ' ') !!}</td>
                    <td class="amount">{!! $period['balance_uif'] < 0 ? '<span class="neg">-' . number_format(abs($period['balance_uif']), 2, '.', ' ') . '</span>' : number_format($period['balance_uif'], 2, '.', ' ') !!}</td>
                    <td class="amount">{!! ($period['balance_other'] ?? 0) < 0 ? '<span class="neg">-' . number_format(abs($period['balance_other'] ?? 0), 2, '.', ' ') . '</span>' : number_format($period['balance_other'] ?? 0, 2, '.', ' ') !!}</td>
                    <td class="amount">{!! $period['balance_total'] < 0 ? '<span class="neg">-' . number_format(abs($period['balance_total']), 2, '.', ' ') . '</span>' : number_format($period['balance_total'], 2, '.', ' ') !!}</td>
                </tr>
                @php
                    $cumPaye += $period['balance_paye'];
                    $cumSdl += $period['balance_sdl'];
                    $cumUif += $period['balance_uif'];
                    $cumOther += ($period['balance_other'] ?? 0);
                    $cumBal += $period['balance_total'];
                @endphp
            @endforeach
            {{-- Cumulative --}}
            <tr class="cumulative">
                <td colspan="4"><strong>CUMULATIVE BALANCE</strong></td>
                <td class="amount">{!! $cumPaye < 0 ? '<span class="neg">-' . number_format(abs($cumPaye), 2, '.', ' ') . '</span>' : number_format($cumPaye, 2, '.', ' ') !!}</td>
                <td class="amount">{!! $cumSdl < 0 ? '<span class="neg">-' . number_format(abs($cumSdl), 2, '.', ' ') . '</span>' : number_format($cumSdl, 2, '.', ' ') !!}</td>
                <td class="amount">{!! $cumUif < 0 ? '<span class="neg">-' . number_format(abs($cumUif), 2, '.', ' ') . '</span>' : number_format($cumUif, 2, '.', ' ') !!}</td>
                <td class="amount">{!! $cumOther < 0 ? '<span class="neg">-' . number_format(abs($cumOther), 2, '.', ' ') . '</span>' : number_format($cumOther, 2, '.', ' ') !!}</td>
                <td class="amount">{!! $cumBal < 0 ? '<span class="neg">-' . number_format(abs($cumBal), 2, '.', ' ') . '</span>' : number_format($cumBal, 2, '.', ' ') !!}</td>
            </tr>
        </tbody>
    </table>

    {{-- AGING --}}
    <div class="section-header">Ageing - Transactions are aged according to the original due date</div>
    <table class="data-table aging-table">
        <thead><tr><th>Current</th><th>30 Days</th><th>60 Days</th><th>90 Days</th><th>120 Days</th><th>Total</th></tr></thead>
        <tbody><tr>
            <td>{{ number_format($data['aging']['current'], 2, '.', ' ') }}</td>
            <td>{{ number_format($data['aging']['days30'], 2, '.', ' ') }}</td>
            <td>{{ number_format($data['aging']['days60'], 2, '.', ' ') }}</td>
            <td>{{ number_format($data['aging']['days90'], 2, '.', ' ') }}</td>
            <td>{{ number_format($data['aging']['days120'], 2, '.', ' ') }}</td>
            <td><strong>{{ number_format($data['aging']['total'], 2, '.', ' ') }}</strong></td>
        </tr></tbody>
    </table>

    {{-- COMPLIANCE --}}
    <div class="section-header">Compliance Information</div>
    <table class="data-table compliance-table">
        <tr><td class="compliance-label">Active SDL Reference</td><td>Seta Code (SDL)</td><td>{{ $data['client']['sdl_number'] ?: 'N/A' }}</td></tr>
        <tr><td class="compliance-label">Outstanding EMP201</td><td colspan="2">{{ $data['compliance']['outstanding_emp201'] ?? 'None' }}</td></tr>
    </table>

</div>

{{-- FOOTER --}}
<table class="sars-footer-table">
    <tr>
        <td style="width:33%;">Reference: {{ $data['client']['paye_number'] ?? '' }}</td>
        <td style="width:33%; text-align:center;">EMPSOA_RO</td>
        <td style="width:33%; text-align:right;">{{ $data['today'] }}</td>
    </tr>
</table>

</body>
</html>
