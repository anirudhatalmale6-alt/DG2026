@extends('layouts.default')

@push('styles')
<style>
    /* ═══ INFO SHEET - Matches CIMS system styling ═══ */
    .is-page { padding: 20px 0 40px; max-width: 1100px; margin: 0 auto; }
    .is-form-border { border: 2px solid #000; }

    /* Client selector bar */
    .is-selector {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 16px 20px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .is-selector-row {
        display: flex;
        gap: 14px;
        align-items: flex-end;
    }
    .is-selector-col { flex: 1; min-width: 0; }
    .is-selector-col label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #1a3c4d;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .is-selector-col-btn { flex: 0 0 auto; }

    /* Header image banner */
    .is-header { position: relative; }
    .is-header img { width: 100%; display: block; }
    .is-header-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding: 14px 24px 0;
    }
    .is-header-overlay h1 {
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .is-header-overlay .is-company-name {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 90px 0 0;
    }
    .is-header-overlay .is-address-line {
        font-size: 14px;
        color: #444;
        margin: 4px 0 0;
    }
    .is-header-overlay .is-address-line i {
        margin-right: 6px;
        color: #148f9f;
    }

    /* Client code - right-aligned with content area */
    .is-code-badge {
        position: absolute;
        bottom: 12px;
        right: 200px;
        font-size: 26px;
        font-weight: 800;
        color: #d6006e;
        letter-spacing: 0.5px;
        text-align: right;
    }

    /* Section content area */
    .is-content { padding: 10px 24px 20px; }

    /* Section title - matches CIMS style: icon + bold text + teal line */
    .is-section-title {
        margin: 22px 0 14px;
        padding-bottom: 6px;
        border-bottom: 2px solid #148f9f;
    }
    .is-section-title:first-child { margin-top: 10px; }
    .is-section-title h3 {
        font-size: 15px;
        font-weight: 700;
        color: #1a3c4d;
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.5px;
    }
    .is-section-title h3 i {
        color: #148f9f;
        margin-right: 8px;
    }

    /* Field rows */
    .is-row {
        display: flex;
        gap: 14px;
        margin-bottom: 12px;
    }
    .is-col { flex: 1; min-width: 0; }
    .is-col-6 { flex: 0 0 calc(50% - 7px); max-width: calc(50% - 7px); min-width: 0; }
    .is-col-3 { flex: 0 0 calc(25% - 10.5px); max-width: calc(25% - 10.5px); min-width: 0; }

    /* Field label */
    .is-label {
        font-size: 13px;
        font-weight: 600;
        color: #1a3c4d;
        margin-bottom: 4px;
    }

    /* Field input box - teal border matching CIMS */
    .is-field {
        border: 1.5px solid #148f9f;
        border-radius: 3px;
        padding: 8px 12px;
        min-height: 38px;
        background: #fff;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .is-field.--empty {
        color: #bbb;
        font-weight: 400;
        font-style: italic;
    }

    /* Footer */
    .is-footer {
        background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
    }
    .is-footer-brand { font-size: 14px; font-weight: 700; }
    .is-footer-brand small { display: block; font-size: 10px; font-weight: 400; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; }
    .is-footer-right { text-align: right; }
    .is-footer-year { font-size: 32px; font-weight: 800; line-height: 1; }
    .is-footer-meta { font-size: 10px; opacity: 0.75; margin-top: 2px; }

    /* Card wrapper */
    .is-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .is-card .is-card-header {
        background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
        padding: 12px 20px;
    }
    .is-card .is-card-header h3 {
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.5px;
    }
    .is-card .is-card-header h3 i {
        margin-right: 8px;
    }
    .is-card .is-card-body {
        padding: 16px 20px;
    }

    /* EMP201 Dashboard */
    .is-card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .is-card-header-flex select {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.4);
        color: #fff;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .is-card-header-flex select option { color: #333; background: #fff; }

    .emp-summary {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }
    .emp-summary-card {
        flex: 1;
        padding: 12px 14px;
        border-radius: 6px;
        text-align: center;
        border: 1px solid #e0e0e0;
    }
    .emp-summary-card .emp-sc-value {
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
    }
    .emp-summary-card .emp-sc-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 2px;
        color: #666;
    }
    .emp-sc-green { background: #e8f5e9; }
    .emp-sc-green .emp-sc-value { color: #2e7d32; }
    .emp-sc-red { background: #fce4ec; }
    .emp-sc-red .emp-sc-value { color: #c62828; }
    .emp-sc-blue { background: #e3f2fd; }
    .emp-sc-blue .emp-sc-value { color: #1565c0; }
    .emp-sc-amber { background: #fff8e1; }
    .emp-sc-amber .emp-sc-value { color: #f57f17; }

    .emp-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .emp-table thead th {
        background: #f5f5f5;
        padding: 8px 10px;
        font-weight: 700;
        color: #1a3c4d;
        text-align: left;
        border-bottom: 2px solid #148f9f;
        font-size: 12px;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .emp-table thead th.text-right { text-align: right; }
    .emp-table thead th.text-center { text-align: center; }
    .emp-table tbody td {
        padding: 7px 10px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .emp-table tbody td.text-right { text-align: right; font-variant-numeric: tabular-nums; }
    .emp-table tbody td.text-center { text-align: center; }
    .emp-table tbody tr:hover { background: #f9fffe; }
    .emp-table tbody tr.--outstanding { background: #fff5f5; }
    .emp-table tfoot td {
        padding: 9px 10px;
        font-weight: 800;
        border-top: 2px solid #148f9f;
        background: #f5f5f5;
        color: #1a3c4d;
    }
    .emp-table tfoot td.text-right { text-align: right; font-variant-numeric: tabular-nums; }

    .emp-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .emp-badge.--filed { background: #e8f5e9; color: #2e7d32; }
    .emp-badge.--outstanding { background: #fce4ec; color: #c62828; }
    .emp-badge.--paid { background: #e3f2fd; color: #1565c0; }
    .emp-badge.--partial { background: #fff8e1; color: #f57f17; }

    .emp-compliance {
        display: inline-block;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        text-align: center;
        line-height: 22px;
        font-size: 12px;
        color: #fff;
    }
    .emp-compliance.--green { background: #2e7d32; }
    .emp-compliance.--red { background: #c62828; }

    /* Export buttons bar */
    .is-export-bar {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        padding: 12px 24px;
        background: #f8f9fa;
        border-top: 1px solid #e0e0e0;
    }

    /* Empty state */
    .is-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    .is-empty-state i {
        font-size: 48px;
        color: #ccc;
        margin-bottom: 12px;
        display: block;
    }
    .is-empty-state p {
        font-size: 16px;
        font-weight: 500;
    }

    /* Action buttons */
    .is-actions { margin-bottom: 12px; display: flex; gap: 8px; }

    /* Print */
    @media print {
        .is-actions, .is-selector, .is-export-bar, .page-titles, .header, .sidebar, .footer-content, .cims-master-menu { display: none !important; }
        body { background: #fff !important; }
        .is-page { padding: 0; }
        .is-form-border { border: none; }
    }
</style>
@endpush

@section('content')
<div class="is-page">

    {{-- ═══ CLIENT SELECTOR BAR ═══ --}}
    <div class="is-selector">
        <div class="is-selector-row">
            <div class="is-selector-col" style="flex:2;">
                <label><i class="fa fa-user me-1"></i> Select Client</label>
                <select id="isClientSelect" class="sd_drop_class" data-live-search="true" data-size="10" title="-- Select Client --" style="width:100%;">
                    <option value="">-- Select Client --</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->client_id }}" {{ (isset($client) && $client->client_id == $c->client_id) ? 'selected' : '' }}>{{ $c->client_code }} - {{ $c->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="is-selector-col">
                <label><i class="fa fa-calendar me-1"></i> Tax Year</label>
                <select id="isTaxYear" class="sd_drop_class" data-size="10" title="-- Tax Year --" style="width:100%;">
                    <option value="">-- All Years --</option>
                    @foreach($taxYears as $ty)
                    <option value="{{ $ty }}" {{ (isset($selectedYear) && $selectedYear == $ty) ? 'selected' : '' }}>{{ $ty }} (Mar {{ $ty - 1 }} - Feb {{ $ty }})</option>
                    @endforeach
                </select>
            </div>
            <div class="is-selector-col-btn">
                <button id="btnLoadInfoSheet" class="btn btn-primary" style="background:linear-gradient(135deg,#0e6977,#148f9f);border:none;padding:8px 20px;">
                    <i class="fa fa-search me-1"></i> Load
                </button>
            </div>
        </div>
    </div>

    @if(isset($client))
    {{-- ═══ INFO SHEET CONTENT (only when client selected) ═══ --}}
    <div id="infoSheetContent">

    <div class="is-form-border">
    {{-- ═══ HEADER IMAGE with company name & address ═══ --}}
    <div class="is-header">
        <img src="{{ asset('storage/assets/info_sheet_header.jpg') }}" alt="Header">
        <div class="is-header-overlay">
            <h1>Client Info Sheet</h1>
            <div class="is-company-name">{{ $client->company_name ?: '—' }}</div>
            @if(isset($address))
            <div class="is-address-line">
                <i class="fa fa-map-marker-alt"></i>
                {{ collect([$address->street_number, $address->street_name, $address->suburb, $address->postal_code, $address->city, $address->province])->filter()->implode(', ') }}
            </div>
            @endif
        </div>
        <div class="is-code-badge">{{ $client->client_code ?: '—' }}</div>
    </div>

    {{-- ═══ CONTENT ═══ --}}
    <div class="is-content">

        {{-- TRADING / REG SECTION --}}
        <div class="is-card">
            <div class="is-card-header"><h3><i class="fa fa-building"></i> Company Registration</h3></div>
            <div class="is-card-body">
            <div class="is-row">
                <div class="is-col-6">
                    <div class="is-label">Trading Name</div>
                    <div class="is-field {{ empty($client->trading_name) ? '--empty' : '' }}">{{ $client->trading_name ?: '—' }}</div>
                </div>
                <div class="is-col-3">
                    <div class="is-label">Company Reg No</div>
                    <div class="is-field {{ empty($client->company_reg_number) ? '--empty' : '' }}">{{ $client->company_reg_number ?: '—' }}</div>
                </div>
                <div class="is-col-3">
                    <div class="is-label">Reg Date</div>
                    <div class="is-field {{ empty($client->company_reg_date) ? '--empty' : '' }}">{{ $client->company_reg_date ? \Carbon\Carbon::parse($client->company_reg_date)->format('d F Y') : '—' }}</div>
                </div>
            </div>
            <div class="is-row">
                <div class="is-col">
                    <div class="is-label">Company Tax Number</div>
                    <div class="is-field {{ empty($client->tax_number) ? '--empty' : '' }}">@if($client->tax_number){{ $client->tax_number }}@if($client->tax_reg_date) <span style="color:#888;">[ {{ \Carbon\Carbon::parse($client->tax_reg_date)->format('d M Y') }} ]</span>@endif @else — @endif</div>
                </div>
                <div class="is-col">
                    <div class="is-label">VAT Number</div>
                    <div class="is-field {{ empty($client->vat_number) ? '--empty' : '' }}">@if($client->vat_number){{ $client->vat_number }}@if($client->vat_reg_date) <span style="color:#888;">[ {{ \Carbon\Carbon::parse($client->vat_reg_date)->format('d M Y') }} ]</span>@endif @else — @endif</div>
                </div>
                <div class="is-col">
                    <div class="is-label">PAYE Number</div>
                    <div class="is-field {{ empty($client->paye_number) ? '--empty' : '' }}">@if($client->paye_number){{ $client->paye_number }}@if($client->payroll_liability_date) <span style="color:#888;">[ {{ \Carbon\Carbon::parse($client->payroll_liability_date)->format('d M Y') }} ]</span>@endif @else — @endif</div>
                </div>
            </div>
            </div>
        </div>

        {{-- DIRECTOR DETAILS --}}
        <div class="is-card">
            <div class="is-card-header"><h3><i class="fa fa-user-tie"></i> Director Details</h3></div>
            <div class="is-card-body">
            @if(isset($director) && $director)
            <div class="is-row">
                <div class="is-col">
                    <div class="is-label">Title</div>
                    <div class="is-field {{ empty($director->title) ? '--empty' : '' }}">{{ $director->title ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">Surname</div>
                    <div class="is-field {{ empty($director->surname) ? '--empty' : '' }}">{{ $director->surname ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">Middle Name</div>
                    <div class="is-field {{ empty($director->middlename) ? '--empty' : '' }}">{{ $director->middlename ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">First Name</div>
                    <div class="is-field {{ empty($director->firstname) ? '--empty' : '' }}">{{ $director->firstname ?: '—' }}</div>
                </div>
            </div>
            <div class="is-row">
                <div class="is-col">
                    <div class="is-label">Tel</div>
                    <div class="is-field {{ empty($director->office_phone) ? '--empty' : '' }}">{{ $director->office_phone ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">Mobile</div>
                    <div class="is-field {{ empty($director->mobile_phone) ? '--empty' : '' }}">{{ $director->mobile_phone ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">WhatsApp</div>
                    <div class="is-field {{ empty($director->whatsapp_number) ? '--empty' : '' }}">{{ $director->whatsapp_number ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">Email</div>
                    <div class="is-field {{ empty($director->email) ? '--empty' : '' }}">{{ $director->email ?: '—' }}</div>
                </div>
            </div>
            @else
            <p style="color:#999;font-style:italic;margin:0;">No director details available for this client.</p>
            @endif
            </div>
        </div>

        {{-- EMP201 COMPLIANCE DASHBOARD --}}
        @if(isset($emp201Years) && $emp201Years->count() > 0)
        <div class="is-card">
            <div class="is-card-header">
                <div class="is-card-header-flex">
                    <h3><i class="fa fa-file-invoice-dollar"></i> EMP201 Compliance Dashboard</h3>
                    <select onchange="window.location.href='{{ route('client.info-sheet-dashboard') }}?client_id={{ $client->client_id }}&fy=' + this.value">
                        @foreach($emp201Years as $fy)
                        <option value="{{ $fy }}" {{ $fy == $selectedYear ? 'selected' : '' }}>Tax Year {{ $fy }} (Mar {{ $fy - 1 }} - Feb {{ $fy }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="is-card-body">

                @php
                    // Build lookup: period_suffix => declaration data
                    $periodLookup = [];
                    foreach($emp201Data as $dec) {
                        $suffix = substr($dec->payment_period, 4, 2);
                        if(!isset($periodLookup[$suffix])) {
                            $periodLookup[$suffix] = $dec;
                        }
                    }

                    // Calculate totals and compliance stats
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
                    $totalOutstanding = $totalPayable - $totalPaid;
                    $isCompliant = $outstandingCount == 0;
                @endphp

                {{-- Summary Cards --}}
                <div class="emp-summary">
                    <div class="emp-summary-card emp-sc-green">
                        <div class="emp-sc-value">{{ $filedCount }} / 12</div>
                        <div class="emp-sc-label">Returns Filed</div>
                    </div>
                    <div class="emp-summary-card emp-sc-red">
                        <div class="emp-sc-value">{{ $outstandingCount }}</div>
                        <div class="emp-sc-label">Outstanding</div>
                    </div>
                    <div class="emp-summary-card emp-sc-blue">
                        <div class="emp-sc-value">R {{ number_format($totalPayable, 2) }}</div>
                        <div class="emp-sc-label">Total Declared</div>
                    </div>
                    <div class="emp-summary-card emp-sc-amber">
                        <div class="emp-sc-value">R {{ number_format($totalPenalty, 2) }}</div>
                        <div class="emp-sc-label">Penalties</div>
                    </div>
                    <div class="emp-summary-card {{ $isCompliant ? 'emp-sc-green' : 'emp-sc-red' }}">
                        <div class="emp-sc-value"><img src="{{ asset($isCompliant ? 'storage/assets/compliant_green.png' : 'storage/assets/noncompliant_red.png') }}" alt="{{ $isCompliant ? 'Compliant' : 'Non-Compliant' }}" style="width:36px;height:36px;"></div>
                        <div class="emp-sc-label">{{ $isCompliant ? 'Compliant' : 'Non-Compliant' }}</div>
                    </div>
                </div>

                {{-- Monthly Table --}}
                <table class="emp-table" id="empTable">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Status</th>
                            <th class="text-right">PAYE</th>
                            <th class="text-right">SDL</th>
                            <th class="text-right">UIF</th>
                            <th class="text-right">Penalty</th>
                            <th class="text-right">Total</th>
                            <th>Reference</th>
                            <th class="text-right">Paid</th>
                            <th class="text-center"><i class="fa fa-receipt" title="Receipt"></i></th>
                            <th class="text-center"><i class="fa fa-shield-alt" title="Compliance"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periodMonths as $key => $monthLabel)
                        @php
                            $d = $periodLookup[$key] ?? null;
                            $isFiled = $d !== null;
                            $isPaid = $d && ($d->amount_paid ?? 0) > 0;
                            $hasReceipt = $d && !empty($d->file_proof_of_payment);
                            $isRowCompliant = $isFiled && $isPaid;
                        @endphp
                        <tr class="{{ !$isFiled ? '--outstanding' : '' }}">
                            <td><strong>{{ $monthLabel }}</strong></td>
                            <td>
                                @if($isFiled && $isPaid)
                                    <span class="emp-badge --paid">Paid</span>
                                @elseif($isFiled)
                                    <span class="emp-badge --filed">Filed</span>
                                @else
                                    <span class="emp-badge --outstanding">Outstanding</span>
                                @endif
                            </td>
                            <td class="text-right">{{ $d ? number_format($d->paye_payable, 2) : '—' }}</td>
                            <td class="text-right">{{ $d ? number_format($d->sdl_payable, 2) : '—' }}</td>
                            <td class="text-right">{{ $d ? number_format($d->uif_payable, 2) : '—' }}</td>
                            <td class="text-right">{{ $d ? number_format($d->penalty_interest ?? 0, 2) : '—' }}</td>
                            <td class="text-right"><strong>{{ $d ? number_format($d->payroll_liability, 2) : '—' }}</strong></td>
                            <td style="font-size:11px;">{{ $d->payment_reference ?? '—' }}</td>
                            <td class="text-right">{{ $d && ($d->amount_paid ?? 0) > 0 ? number_format($d->amount_paid, 2) : '—' }}</td>
                            <td class="text-center">
                                @if($hasReceipt)
                                    <i class="fa fa-file-pdf" style="color:#148f9f;" title="Receipt uploaded"></i>
                                @elseif($isFiled)
                                    <i class="fa fa-minus-circle" style="color:#bbb;" title="No receipt"></i>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center">
                                @if($isFiled && $isRowCompliant)
                                    <img src="{{ asset('storage/assets/compliant_green.png') }}" alt="Compliant" style="width:22px;height:22px;">
                                @else
                                    <img src="{{ asset('storage/assets/noncompliant_red.png') }}" alt="Non-Compliant" style="width:22px;height:22px;">
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><strong>TOTALS</strong></td>
                            <td class="text-right">{{ number_format($totalPaye, 2) }}</td>
                            <td class="text-right">{{ number_format($totalSdl, 2) }}</td>
                            <td class="text-right">{{ number_format($totalUif, 2) }}</td>
                            <td class="text-right">{{ number_format($totalPenalty, 2) }}</td>
                            <td class="text-right"><strong>{{ number_format($totalPayable, 2) }}</strong></td>
                            <td></td>
                            <td class="text-right"><strong>{{ number_format($totalPaid, 2) }}</strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
        @endif

    </div>

    {{-- ═══ EXPORT BUTTONS BAR ═══ --}}
    <div class="is-export-bar">
        <button onclick="window.print();" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-print me-1"></i> Print
        </button>
        <a href="{{ route('client.info-sheet-pdf', ['client_id' => $client->client_id, 'fy' => $selectedYear ?? '']) }}" target="_blank" class="btn btn-sm btn-danger">
            <i class="fa fa-file-pdf me-1"></i> PDF
        </a>
        <button id="btnExportExcel" class="btn btn-sm btn-success">
            <i class="fa fa-file-excel me-1"></i> Excel
        </button>
    </div>

    {{-- ═══ FOOTER ═══ --}}
    <div class="is-footer">
        <div class="is-footer-brand">
            {{ config('app.name', 'SmartWeigh') }}
            <small>Client Information Management</small>
        </div>
        <div class="is-footer-right">
            <div class="is-footer-year">{{ now()->format('Y') }}</div>
            <div class="is-footer-meta">
                {{ now()->format('F Y') }} |
                Generated by {{ auth()->user()->first_name ?? 'System' }} {{ auth()->user()->last_name ?? '' }}
            </div>
        </div>
    </div>
    </div>

    </div>
    @else
    {{-- ═══ EMPTY STATE (no client selected) ═══ --}}
    <div class="is-empty-state">
        <i class="fa fa-address-card"></i>
        <p>Select a client above to view their Info Sheet</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Bootstrap-Select
    $('select.sd_drop_class').selectpicker({ liveSearch: true, size: 10 });

    // Load button
    $('#btnLoadInfoSheet').on('click', function() {
        loadInfoSheet();
    });

    // Auto-load when client is selected
    $('#isClientSelect').on('changed.bs.select', function() {
        if ($(this).val()) {
            loadInfoSheet();
        }
    });

    function loadInfoSheet() {
        var clientId = $('#isClientSelect').val();
        var taxYear = $('#isTaxYear').val();

        if (!clientId) {
            Swal.fire({ icon: 'warning', title: 'Selection Required', text: 'Please select a client.', confirmButtonColor: '#148f9f' });
            return;
        }

        var url = '{{ route("client.info-sheet-dashboard") }}?client_id=' + clientId;
        if (taxYear) {
            url += '&fy=' + taxYear;
        }
        window.location.href = url;
    }

    // Excel Export - export EMP201 table as CSV
    $('#btnExportExcel').on('click', function() {
        var table = document.getElementById('empTable');
        if (!table) {
            Swal.fire({ icon: 'info', title: 'No Data', text: 'No EMP201 data to export.', confirmButtonColor: '#148f9f' });
            return;
        }

        var csv = [];
        var rows = table.querySelectorAll('tr');
        rows.forEach(function(row) {
            var cols = row.querySelectorAll('td, th');
            var rowData = [];
            cols.forEach(function(col) {
                var text = col.innerText.replace(/"/g, '""').trim();
                rowData.push('"' + text + '"');
            });
            csv.push(rowData.join(','));
        });

        var blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'EMP201_InfoSheet_{{ isset($client) ? $client->client_code : "export" }}.csv';
        link.click();
    });
});
</script>
@endpush
