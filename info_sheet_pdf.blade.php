<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page {
        size: A4 portrait;
        margin: 10mm 12mm 10mm 12mm;
    }
    body {
        font-family: 'Helvetica', sans-serif;
        font-size: 10px;
        color: #333;
        margin: 0;
        padding: 0;
    }

    /* Page border */
    .page-border {
        border: 2px solid #000;
        padding: 0;
    }

    /* Header banner image */
    .header-img {
        width: 100%;
        display: block;
    }

    /* Company name & address bar */
    .company-bar {
        padding: 8px 14px 4px;
    }
    .company-name {
        font-size: 15px;
        font-weight: bold;
        color: #1a1a1a;
        margin: 0;
    }
    .company-address {
        font-size: 10px;
        color: #555;
        margin: 2px 0 0;
    }
    .client-code {
        font-size: 18px;
        font-weight: bold;
        color: #d6006e;
        text-align: right;
        padding-right: 14px;
        vertical-align: bottom;
    }

    /* Section card */
    .card {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 8px 14px;
        overflow: hidden;
    }
    .card-header {
        background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
        background-color: #148f9f;
        padding: 6px 12px;
        color: #fff;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-body {
        padding: 10px 12px;
    }

    /* Field tables */
    .field-table {
        width: 100%;
        border-collapse: collapse;
    }
    .field-table td {
        vertical-align: top;
        padding: 2px 3px;
    }
    .field-label {
        font-size: 9px;
        font-weight: bold;
        color: #1a3c4d;
        padding-bottom: 1px;
    }
    .field-value {
        border: 1.5px solid #148f9f;
        border-radius: 3px;
        padding: 5px 8px;
        font-size: 10px;
        color: #333;
        background: #fff;
        min-height: 14px;
    }
    .field-value.empty {
        color: #bbb;
        font-style: italic;
    }
    .field-date-tag {
        color: #888;
        font-size: 9px;
    }

    /* EMP201 Table */
    .emp-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8px;
    }
    .emp-table thead th {
        background-color: #f0f0f0;
        padding: 4px 3px;
        font-weight: bold;
        color: #1a3c4d;
        text-align: left;
        border-bottom: 2px solid #148f9f;
        font-size: 7px;
        text-transform: uppercase;
    }
    .emp-table thead th.r { text-align: right; }
    .emp-table thead th.c { text-align: center; }
    .emp-table tbody td {
        padding: 3px 3px;
        border-bottom: 1px solid #eee;
    }
    .emp-table tbody td.r { text-align: right; }
    .emp-table tbody td.c { text-align: center; }
    .emp-table tbody tr.outstanding { background-color: #fff5f5; }
    .emp-table tfoot td {
        padding: 4px 3px;
        font-weight: bold;
        border-top: 2px solid #148f9f;
        background-color: #f0f0f0;
        color: #1a3c4d;
        font-size: 8px;
    }
    .emp-table tfoot td.r { text-align: right; }

    /* Status badges */
    .badge {
        display: inline-block;
        padding: 1px 5px;
        border-radius: 2px;
        font-size: 7px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .badge-paid { background: #e3f2fd; color: #1565c0; }
    .badge-filed { background: #e8f5e9; color: #2e7d32; }
    .badge-outstanding { background: #fce4ec; color: #c62828; }

    /* Summary cards */
    .summary-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .summary-table td {
        text-align: center;
        padding: 6px 4px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        width: 20%;
    }
    .summary-value {
        font-size: 14px;
        font-weight: bold;
        line-height: 1.2;
    }
    .summary-label {
        font-size: 7px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #666;
        margin-top: 2px;
    }
    .sc-green { background: #e8f5e9; }
    .sc-green .summary-value { color: #2e7d32; }
    .sc-red { background: #fce4ec; }
    .sc-red .summary-value { color: #c62828; }
    .sc-blue { background: #e3f2fd; }
    .sc-blue .summary-value { color: #1565c0; }
    .sc-amber { background: #fff8e1; }
    .sc-amber .summary-value { color: #f57f17; }

    /* Compliance status in table */
    .status-yes {
        color: #2e7d32;
        font-weight: bold;
        font-size: 10px;
    }
    .status-no {
        color: #c62828;
        font-weight: bold;
        font-size: 10px;
    }

    /* Footer */
    .footer {
        background-color: #148f9f;
        padding: 6px 14px;
        color: #fff;
        font-size: 9px;
    }
    .footer-brand {
        font-weight: bold;
        font-size: 10px;
    }
    .footer-meta {
        font-size: 7px;
        opacity: 0.8;
    }

    /* Page break */
    .page-break {
        page-break-before: always;
    }
</style>
</head>
<body>

{{-- ═══════════════════════════════════════════ --}}
{{-- PAGE 1: Company Details & Director Details --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="page-border">

    {{-- Header Image --}}
    @if($headerImgBase64)
    <img src="{{ $headerImgBase64 }}" class="header-img">
    @endif

    {{-- Company Name, Address & Client Code --}}
    <table style="width:100%; padding:0;">
        <tr>
            <td class="company-bar" style="width:70%;">
                <div class="company-name">{{ $client->company_name ?: '—' }}</div>
                @if(isset($address))
                <div class="company-address">
                    {{ collect([$address->street_number, $address->street_name, $address->suburb, $address->postal_code, $address->city, $address->province])->filter()->implode(', ') }}
                </div>
                @endif
            </td>
            <td class="client-code" style="width:30%;">
                {{ $client->client_code ?: '—' }}
            </td>
        </tr>
    </table>

    {{-- COMPANY REGISTRATION CARD --}}
    <div class="card">
        <div class="card-header">:: Company Registration</div>
        <div class="card-body">
            {{-- Row 1: Trading Name / Reg No / Reg Date --}}
            <table class="field-table">
                <tr>
                    <td style="width:50%;">
                        <div class="field-label">Trading Name</div>
                        <div class="field-value {{ empty($client->trading_name) ? 'empty' : '' }}">{{ $client->trading_name ?: '—' }}</div>
                    </td>
                    <td style="width:25%;">
                        <div class="field-label">Company Reg No</div>
                        <div class="field-value {{ empty($client->company_reg_number) ? 'empty' : '' }}">{{ $client->company_reg_number ?: '—' }}</div>
                    </td>
                    <td style="width:25%;">
                        <div class="field-label">Reg Date</div>
                        <div class="field-value {{ empty($client->company_reg_date) ? 'empty' : '' }}">{{ $client->company_reg_date ? \Carbon\Carbon::parse($client->company_reg_date)->format('d F Y') : '—' }}</div>
                    </td>
                </tr>
            </table>
            <div style="height:6px;"></div>
            {{-- Row 2: Tax / VAT / PAYE with dates --}}
            <table class="field-table">
                <tr>
                    <td style="width:33.3%;">
                        <div class="field-label">Company Tax Number</div>
                        <div class="field-value {{ empty($client->tax_number) ? 'empty' : '' }}">@if($client->tax_number){{ $client->tax_number }} @if($client->tax_reg_date)<span class="field-date-tag">[ {{ \Carbon\Carbon::parse($client->tax_reg_date)->format('d M Y') }} ]</span>@endif @else — @endif</div>
                    </td>
                    <td style="width:33.3%;">
                        <div class="field-label">VAT Number</div>
                        <div class="field-value {{ empty($client->vat_number) ? 'empty' : '' }}">@if($client->vat_number){{ $client->vat_number }} @if($client->vat_reg_date)<span class="field-date-tag">[ {{ \Carbon\Carbon::parse($client->vat_reg_date)->format('d M Y') }} ]</span>@endif @else — @endif</div>
                    </td>
                    <td style="width:33.3%;">
                        <div class="field-label">PAYE Number</div>
                        <div class="field-value {{ empty($client->paye_number) ? 'empty' : '' }}">@if($client->paye_number){{ $client->paye_number }} @if($client->payroll_liability_date)<span class="field-date-tag">[ {{ \Carbon\Carbon::parse($client->payroll_liability_date)->format('d M Y') }} ]</span>@endif @else — @endif</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- DIRECTOR DETAILS CARD --}}
    <div class="card">
        <div class="card-header">:: Director Details</div>
        <div class="card-body">
            @if(isset($director) && $director)
            <table class="field-table">
                <tr>
                    <td style="width:25%;">
                        <div class="field-label">Title</div>
                        <div class="field-value {{ empty($director->title) ? 'empty' : '' }}">{{ $director->title ?: '—' }}</div>
                    </td>
                    <td style="width:25%;">
                        <div class="field-label">Surname</div>
                        <div class="field-value {{ empty($director->surname) ? 'empty' : '' }}">{{ $director->surname ?: '—' }}</div>
                    </td>
                    <td style="width:25%;">
                        <div class="field-label">Middle Name</div>
                        <div class="field-value {{ empty($director->middlename) ? 'empty' : '' }}">{{ $director->middlename ?: '—' }}</div>
                    </td>
                    <td style="width:25%;">
                        <div class="field-label">First Name</div>
                        <div class="field-value {{ empty($director->firstname) ? 'empty' : '' }}">{{ $director->firstname ?: '—' }}</div>
                    </td>
                </tr>
            </table>
            <div style="height:6px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:25%;">
                        <div class="field-label">Tel</div>
                        <div class="field-value {{ empty($director->office_phone) ? 'empty' : '' }}">{{ $director->office_phone ?: '—' }}</div>
                    </td>
                    <td style="width:25%;">
                        <div class="field-label">Mobile</div>
                        <div class="field-value {{ empty($director->mobile_phone) ? 'empty' : '' }}">{{ $director->mobile_phone ?: '—' }}</div>
                    </td>
                    <td style="width:25%;">
                        <div class="field-label">WhatsApp</div>
                        <div class="field-value {{ empty($director->whatsapp_number) ? 'empty' : '' }}">{{ $director->whatsapp_number ?: '—' }}</div>
                    </td>
                    <td style="width:25%;">
                        <div class="field-label">Email</div>
                        <div class="field-value {{ empty($director->email) ? 'empty' : '' }}">{{ $director->email ?: '—' }}</div>
                    </td>
                </tr>
            </table>
            @else
            <p style="color:#999;font-style:italic;">No director details available for this client.</p>
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <table style="width:100%;">
        <tr>
            <td class="footer" style="width:60%;">
                <div class="footer-brand">{{ config('app.name', 'SmartWeigh') }}</div>
                <div class="footer-meta">Client Information Management</div>
            </td>
            <td class="footer" style="width:40%;text-align:right;">
                <div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div>
                <div class="footer-meta">{{ now()->format('F Y') }} | Generated by {{ auth()->user()->first_name ?? 'System' }} {{ auth()->user()->last_name ?? '' }}</div>
            </td>
        </tr>
    </table>

</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- PAGE 2: EMP201 Compliance Dashboard            --}}
{{-- ═══════════════════════════════════════════════ --}}
@if(isset($emp201Years) && $emp201Years->count() > 0)
<div class="page-break"></div>
<div class="page-border">

    {{-- Header Image --}}
    @if($headerImgBase64)
    <img src="{{ $headerImgBase64 }}" class="header-img">
    @endif

    {{-- Company bar repeated for page 2 --}}
    <table style="width:100%;padding:0;">
        <tr>
            <td class="company-bar" style="width:70%;">
                <div class="company-name">{{ $client->company_name ?: '—' }}</div>
            </td>
            <td class="client-code" style="width:30%;">
                {{ $client->client_code ?: '—' }}
            </td>
        </tr>
    </table>

    {{-- EMP201 COMPLIANCE CARD --}}
    <div class="card">
        <div class="card-header">:: EMP201 Compliance Dashboard — Tax Year {{ $selectedYear }} (Mar {{ $selectedYear - 1 }} - Feb {{ $selectedYear }})</div>
        <div class="card-body">

            @php
                $periodLookup = [];
                foreach($emp201Data as $dec) {
                    $suffix = substr($dec->payment_period, 4, 2);
                    if(!isset($periodLookup[$suffix])) {
                        $periodLookup[$suffix] = $dec;
                    }
                }

                $totalPaye = 0; $totalSdl = 0; $totalUif = 0; $totalPenalty = 0; $totalPayable = 0; $totalPaid = 0;
                $filedCount = 0; $outstandingCount = 0;

                foreach($periodMonths as $key => $monthLabel) {
                    if(isset($periodLookup[$key])) {
                        $d = $periodLookup[$key];
                        $totalPaye += $d->paye_payable;
                        $totalSdl += $d->sdl_payable;
                        $totalUif += $d->uif_payable;
                        $totalPenalty += $d->penalty_interest ?? 0;
                        $totalPayable += $d->payroll_liability;
                        $totalPaid += $d->amount_paid ?? 0;
                        $filedCount++;
                    } else {
                        $outstandingCount++;
                    }
                }
                $isCompliant = $outstandingCount == 0;
            @endphp

            {{-- Summary --}}
            <table class="summary-table">
                <tr>
                    <td class="sc-green">
                        <div class="summary-value">{{ $filedCount }} / 12</div>
                        <div class="summary-label">Returns Filed</div>
                    </td>
                    <td class="sc-red">
                        <div class="summary-value">{{ $outstandingCount }}</div>
                        <div class="summary-label">Outstanding</div>
                    </td>
                    <td class="sc-blue">
                        <div class="summary-value">R {{ number_format($totalPayable, 2) }}</div>
                        <div class="summary-label">Total Declared</div>
                    </td>
                    <td class="sc-amber">
                        <div class="summary-value">R {{ number_format($totalPenalty, 2) }}</div>
                        <div class="summary-label">Penalties</div>
                    </td>
                    <td class="{{ $isCompliant ? 'sc-green' : 'sc-red' }}">
                        @if($isCompliant && $greenBadgeBase64)
                            <img src="{{ $greenBadgeBase64 }}" style="width:28px;height:28px;">
                        @elseif(!$isCompliant && $redBadgeBase64)
                            <img src="{{ $redBadgeBase64 }}" style="width:28px;height:28px;">
                        @else
                            <div class="summary-value">{{ $isCompliant ? 'YES' : 'NO' }}</div>
                        @endif
                        <div class="summary-label">{{ $isCompliant ? 'Compliant' : 'Non-Compliant' }}</div>
                    </td>
                </tr>
            </table>

            {{-- Monthly Table --}}
            <table class="emp-table">
                <thead>
                    <tr>
                        <th>Period</th>
                        <th>Status</th>
                        <th class="r">PAYE</th>
                        <th class="r">SDL</th>
                        <th class="r">UIF</th>
                        <th class="r">Penalty</th>
                        <th class="r">Total</th>
                        <th>Reference</th>
                        <th class="r">Paid</th>
                        <th class="c">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($periodMonths as $key => $monthLabel)
                    @php
                        $d = $periodLookup[$key] ?? null;
                        $isFiled = $d !== null;
                        $isPaid = $d && ($d->amount_paid ?? 0) > 0;
                        $isRowCompliant = $isFiled && $isPaid;
                    @endphp
                    <tr class="{{ !$isFiled ? 'outstanding' : '' }}">
                        <td><strong>{{ $monthLabel }}</strong></td>
                        <td>
                            @if($isFiled && $isPaid)
                                <span class="badge badge-paid">Paid</span>
                            @elseif($isFiled)
                                <span class="badge badge-filed">Filed</span>
                            @else
                                <span class="badge badge-outstanding">Outstanding</span>
                            @endif
                        </td>
                        <td class="r">{{ $d ? number_format($d->paye_payable, 2) : '—' }}</td>
                        <td class="r">{{ $d ? number_format($d->sdl_payable, 2) : '—' }}</td>
                        <td class="r">{{ $d ? number_format($d->uif_payable, 2) : '—' }}</td>
                        <td class="r">{{ $d ? number_format($d->penalty_interest ?? 0, 2) : '—' }}</td>
                        <td class="r"><strong>{{ $d ? number_format($d->payroll_liability, 2) : '—' }}</strong></td>
                        <td style="font-size:7px;">{{ $d->payment_reference ?? '—' }}</td>
                        <td class="r">{{ $d && ($d->amount_paid ?? 0) > 0 ? number_format($d->amount_paid, 2) : '—' }}</td>
                        <td class="c">
                            @if($isRowCompliant)
                                <span class="status-yes">YES</span>
                            @else
                                <span class="status-no">NO</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>TOTALS</strong></td>
                        <td class="r">{{ number_format($totalPaye, 2) }}</td>
                        <td class="r">{{ number_format($totalSdl, 2) }}</td>
                        <td class="r">{{ number_format($totalUif, 2) }}</td>
                        <td class="r">{{ number_format($totalPenalty, 2) }}</td>
                        <td class="r"><strong>{{ number_format($totalPayable, 2) }}</strong></td>
                        <td></td>
                        <td class="r"><strong>{{ number_format($totalPaid, 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>

    {{-- Footer --}}
    <table style="width:100%;">
        <tr>
            <td class="footer" style="width:60%;">
                <div class="footer-brand">{{ config('app.name', 'SmartWeigh') }}</div>
                <div class="footer-meta">Client Information Management</div>
            </td>
            <td class="footer" style="width:40%;text-align:right;">
                <div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div>
                <div class="footer-meta">{{ now()->format('F Y') }} | Generated by {{ auth()->user()->first_name ?? 'System' }} {{ auth()->user()->last_name ?? '' }}</div>
            </td>
        </tr>
    </table>

</div>
@endif

</body>
</html>
