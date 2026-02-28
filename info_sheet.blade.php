@extends('layouts.default')

@push('styles')
<style>
    /* ═══ INFO SHEET - Matches CIMS system styling ═══ */
    .is-page { padding: 20px 0 40px; max-width: 1100px; margin: 0 auto; }
    .is-form-border { border: 2px solid #000; }

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

    /* Action buttons */
    .is-actions { margin-bottom: 12px; display: flex; gap: 8px; }

    /* Print */
    @media print {
        .is-actions, .page-titles, .header, .sidebar, .footer-content, .cims-master-menu { display: none !important; }
        body { background: #fff !important; }
        .is-page { padding: 0; }
    }
</style>
@endpush

@section('content')
<div class="is-page">

    <div class="is-actions">
        <a href="{{ route('client.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i> Back to Clients
        </a>
        <a href="{{ route('client.edit', $client->client_id) }}" class="btn btn-sm btn-outline-primary">
            <i class="fa fa-pen me-1"></i> Edit Client
        </a>
    </div>

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
            <div class="is-row">
                <div class="is-col">
                    <div class="is-label">Title</div>
                    <div class="is-field {{ empty($client->director_title) ? '--empty' : '' }}">{{ $client->director_title ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">Surname</div>
                    <div class="is-field {{ empty($client->director_surname) ? '--empty' : '' }}">{{ $client->director_surname ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">Middle Name</div>
                    <div class="is-field {{ empty($client->director_middle_name) ? '--empty' : '' }}">{{ $client->director_middle_name ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">First Name</div>
                    <div class="is-field {{ empty($client->director_first_name) ? '--empty' : '' }}">{{ $client->director_first_name ?: '—' }}</div>
                </div>
            </div>
            <div class="is-row">
                <div class="is-col">
                    <div class="is-label">Tel</div>
                    <div class="is-field {{ empty($client->phone_business) ? '--empty' : '' }}">{{ $client->phone_business ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">Mobile</div>
                    <div class="is-field {{ empty($client->phone_mobile) ? '--empty' : '' }}">{{ $client->phone_mobile ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">WhatsApp</div>
                    <div class="is-field {{ empty($client->phone_whatsapp) ? '--empty' : '' }}">{{ $client->phone_whatsapp ?: '—' }}</div>
                </div>
                <div class="is-col">
                    <div class="is-label">Email</div>
                    <div class="is-field {{ empty($client->email) ? '--empty' : '' }}">{{ $client->email ?: '—' }}</div>
                </div>
            </div>
            </div>
        </div>

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
@endsection
