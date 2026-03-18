<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page {
        size: A4 portrait;
        margin: 8mm 10mm 8mm 10mm;
    }
    body {
        font-family: 'Helvetica', sans-serif;
        font-size: 10px;
        color: #333;
        margin: 0;
        padding: 0;
    }

    /* Page border - CIMS standard */
    .page-border {
        border: 2px solid #000;
        padding: 0;
        min-height: 275mm;
    }

    /* Header banner image */
    .header-img { width: 100%; display: block; }

    /* Company name & address bar */
    .company-bar { padding: 10px 16px 6px; }
    .company-name { font-size: 16px; font-weight: bold; color: #1a1a1a; margin: 0; }
    .company-address { font-size: 10px; color: #555; margin: 3px 0 0; }
    .client-code { font-size: 20px; font-weight: bold; color: #d6006e; text-align: right; padding-right: 16px; vertical-align: bottom; }

    /* Section card - matches CIMS system */
    .card {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 10px 16px;
        overflow: hidden;
    }
    .card-header {
        background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
        background-color: #148f9f;
        padding: 7px 14px;
        color: #fff;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-body {
        padding: 10px 14px;
    }

    /* Field tables */
    .field-table {
        width: 100%;
        border-collapse: collapse;
    }
    .field-table td {
        vertical-align: top;
        padding: 3px 4px;
    }
    .field-label {
        font-size: 8px;
        font-weight: bold;
        color: #1a3c4d;
        padding-bottom: 2px;
    }
    .field-value {
        border: 1.5px solid #148f9f;
        border-radius: 3px;
        padding: 6px 10px;
        font-size: 10px;
        color: #333;
        background: #fff;
        min-height: 14px;
    }
    .field-value.empty {
        color: #bbb;
        font-style: italic;
    }
    .field-date-tag { color: #888; font-size: 9px; }

    /* Director table */
    .dir-table { width: 100%; border-collapse: collapse; }
    .dir-table th {
        background-color: #148f9f;
        color: #fff;
        font-size: 7px;
        font-weight: bold;
        padding: 4px 4px;
        text-align: center;
        border: 1px solid #148f9f;
        text-transform: uppercase;
    }
    .dir-table td {
        border: 1px solid #ccc;
        padding: 3px 4px;
        font-size: 8px;
        background: #fff;
    }
    .dir-table tr:nth-child(even) td { background: #f5f5f5; }
    .dir-table .totals td { background: #e0f7fa; font-weight: bold; border-top: 2px solid #148f9f; }

    /* Checklist table */
    .check-table { width: 100%; border-collapse: collapse; }
    .check-table th {
        background-color: #148f9f;
        color: #fff;
        font-size: 7px;
        font-weight: bold;
        padding: 4px 5px;
        text-align: center;
        border: 1px solid #148f9f;
        text-transform: uppercase;
    }
    .check-table td {
        border: 1px solid #ccc;
        padding: 3px 5px;
        font-size: 8px;
        background: #fff;
        height: 7mm;
    }
    .check-table tr:nth-child(even) td { background: #f5f5f5; }

    /* Sub-header */
    .sub-header { font-weight: bold; font-size: 9px; color: #1a3c4d; margin: 8px 0 4px; }
    .separator { border-top: 1px dashed #148f9f; margin: 8px 0; opacity: 0.4; }

    /* Footer - CIMS standard */
    .footer-bar {
        background-color: #148f9f;
        padding: 8px 16px;
        color: #fff;
        font-size: 9px;
    }
    .footer-brand { font-weight: bold; font-size: 11px; }
    .footer-meta { font-size: 7px; opacity: 0.8; }

    /* Generated stamp */
    .generated-stamp {
        background: #e0f7fa;
        border: 1px solid #148f9f;
        border-radius: 4px;
        padding: 6px 14px;
        margin: 10px 16px;
        font-size: 8px;
        color: #0097A7;
    }

    .page-break { page-break-before: always; }
</style>
</head>
<body>

@php
    $val = function($v) {
        if (is_array($v) || $v instanceof \Traversable) return implode(', ', (array)$v);
        return $v ?: '';
    };
    $dateVal = function($v) {
        if (!$v || $v === '0000-00-00') return '';
        try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); } catch(\Exception $e) { return $v; }
    };
    $totalShares = $client->number_of_shares ?? 0;
@endphp

{{-- ═══════════════════════════════════════ --}}
{{-- PAGE 1: HEADER + COMPANY + TAX + PAYROLL + VAT --}}
{{-- ═══════════════════════════════════════ --}}
<div class="page-border">

    {{-- Header Image --}}
    @if($headerImgBase64)
    <img src="{{ $headerImgBase64 }}" class="header-img">
    @endif

    {{-- Company Name & Client Code --}}
    <table style="width:100%; padding:0;">
        <tr>
            <td class="company-bar" style="width:70%;">
                <div class="company-name">{{ $client->company_name ?: '—' }}</div>
                @if($addresses->count() > 0)
                @php $addr = $addresses->first(); @endphp
                <div class="company-address">
                    {{ collect([$addr->street_number ?? '', $addr->street_name ?? '', $addr->suburb ?? '', $addr->postal_code ?? '', $addr->city ?? '', $addr->province ?? ''])->filter()->implode(', ') }}
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
        <div class="card-header">:: 01 — Company Information</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Registered Company Name</div><div class="field-value {{ empty($client->company_name) ? 'empty' : '' }}">{{ $val($client->company_name) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">Company Reg No</div><div class="field-value {{ empty($client->company_reg_number) ? 'empty' : '' }}">{{ $val($client->company_reg_number) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">Reg Date</div><div class="field-value {{ empty($client->company_reg_date) ? 'empty' : '' }}">{{ $client->company_reg_date ? \Carbon\Carbon::parse($client->company_reg_date)->format('d F Y') : '—' }}</div></td>
                </tr>
            </table>
            <div style="height:6px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Trading Name</div><div class="field-value {{ empty($client->trading_name) ? 'empty' : '' }}">{{ $val($client->trading_name) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">Company Type</div><div class="field-value">{{ $val($client->company_type) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">Fin. Year End</div><div class="field-value">{{ $val($client->financial_year_end) ?: '—' }}</div></td>
                </tr>
            </table>
            <div style="height:6px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">Number of Directors</div><div class="field-value">{{ $val($client->number_of_directors) ?: '—' }}</div></td>
                    <td style="width:33%;"><div class="field-label">Number of Shares</div><div class="field-value">{{ $val($client->number_of_shares) ?: '—' }}</div></td>
                    <td style="width:33%;"><div class="field-label">Share Type</div><div class="field-value">{{ $val($client->share_type_name) ?: '—' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- TAX CARD --}}
    <div class="card">
        <div class="card-header">:: 02 — Income Tax Registration</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Company Income Tax Number</div><div class="field-value {{ empty($client->tax_number) ? 'empty' : '' }}">@if($client->tax_number){{ $client->tax_number }} @if($client->tax_reg_date)<span class="field-date-tag">[ {{ \Carbon\Carbon::parse($client->tax_reg_date)->format('d M Y') }} ]</span>@endif @else — @endif</div></td>
                    <td style="width:50%;"><div class="field-label">Date of IT Registration</div><div class="field-value {{ empty($client->tax_reg_date) ? 'empty' : '' }}">{{ $dateVal($client->tax_reg_date) ?: '—' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- PAYROLL CARD --}}
    <div class="card">
        <div class="card-header">:: 03 — Payroll Registration</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">PAYE Number</div><div class="field-value {{ empty($client->paye_number) ? 'empty' : '' }}">{{ $val($client->paye_number) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">SDL Number</div><div class="field-value {{ empty($client->sdl_number) ? 'empty' : '' }}">{{ $val($client->sdl_number) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">UIF Number</div><div class="field-value {{ empty($client->uif_number) ? 'empty' : '' }}">{{ $val($client->uif_number) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">Date of Liability</div><div class="field-value">{{ $dateVal($client->payroll_liability_date) ?: '—' }}</div></td>
                </tr>
            </table>
            <div style="height:6px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Dept of Labour Number</div><div class="field-value">{{ $val($client->dept_labour_number) ?: '—' }}</div></td>
                    <td style="width:50%;"><div class="field-label">WCA - COIDA Number</div><div class="field-value">{{ $val($client->wca_coida_number) ?: '—' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- VAT CARD --}}
    <div class="card">
        <div class="card-header">:: 04 — VAT Registration</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">VAT Number</div><div class="field-value {{ empty($client->vat_number) ? 'empty' : '' }}">@if($client->vat_number){{ $client->vat_number }} @if($client->vat_reg_date)<span class="field-date-tag">[ {{ \Carbon\Carbon::parse($client->vat_reg_date)->format('d M Y') }} ]</span>@endif @else — @endif</div></td>
                    <td style="width:33%;"><div class="field-label">Return Cycle</div><div class="field-value">{{ $val($client->vat_return_cycle) ?: '—' }}</div></td>
                    <td style="width:33%;"><div class="field-label">VAT Status</div><div class="field-value">{{ $val($client->vat_status) ?: '—' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    <table style="width:100%; margin-top:auto;">
        <tr>
            <td class="footer-bar" style="width:60%;"><div class="footer-brand">{{ config('app.name', 'SmartWeigh') }}</div><div class="footer-meta">Client Information Management</div></td>
            <td class="footer-bar" style="width:40%;text-align:right;"><div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div><div class="footer-meta">{{ now()->format('F Y') }} | Generated by {{ auth()->user()->first_name ?? 'System' }} {{ auth()->user()->last_name ?? '' }}</div></td>
        </tr>
    </table>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- PAGE 2: CONTACT + ADDRESS + DIRECTORS --}}
{{-- ═══════════════════════════════════════ --}}
<div class="page-break"></div>
<div class="page-border">

    {{-- CONTACT CARD --}}
    <div class="card">
        <div class="card-header">:: 05 — Contact Information</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">Business Phone</div><div class="field-value {{ empty($client->phone_business) ? 'empty' : '' }}">{{ $val($client->phone_business) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">Direct</div><div class="field-value {{ empty($client->direct) ? 'empty' : '' }}">{{ $val($client->direct) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">Mobile</div><div class="field-value {{ empty($client->phone_mobile) ? 'empty' : '' }}">{{ $val($client->phone_mobile) ?: '—' }}</div></td>
                    <td style="width:25%;"><div class="field-label">WhatsApp</div><div class="field-value {{ empty($client->phone_whatsapp) ? 'empty' : '' }}">{{ $val($client->phone_whatsapp) ?: '—' }}</div></td>
                </tr>
            </table>
            <div style="height:6px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Email (Compliance)</div><div class="field-value {{ empty($client->email) ? 'empty' : '' }}">{{ $val($client->email ?? $client->email_compliance ?? '') ?: '—' }}</div></td>
                    <td style="width:50%;"><div class="field-label">Email (Admin)</div><div class="field-value {{ empty($client->email_admin) ? 'empty' : '' }}">{{ $val($client->email_admin) ?: '—' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ADDRESS CARD --}}
    <div class="card">
        <div class="card-header">:: 06 — Address Details</div>
        <div class="card-body">
            @forelse($addresses as $idx => $addr)
            <div class="sub-header">Address {{ $idx + 1 }} — {{ $addr->address_type_name ?? 'N/A' }}</div>
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">Unit No.</div><div class="field-value {{ empty($addr->unit_number) ? 'empty' : '' }}">{{ $val($addr->unit_number ?? '') ?: '—' }}</div></td>
                    <td style="width:75%;"><div class="field-label">Complex / Building</div><div class="field-value {{ empty($addr->complex_name) ? 'empty' : '' }}">{{ $val($addr->complex_name ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">Street No.</div><div class="field-value">{{ $val($addr->street_number ?? '') ?: '—' }}</div></td>
                    <td style="width:75%;"><div class="field-label">Street Name</div><div class="field-value">{{ $val($addr->street_name ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Suburb</div><div class="field-value">{{ $val($addr->suburb ?? '') ?: '—' }}</div></td>
                    <td style="width:50%;"><div class="field-label">City</div><div class="field-value">{{ $val($addr->city ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">Postal Code</div><div class="field-value">{{ $val($addr->postal_code ?? '') ?: '—' }}</div></td>
                    <td style="width:34%;"><div class="field-label">Province</div><div class="field-value">{{ $val($addr->province ?? '') ?: '—' }}</div></td>
                    <td style="width:33%;"><div class="field-label">Country</div><div class="field-value">{{ $val($addr->country ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
            @if(!$loop->last)<div class="separator"></div>@endif
            @empty
            <p style="color:#999;font-style:italic;margin:4px 0;">No addresses on file</p>
            @endforelse
        </div>
    </div>

    {{-- DIRECTORS CARD --}}
    <div class="card">
        <div class="card-header">:: 07 — Directors / Shareholders</div>
        <div class="card-body">
            @if(count($directors) > 0)
            <table class="dir-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>ID Number</th>
                        <th>Tax No.</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date Engaged</th>
                        <th>Shares</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumShares = 0; @endphp
                    @foreach($directors as $dir)
                    @php
                        $dirShares = intval($dir->number_of_director_shares ?? 0);
                        $sumShares += $dirShares;
                        $pct = ($totalShares > 0 && $dir->director_type_id == 1) ? round(($dirShares / $totalShares) * 100, 2) : '';
                    @endphp
                    <tr>
                        <td>{{ trim(($dir->firstname ?? '') . ' ' . ($dir->middlename ?? '') . ' ' . ($dir->surname ?? '')) }}</td>
                        <td>{{ $dir->identity_number ?? '' }}</td>
                        <td>{{ $dir->tax_number ?? '' }}</td>
                        <td>{{ $dir->director_type_name ?? '' }}</td>
                        <td>{{ $dir->director_status_name ?? '' }}</td>
                        <td>{{ $dateVal($dir->date_engaged ?? '') }}</td>
                        <td style="text-align:right">{{ $dir->director_type_id == 1 ? number_format($dirShares) : '' }}</td>
                        <td style="text-align:right">{{ $pct ? $pct . '%' : '' }}</td>
                    </tr>
                    @endforeach
                    <tr class="totals">
                        <td colspan="6" style="text-align:right; font-weight:bold">TOTALS:</td>
                        <td style="text-align:right">{{ number_format($sumShares) }}</td>
                        <td style="text-align:right">{{ $totalShares > 0 ? round(($sumShares / $totalShares) * 100, 2) . '%' : '' }}</td>
                    </tr>
                </tbody>
            </table>
            @else
            <p style="color:#999;font-style:italic;margin:4px 0;">No directors on file</p>
            @endif
        </div>
    </div>

    <table style="width:100%; margin-top:auto;">
        <tr>
            <td class="footer-bar" style="width:60%;"><div class="footer-brand">{{ config('app.name', 'SmartWeigh') }}</div><div class="footer-meta">Client Information Management</div></td>
            <td class="footer-bar" style="width:40%;text-align:right;"><div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div><div class="footer-meta">{{ now()->format('F Y') }} | Generated by {{ auth()->user()->first_name ?? 'System' }} {{ auth()->user()->last_name ?? '' }}</div></td>
        </tr>
    </table>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- PAGE 3: SARS + BANKING + BEE + GENERAL --}}
{{-- ═══════════════════════════════════════ --}}
<div class="page-break"></div>
<div class="page-border">

    {{-- SARS CARD --}}
    <div class="card">
        <div class="card-header">:: 08 — SARS E-Filing Login Details</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">SARS Login</div><div class="field-value {{ empty($client->sars_login) ? 'empty' : '' }}">{{ $val($client->sars_login) ?: '—' }}</div></td>
                    <td style="width:50%;"><div class="field-label">SARS Password</div><div class="field-value">{{ $client->sars_password ? '********' : '—' }}</div></td>
                </tr>
            </table>
            <div style="height:6px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Mobile for SARS OTP</div><div class="field-value {{ empty($client->sars_otp_mobile) ? 'empty' : '' }}">{{ $val($client->sars_otp_mobile) ?: '—' }}</div></td>
                    <td style="width:50%;"><div class="field-label">Email for SARS OTP</div><div class="field-value {{ empty($client->sars_otp_email) ? 'empty' : '' }}">{{ $val($client->sars_otp_email) ?: '—' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- BANKING CARD --}}
    <div class="card">
        <div class="card-header">:: 09 — Banking Details</div>
        <div class="card-body">
            @forelse($banks as $idx => $bank)
            <div class="sub-header">Bank Account {{ $idx + 1 }}{{ $bank->is_default ? ' (Default)' : '' }}</div>
            <div class="field-label">Account Holder</div>
            <div class="field-value {{ empty($bank->account_holder) ? 'empty' : '' }}">{{ $val($bank->account_holder ?? '') ?: '—' }}</div>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">Bank Name</div><div class="field-value">{{ $val($bank->bank_name ?? '') ?: '—' }}</div></td>
                    <td style="width:33%;"><div class="field-label">Account Number</div><div class="field-value">{{ $val($bank->account_number ?? '') ?: '—' }}</div></td>
                    <td style="width:33%;"><div class="field-label">Account Type</div><div class="field-value">{{ $val($bank->account_type ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">Branch Name</div><div class="field-value">{{ $val($bank->branch_name ?? '') ?: '—' }}</div></td>
                    <td style="width:33%;"><div class="field-label">Branch Code</div><div class="field-value">{{ $val($bank->branch_code ?? '') ?: '—' }}</div></td>
                    <td style="width:33%;"><div class="field-label">Swift Code</div><div class="field-value">{{ $val($bank->swift_code ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
            @if(!$loop->last)<div class="separator"></div>@endif
            @empty
            <p style="color:#999;font-style:italic;margin:4px 0;">No banking details on file</p>
            @endforelse
        </div>
    </div>

    {{-- BEE CARD --}}
    <div class="card">
        <div class="card-header">:: 10 — BEE Information</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">BEE Level</div><div class="field-value {{ empty($client->bee_level) ? 'empty' : '' }}">{{ $val($client->bee_level ?? '') ?: '—' }}</div></td>
                    <td style="width:50%;"><div class="field-label">BEE Certificate Expiry</div><div class="field-value">{{ $dateVal($client->bee_expiry_date ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">BEE Certificate Number</div><div class="field-value">{{ $val($client->bee_certificate_number ?? '') ?: '—' }}</div></td>
                    <td style="width:50%;"><div class="field-label">BEE Verification Agency</div><div class="field-value">{{ $val($client->bee_verification_agency ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- GENERAL CARD --}}
    <div class="card">
        <div class="card-header">:: 11 — General</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Client Status</div><div class="field-value">{{ $val($client->status_name ?? '') ?: '—' }}</div></td>
                    <td style="width:50%;"><div class="field-label">Client Category</div><div class="field-value">{{ $val($client->category_name ?? '') ?: '—' }}</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <div class="field-label">Services</div>
            <div class="field-value">{{ is_array($services) ? implode(', ', $services) : ($services ?: '—') }}</div>
        </div>
    </div>

    {{-- Generated stamp --}}
    <div class="generated-stamp">
        <strong>System Generated</strong> — This document was auto-generated from CIMS on {{ now()->format('d F Y \a\t H:i') }} by {{ auth()->user()->first_name ?? 'System' }} {{ auth()->user()->last_name ?? '' }}.
    </div>

    <table style="width:100%; margin-top:auto;">
        <tr>
            <td class="footer-bar" style="width:60%;"><div class="footer-brand">{{ config('app.name', 'SmartWeigh') }}</div><div class="footer-meta">Client Information Management</div></td>
            <td class="footer-bar" style="width:40%;text-align:right;"><div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div><div class="footer-meta">{{ now()->format('F Y') }} | Generated by {{ auth()->user()->first_name ?? 'System' }} {{ auth()->user()->last_name ?? '' }}</div></td>
        </tr>
    </table>
</div>

</body>
</html>
