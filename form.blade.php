@extends('layouts.default')

@section('title', isset($declaration) ? 'Edit EMP201 Declaration' : 'New EMP201 Declaration')

@section('page-css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* ============================================
       SARS EMP201 Form Styles
       Matches official SARS eFiling layout
       ============================================ */

    /* Page background */
    .emp201-page-wrapper {
        background-color: #d5d5d5;
        padding: 20px 0;
        min-height: 100vh;
    }

    /* SARS Header Bar */
    .sars-header {
        background-color: #c0c0c0;
        padding: 20px 30px;
        display: flex;
        align-items: center;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .sars-header img {
        height: 70px;
        margin-right: 30px;
    }
    .sars-header .sars-title {
        font-size: 22px;
        font-weight: 700;
        color: #003d6b;
        letter-spacing: 0.5px;
    }
    .sars-header .sars-subtitle {
        font-size: 14px;
        color: #003d6b;
        font-weight: 400;
    }

    /* Main section headers (dark blue) */
    .section-bar-main {
        background-color: #003d6b;
        color: #fff;
        padding: 12px 20px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 4px;
        margin-bottom: 15px;
        margin-top: 10px;
    }

    /* Sub-section headers (lighter blue-grey) */
    .section-bar-sub {
        background-color: #a8bcc8;
        color: #003d6b;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 4px;
        margin-bottom: 15px;
        margin-top: 5px;
    }

    /* Card styling */
    .emp201-card {
        background: #fff;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .emp201-card .card-header-blue {
        background-color: #003d6b;
        color: #fff;
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 4px 4px 0 0;
        margin: -20px -20px 20px -20px;
    }

    /* Fieldset-style labels (floating above border) */
    .sars-fieldset {
        position: relative;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 12px 14px 10px 14px;
        margin-bottom: 15px;
        background: #fff;
    }
    .sars-fieldset .sars-fieldset-label {
        position: absolute;
        top: -10px;
        left: 12px;
        background: #fff;
        padding: 0 6px;
        font-size: 11px;
        color: #c00;
        font-weight: 500;
    }
    .sars-fieldset .sars-fieldset-label-dark {
        position: absolute;
        top: -10px;
        left: 12px;
        background: #fff;
        padding: 0 6px;
        font-size: 11px;
        color: #003d6b;
        font-weight: 500;
    }
    .sars-fieldset input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 14px;
        color: #333;
        background-color: #fff !important;
        padding: 0;
    }
    .sars-fieldset input:focus {
        box-shadow: none;
        outline: none;
    }
    .sars-fieldset .lock-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 14px;
    }

    /* Readonly fields - white background, not greyed */
    input[readonly],
    input[readonly]:focus {
        background-color: #fff !important;
        color: #333 !important;
        cursor: default;
    }

    /* Currency fields right-aligned */
    .currency-input {
        text-align: right !important;
        padding-right: 10px !important;
    }

    /* Currency fields - no prefix needed */

    /* Financial three-column headers */
    .fin-col-header {
        background-color: #003d6b;
        color: #fff;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 4px;
        margin-bottom: 15px;
        border: 1px solid #003d6b;
    }

    /* Warning note box */
    .warning-note {
        border: 1px solid #a8bcc8;
        border-radius: 4px;
        padding: 12px 16px;
        margin-bottom: 15px;
        font-size: 13px;
        color: #333;
        background: #f8f9fa;
    }
    .warning-note a {
        color: #003d6b;
        text-decoration: underline;
    }

    /* ETI / VDP Radio buttons */
    .radio-inline-group {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
        color: #333;
    }
    .radio-inline-group label {
        margin-bottom: 0;
        margin-right: 10px;
        cursor: pointer;
        font-weight: 400;
    }
    .radio-inline-group input[type="radio"] {
        margin-right: 3px;
        cursor: pointer;
    }

    /* Part 1 - Client & Period Selection card */
    .selection-card {
        background: #fff;
        border-radius: 6px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .selection-card .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #003d6b;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #003d6b;
    }

    /* Bootstrap-Select overrides */
    .bootstrap-select.default-select .dropdown-toggle {
        border: 1px solid #ccc !important;
        border-radius: 4px !important;
        background-color: #fff !important;
        height: 42px;
    }
    .bootstrap-select.default-select .dropdown-toggle:focus {
        outline: none !important;
        box-shadow: 0 0 0 0.2rem rgba(0,61,107,0.25) !important;
    }

    /* File upload zones */
    .upload-zone {
        border: 2px dashed #ccc;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background: #fafafa;
    }
    .upload-zone:hover {
        border-color: #003d6b;
        background: #f0f4f8;
    }
    .upload-zone .upload-icon {
        font-size: 28px;
        color: #999;
        margin-bottom: 8px;
    }
    .upload-zone .upload-label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    .upload-zone input[type="file"] {
        width: 100%;
        font-size: 12px;
    }
    .upload-zone .file-name {
        font-size: 12px;
        color: #666;
        word-break: break-all;
        margin-top: 5px;
    }
    .upload-zone .btn-view-file {
        margin-top: 8px;
        font-size: 12px;
    }

    /* Submit buttons */
    .btn-save-emp201 {
        background: linear-gradient(135deg, #003d6b 0%, #005a9e 100%);
        border: none;
        color: #fff;
        padding: 12px 35px;
        font-weight: 700;
        border-radius: 6px;
        font-size: 15px;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 14px rgba(0,61,107,0.3);
        transition: all 0.3s ease;
    }
    .btn-save-emp201:hover {
        background: linear-gradient(135deg, #002a4a 0%, #003d6b 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0,61,107,0.4);
        color: #fff;
    }
    .btn-cancel-emp201 {
        color: #666;
        padding: 12px 25px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
    }
    .btn-cancel-emp201:hover {
        color: #333;
        text-decoration: none;
    }

    /* Form labels */
    .emp201-label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
        display: block;
    }

    /* Notes card textarea */
    .notes-textarea {
        width: 100%;
        min-height: 150px;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 10px;
        font-size: 14px;
        resize: vertical;
    }
    .notes-textarea:focus {
        outline: none;
        border-color: #003d6b;
        box-shadow: 0 0 0 0.2rem rgba(0,61,107,0.15);
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .sars-header {
            flex-direction: column;
            text-align: center;
        }
        .sars-header img {
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="emp201-page-wrapper">
<div class="container-fluid">

    {{-- ============================================
         SARS HEADER
         ============================================ --}}
    <div class="sars-header">
        <img src="{{ asset('images/sars_logo.png') }}" alt="SARS Logo">
        <div>
            <div class="sars-title">Monthly Employer Return <span style="font-weight:400;">EMP201</span></div>
            <div class="sars-subtitle">South African Revenue Service</div>
        </div>
    </div>

    {{-- ============================================
         FORM START
         ============================================ --}}
    <form
        id="emp201Form"
        method="POST"
        action="{{ isset($declaration) ? route('cimsemp201.update', $declaration->id) : route('cimsemp201.store') }}"
        enctype="multipart/form-data"
    >
        @csrf
        @if(isset($declaration))
            @method('PUT')
        @endif

        {{-- Hidden fields --}}
        <input type="hidden" name="financial_year" id="financial_year" value="{{ old('financial_year', $declaration->financial_year ?? '') }}">
        <input type="hidden" name="period_combo" id="period_combo" value="{{ old('period_combo', $declaration->period_combo ?? '') }}">
        <input type="hidden" name="pay_period" id="pay_period" value="{{ old('pay_period', $declaration->pay_period ?? '') }}">
        <input type="hidden" name="client_code" id="hidden_client_code" value="{{ old('client_code', $declaration->client_code ?? '') }}">
        <input type="hidden" name="company_name" id="hidden_company_name" value="{{ old('company_name', $declaration->company_name ?? '') }}">
        <input type="hidden" name="company_number" id="hidden_company_number" value="{{ old('company_number', $declaration->company_number ?? '') }}">
        <input type="hidden" name="vat_number" id="hidden_vat_number" value="{{ old('vat_number', $declaration->vat_number ?? '') }}">
        <input type="hidden" name="income_tax_number" id="hidden_income_tax_number" value="{{ old('income_tax_number', $declaration->income_tax_number ?? '') }}">


        {{-- ============================================
             PART 1 - CLIENT & PERIOD SELECTION
             ============================================ --}}
        <div class="selection-card">
            <div class="section-title">
                <i class="fa fa-building"></i> Client &amp; Period Selection
            </div>

            {{-- Row 1: Client Dropdown --}}
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label class="emp201-label" for="client_id">Client <span class="text-danger">*</span></label>
                        <select
                            id="client_id"
                            name="client_id"
                            class="default-select sd_drop_class"
                            data-live-search="true"
                            data-live-search-placeholder="Search clients..."
                            data-size="10"
                            title="-- Select Client --"
                            style="width: 100%;"
                        >
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $client)
                                <option
                                    value="{{ $client->client_id }}"
                                    data-trading_name="{{ $client->trading_name ?? '' }}"
                                    data-paye_number="{{ $client->paye_number ?? '' }}"
                                    data-sdl_number="{{ $client->sdl_number ?? '' }}"
                                    data-uif_number="{{ $client->uif_number ?? '' }}"
                                    data-tax_number="{{ $client->tax_number ?? '' }}"
                                    data-sars_rep_first_name="{{ $client->sars_rep_first_name ?? '' }}"
                                    data-sars_rep_surname="{{ $client->sars_rep_surname ?? '' }}"
                                    data-sars_rep_position="{{ $client->sars_rep_position ?? '' }}"
                                    data-phone_business="{{ $client->phone_business ?? '' }}"
                                    data-phone_mobile="{{ $client->phone_mobile ?? '' }}"
                                    data-email="{{ $client->email ?? '' }}"
                                    data-company_reg_number="{{ $client->company_reg_number ?? '' }}"
                                    data-vat_number="{{ $client->vat_number ?? '' }}"
                                    data-client_code="{{ $client->client_code ?? '' }}"
                                    data-company_name="{{ $client->company_name ?? '' }}"
                                    {{ (old('client_id', $declaration->client_id ?? '') == $client->client_id) ? 'selected' : '' }}
                                >{{ $client->client_code }} - {{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Row 2: Period & Prepared By --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="emp201-label" for="period_id">Period <span class="text-danger">*</span></label>
                        <select
                            id="period_id"
                            name="period_id"
                            class="default-select sd_drop_class"
                            data-live-search="true"
                            data-size="10"
                            title="-- Select Period --"
                            style="width: 100%;"
                        >
                            <option value="">-- Select Period --</option>
                            @foreach($periods as $period)
                                <option
                                    value="{{ $period->id }}"
                                    data-period_combo="{{ $period->period_combo }}"
                                    data-tax_year="{{ $period->tax_year }}"
                                    data-financial_year="{{ $period->tax_year }}"
                                    data-period_name="{{ $period->period_name }}"
                                    {{ (old('period_id', $declaration->period_id ?? '') == $period->id) ? 'selected' : '' }}
                                >{{ $period->period_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="emp201-label" for="prepared_by">Prepared By</label>
                        <input
                            type="text"
                            id="prepared_by"
                            name="prepared_by"
                            class="form-control"
                            value="{{ old('prepared_by', $declaration->prepared_by ?? auth()->user()->name ?? '') }}"
                            placeholder="Name of person preparing this return"
                            style="height: 42px;"
                        >
                    </div>
                </div>
            </div>
        </div>


        {{-- ============================================
             PART 2 - DEMOGRAPHICS (readonly)
             ============================================ --}}
        <div class="section-bar-main">
            <i class="fa fa-user"></i> Demographics
        </div>

        <div class="emp201-card">
            {{-- Employer Details sub-section --}}
            <div class="section-bar-sub">
                <i class="fa fa-briefcase"></i> Employer Details
            </div>

            {{-- Trading or Other Name --}}
            <div class="row">
                <div class="col-12">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Trading or Other Name</span>
                        <input type="text" id="trading_name" name="trading_name" readonly
                               value="{{ old('trading_name', $declaration->trading_name ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
            </div>

            {{-- PAYE / SDL / UIF --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">PAYE Ref No.</span>
                        <input type="text" id="paye_number" name="paye_number" readonly
                               value="{{ old('paye_number', $declaration->paye_number ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">SDL Ref No.</span>
                        <input type="text" id="sdl_number" name="sdl_number" readonly
                               value="{{ old('sdl_number', $declaration->sdl_number ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">UIF Ref No.</span>
                        <input type="text" id="uif_number" name="uif_number" readonly
                               value="{{ old('uif_number', $declaration->uif_number ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
            </div>

            {{-- Contact Details sub-section --}}
            <div class="section-bar-sub" style="margin-top: 20px;">
                <i class="fa fa-address-card"></i> Contact Details
            </div>

            {{-- First Name / Surname / Position --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">First Name</span>
                        <input type="text" id="first_name" name="first_name" readonly
                               value="{{ old('first_name', $declaration->first_name ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Surname</span>
                        <input type="text" id="surname" name="surname" readonly
                               value="{{ old('surname', $declaration->surname ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Position held at Business</span>
                        <input type="text" id="position" name="position" readonly
                               value="{{ old('position', $declaration->position ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
            </div>

            {{-- Bus Tel / Cell / Email --}}
            <div class="row">
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Bus Tel No.</span>
                        <input type="text" id="telephone_number" name="telephone_number" readonly
                               value="{{ old('telephone_number', $declaration->telephone_number ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Cell No.</span>
                        <input type="text" id="mobile_number" name="mobile_number" readonly
                               value="{{ old('mobile_number', $declaration->mobile_number ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Email</span>
                        <input type="text" id="email" name="email" readonly
                               value="{{ old('email', $declaration->email ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
            </div>
        </div>


        {{-- ============================================
             PART 3 - FINANCIALS
             ============================================ --}}
        <div class="section-bar-main">
            <i class="fa fa-calculator"></i> Financials
        </div>

        <div class="emp201-card">
            {{-- Warning note --}}
            <div class="warning-note">
                <i class="fa fa-exclamation-triangle text-warning"></i>
                Penalty of 10% is payable on late payments. Interest is calculated on a daily basis at the applicable prescribed rate.
                To view the table of rates, go to <a href="https://www.sars.gov.za" target="_blank">www.sars.gov.za</a>
            </div>

            {{-- ETI Indicator --}}
            <div class="mb-3" style="padding: 10px 0;">
                <span style="font-weight: 600; color: #003d6b; font-size: 14px; margin-right: 10px;">ETI Indicator</span>
                <div class="radio-inline-group">
                    <label>
                        <input type="radio" name="eti_indicator" value="Y"
                            {{ old('eti_indicator', $declaration->eti_indicator ?? 'N') == 'Y' ? 'checked' : '' }}> Y
                    </label>
                    <label>
                        <input type="radio" name="eti_indicator" value="N"
                            {{ old('eti_indicator', $declaration->eti_indicator ?? 'N') == 'N' ? 'checked' : '' }}> N
                    </label>
                </div>
            </div>

            {{-- Three-column layout: Payroll Tax | ETI Calculation | Total Payable --}}
            <div class="row">
                {{-- Column 1: Payroll Tax Calculation --}}
                <div class="col-md-4">
                    <div class="fin-col-header">Payroll Tax Calculation</div>

                    {{-- PAYE Liability --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">PAYE Liability</span>

                        <input type="text" id="paye_liability" name="paye_liability"
                               class="currency-input currency-field"
                               value="{{ old('paye_liability', isset($declaration) ? number_format($declaration->paye_liability, 2, '.', ' ') : '0.00') }}">
                    </div>

                    {{-- SDL Liability --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">SDL Liability</span>

                        <input type="text" id="sdl_liability" name="sdl_liability"
                               class="currency-input currency-field"
                               value="{{ old('sdl_liability', isset($declaration) ? number_format($declaration->sdl_liability, 2, '.', ' ') : '0.00') }}">
                    </div>

                    {{-- UIF Liability --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">UIF Liability</span>

                        <input type="text" id="uif_liability" name="uif_liability"
                               class="currency-input currency-field"
                               value="{{ old('uif_liability', isset($declaration) ? number_format($declaration->uif_liability, 2, '.', ' ') : '0.00') }}">
                    </div>

                    {{-- Payroll Liability (calculated) --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Payroll Liability</span>

                        <input type="text" id="payroll_liability" name="payroll_liability" readonly
                               class="currency-input"
                               value="{{ old('payroll_liability', isset($declaration) ? number_format($declaration->payroll_liability, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>

                {{-- Column 2: ETI Calculation --}}
                <div class="col-md-4">
                    <div class="fin-col-header">ETI Calculation</div>

                    {{-- ETI Brought Forward --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">ETI Brought Forward</span>

                        <input type="text" id="eti_brought_forward" name="eti_brought_forward"
                               class="currency-input eti-field"
                               readonly
                               value="{{ old('eti_brought_forward', isset($declaration) ? number_format($declaration->eti_brought_forward, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon eti-lock"></i>
                    </div>

                    {{-- ETI Calculated --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">ETI Calculated</span>

                        <input type="text" id="eti_calculated" name="eti_calculated"
                               class="currency-input eti-field"
                               readonly
                               value="{{ old('eti_calculated', isset($declaration) ? number_format($declaration->eti_calculated, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon eti-lock"></i>
                    </div>

                    {{-- ETI Utilised --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">ETI Utilised</span>

                        <input type="text" id="eti_utilised" name="eti_utilised"
                               class="currency-input eti-field"
                               readonly
                               value="{{ old('eti_utilised', isset($declaration) ? number_format($declaration->eti_utilised, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon eti-lock"></i>
                    </div>

                    {{-- ETI Carry Forward (calculated) --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">ETI Carry Forward</span>

                        <input type="text" id="eti_carry_forward" name="eti_carry_forward" readonly
                               class="currency-input"
                               value="{{ old('eti_carry_forward', isset($declaration) ? number_format($declaration->eti_carry_forward, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>

                {{-- Column 3: Total Payable --}}
                <div class="col-md-4">
                    <div class="fin-col-header">Total Payable</div>

                    {{-- PAYE Payable (calculated) --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">PAYE Payable</span>

                        <input type="text" id="paye_payable" name="paye_payable" readonly
                               class="currency-input"
                               value="{{ old('paye_payable', isset($declaration) ? number_format($declaration->paye_payable, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>

                    {{-- SDL Payable (calculated) --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">SDL Payable</span>

                        <input type="text" id="sdl_payable" name="sdl_payable" readonly
                               class="currency-input"
                               value="{{ old('sdl_payable', isset($declaration) ? number_format($declaration->sdl_payable, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>

                    {{-- UIF Payable (calculated) --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">UIF Payable</span>

                        <input type="text" id="uif_payable" name="uif_payable" readonly
                               class="currency-input"
                               value="{{ old('uif_payable', isset($declaration) ? number_format($declaration->uif_payable, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>

                    {{-- Penalty & Interest --}}
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Penalty &amp; Interest</span>

                        <input type="text" id="penalty_interest" name="penalty_interest"
                               class="currency-input currency-field"
                               value="{{ old('penalty_interest', isset($declaration) ? number_format($declaration->penalty_interest, 2, '.', ' ') : '0.00') }}">
                    </div>
                </div>
            </div>

            {{-- Bottom row: Payment Reference | Payment Period | Total Payable --}}
            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Payment Reference No.</span>
                        <input type="text" id="payment_reference" name="payment_reference" readonly
                               value="{{ old('payment_reference', $declaration->payment_reference ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Payment Period (CCYYMM)</span>
                        <input type="text" id="payment_period" name="payment_period" readonly
                               value="{{ old('payment_period', $declaration->payment_period ?? '') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label">Total Payable</span>

                        <input type="text" id="total_payable" name="tax_payable" readonly
                               class="currency-input"
                               style="font-weight: 700; font-size: 16px; color: #003d6b;"
                               value="{{ old('total_payable', isset($declaration) ? number_format($declaration->total_payable, 2, '.', ' ') : '0.00') }}">
                        <i class="fa fa-lock lock-icon"></i>
                    </div>
                </div>
            </div>
        </div>


        {{-- ============================================
             PART 4 - VDP, TAX PRACTITIONER, NOTES
             ============================================ --}}
        <div class="row">
            {{-- Column 1: Voluntary Disclosure Programme --}}
            <div class="col-md-4">
                <div class="emp201-card" style="min-height: 250px;">
                    <div class="card-header-blue">
                        <i class="fa fa-shield-alt"></i> Voluntary Disclosure Programme
                    </div>
                    <div class="mb-3">
                        <span style="font-size: 13px; color: #333;">
                            Is this declaration in respect of a VDP agreement with SARS
                        </span>
                        <div class="radio-inline-group" style="margin-left: 10px;">
                            <label>
                                <input type="radio" name="vdp_agreement" value="Y"
                                    {{ old('vdp_agreement', $declaration->vdp_agreement ?? 'N') == 'Y' ? 'checked' : '' }}> Y
                            </label>
                            <label>
                                <input type="radio" name="vdp_agreement" value="N"
                                    {{ old('vdp_agreement', $declaration->vdp_agreement ?? 'N') == 'N' ? 'checked' : '' }}> N
                            </label>
                        </div>
                    </div>
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label-dark">VDP Application No.</span>
                        <input type="text" id="vdp_application_no" name="vdp_application_no"
                               {{ (old('vdp_agreement', $declaration->vdp_agreement ?? 'N') == 'N') ? 'disabled' : '' }}
                               value="{{ old('vdp_application_no', $declaration->vdp_application_no ?? '') }}"
                               placeholder="">
                        <i class="fa fa-lock lock-icon vdp-lock" style="{{ (old('vdp_agreement', $declaration->vdp_agreement ?? 'N') == 'Y') ? 'display:none;' : '' }}"></i>
                    </div>
                </div>
            </div>

            {{-- Column 2: Tax Practitioner Details --}}
            <div class="col-md-4">
                <div class="emp201-card" style="min-height: 250px;">
                    <div class="card-header-blue">
                        <i class="fa fa-id-badge"></i> Tax Practitioner <span style="font-weight:400;">Details (if applicable)</span>
                    </div>
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label-dark">Tax Practitioner Registration No.</span>
                        <input type="text" id="tax_practitioner_reg_no" name="tax_practitioner_reg_no"
                               value="{{ old('tax_practitioner_reg_no', $declaration->tax_practitioner_reg_no ?? '') }}">
                    </div>
                    <div class="sars-fieldset">
                        <span class="sars-fieldset-label-dark">Tax Practitioner's Tel No.</span>
                        <input type="text" id="tax_practitioner_tel" name="tax_practitioner_tel_no"
                               value="{{ old('tax_practitioner_tel', $declaration->tax_practitioner_tel_no ?? '') }}">
                    </div>
                </div>
            </div>

            {{-- Column 3: Notes --}}
            <div class="col-md-4">
                <div class="emp201-card" style="min-height: 250px;">
                    <div class="card-header-blue">
                        <i class="fa fa-sticky-note"></i> Notes
                    </div>
                    <textarea
                        id="notes"
                        name="notes"
                        class="notes-textarea"
                        placeholder="Add any notes or comments here..."
                    >{{ old('notes', $declaration->notes ?? '') }}</textarea>
                </div>
            </div>
        </div>


        {{-- ============================================
             PART 5 - UPLOAD DOCUMENTS
             ============================================ --}}
        <div class="emp201-card">
            <div class="card-header-blue">
                <i class="fa fa-cloud-upload-alt"></i> Upload Documents
            </div>

            <div class="row">
                {{-- 1. SARS EMP201 Return --}}
                <div class="col-md-3">
                    <div class="upload-zone">
                        <i class="fa fa-file-pdf upload-icon"></i>
                        <span class="upload-label">SARS EMP201 Return</span>
                        <input type="file" id="file_emp201_return" name="file_emp201_return"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               onchange="showFileName(this, 'fname_emp201_return')">
                        <span class="file-name" id="fname_emp201_return">
                            @if(isset($declaration) && $declaration->file_emp201_return)
                                {{ $declaration->file_emp201_return }}
                            @else
                                No file selected
                            @endif
                        </span>
                        @if(isset($declaration) && $declaration->file_emp201_return)
                            <a href="{{ asset('uploads/emp201/' . $declaration->file_emp201_return) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary btn-view-file">
                                <i class="fa fa-eye"></i> View
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 2. SARS PAYE Statement --}}
                <div class="col-md-3">
                    <div class="upload-zone">
                        <i class="fa fa-file-pdf upload-icon"></i>
                        <span class="upload-label">SARS PAYE Statement</span>
                        <input type="file" id="file_emp201_statement" name="file_emp201_statement"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               onchange="showFileName(this, 'fname_emp201_statement')">
                        <span class="file-name" id="fname_emp201_statement">
                            @if(isset($declaration) && $declaration->file_emp201_statement)
                                {{ $declaration->file_emp201_statement }}
                            @else
                                No file selected
                            @endif
                        </span>
                        @if(isset($declaration) && $declaration->file_emp201_statement)
                            <a href="{{ asset('uploads/emp201/' . $declaration->file_emp201_statement) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary btn-view-file">
                                <i class="fa fa-eye"></i> View
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 3. EMP201 Working Papers --}}
                <div class="col-md-3">
                    <div class="upload-zone">
                        <i class="fa fa-file-alt upload-icon"></i>
                        <span class="upload-label">EMP201 Working Papers</span>
                        <input type="file" id="file_working_papers" name="file_working_papers"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx"
                               onchange="showFileName(this, 'fname_working_papers')">
                        <span class="file-name" id="fname_working_papers">
                            @if(isset($declaration) && $declaration->file_working_papers)
                                {{ $declaration->file_working_papers }}
                            @else
                                No file selected
                            @endif
                        </span>
                        @if(isset($declaration) && $declaration->file_working_papers)
                            <a href="{{ asset('uploads/emp201/' . $declaration->file_working_papers) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary btn-view-file">
                                <i class="fa fa-eye"></i> View
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 4. EMP201 Pack --}}
                <div class="col-md-3">
                    <div class="upload-zone">
                        <i class="fa fa-file-archive upload-icon"></i>
                        <span class="upload-label">EMP201 Pack</span>
                        <input type="file" id="file_emp201_pack" name="file_emp201_pack"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar"
                               onchange="showFileName(this, 'fname_emp201_pack')">
                        <span class="file-name" id="fname_emp201_pack">
                            @if(isset($declaration) && $declaration->file_emp201_pack)
                                {{ $declaration->file_emp201_pack }}
                            @else
                                No file selected
                            @endif
                        </span>
                        @if(isset($declaration) && $declaration->file_emp201_pack)
                            <a href="{{ asset('uploads/emp201/' . $declaration->file_emp201_pack) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary btn-view-file">
                                <i class="fa fa-eye"></i> View
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        {{-- ============================================
             SUBMIT BUTTONS
             ============================================ --}}
        <div class="text-center" style="padding: 20px 0 40px 0;">
            <button type="submit" class="btn btn-save-emp201" id="btnSaveEmp201">
                <i class="fa fa-save"></i> Save EMP201
            </button>
            <a href="{{ route('cimsemp201.index') }}" class="btn-cancel-emp201">
                <i class="fa fa-times"></i> Cancel
            </a>
        </div>

    </form>

</div>
</div>
@endsection


@section('page-js')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // HELPER: Currency formatting
    // ============================================
    function parseCurrency(str) {
        if (!str || str === '') return 0;
        // Remove spaces (thousands separator) and any non-numeric chars except . and -
        var cleaned = String(str).replace(/\s/g, '').replace(/[^0-9.\-]/g, '');
        var val = parseFloat(cleaned);
        return isNaN(val) ? 0 : val;
    }

    function formatCurrency(value) {
        var num = parseFloat(value);
        if (isNaN(num)) num = 0;
        // Format with 2 decimal places
        var parts = num.toFixed(2).split('.');
        // Add space as thousands separator
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return parts.join('.');
    }

    // ============================================
    // HELPER: Show file name on file input change
    // ============================================
    window.showFileName = function(input, targetId) {
        var target = document.getElementById(targetId);
        if (input.files && input.files.length > 0) {
            target.textContent = input.files[0].name;
        } else {
            target.textContent = 'No file selected';
        }
    };

    // ============================================
    // 1. CLIENT SELECTION HANDLER
    // ============================================
    $('#client_id').on('changed.bs.select', function(e) {
        var $selected = $(this).find('option:selected');

        if (!$selected.val()) {
            // Clear all demographics
            $('#trading_name').val('');
            $('#paye_number').val('');
            $('#sdl_number').val('');
            $('#uif_number').val('');
            $('#first_name').val('');
            $('#surname').val('');
            $('#position').val('');
            $('#telephone_number').val('');
            $('#mobile_number').val('');
            $('#email').val('');
            $('#hidden_client_code').val('');
            $('#hidden_company_name').val('');
            $('#hidden_company_number').val('');
            $('#hidden_vat_number').val('');
            $('#hidden_income_tax_number').val('');
            $('#payment_reference').val('');
            return;
        }

        // Populate Demographics - Employer Details
        $('#trading_name').val($selected.data('trading_name') || '');
        $('#paye_number').val($selected.data('paye_number') || '');
        $('#sdl_number').val($selected.data('sdl_number') || '');
        $('#uif_number').val($selected.data('uif_number') || '');

        // Populate Demographics - Contact Details
        $('#first_name').val($selected.data('sars_rep_first_name') || '');
        $('#surname').val($selected.data('sars_rep_surname') || '');
        $('#position').val($selected.data('sars_rep_position') || '');
        $('#telephone_number').val($selected.data('phone_business') || '');
        $('#mobile_number').val($selected.data('phone_mobile') || '');
        $('#email').val($selected.data('email') || '');

        // Populate hidden fields
        $('#hidden_client_code').val($selected.data('client_code') || '');
        $('#hidden_company_name').val($selected.data('company_name') || '');
        $('#hidden_company_number').val($selected.data('company_reg_number') || '');
        $('#hidden_vat_number').val($selected.data('vat_number') || '');
        $('#hidden_income_tax_number').val($selected.data('tax_number') || '');

        // Generate payment reference: paye_number + "LC" + period_combo
        generatePaymentReference();
    });


    // ============================================
    // 2. PERIOD SELECTION HANDLER
    // ============================================
    $('#period_id').on('changed.bs.select', function(e) {
        var $selected = $(this).find('option:selected');

        if (!$selected.val()) {
            $('#period_combo').val('');
            $('#financial_year').val('');
            $('#pay_period').val('');
            $('#payment_period').val('');
            $('#payment_reference').val('');
            return;
        }

        var periodCombo = $selected.data('period_combo') || '';
        var financialYear = $selected.data('financial_year') || '';
        var taxYear = $selected.data('tax_year') || '';

        $('#period_combo').val(periodCombo);
        $('#financial_year').val(financialYear);
        $('#pay_period').val($selected.data('period_name') || '');

        // Payment Period in CCYYMM format (same as period_combo)
        $('#payment_period').val(periodCombo);

        // Regenerate payment reference
        generatePaymentReference();
    });


    // ============================================
    // HELPER: Generate Payment Reference
    // ============================================
    function generatePaymentReference() {
        var payeNumber = $('#paye_number').val() || '';
        var periodCombo = $('#period_combo').val() || '';

        if (payeNumber && periodCombo) {
            $('#payment_reference').val(payeNumber + 'LC' + periodCombo);
        } else {
            $('#payment_reference').val('');
        }
    }


    // ============================================
    // 3. AUTO-CALCULATIONS
    // ============================================
    function recalculate() {
        var payeLiability  = parseCurrency($('#paye_liability').val());
        var sdlLiability   = parseCurrency($('#sdl_liability').val());
        var uifLiability   = parseCurrency($('#uif_liability').val());
        var etiUtilised    = parseCurrency($('#eti_utilised').val());
        var etiBroughtFwd  = parseCurrency($('#eti_brought_forward').val());
        var etiCalculated  = parseCurrency($('#eti_calculated').val());
        var penaltyInt     = parseCurrency($('#penalty_interest').val());

        // Payroll Liability = PAYE + SDL + UIF
        var payrollLiability = payeLiability + sdlLiability + uifLiability;
        $('#payroll_liability').val(formatCurrency(payrollLiability));

        // PAYE Payable = PAYE Liability - ETI Utilised
        var payePayable = payeLiability - etiUtilised;
        $('#paye_payable').val(formatCurrency(payePayable));

        // SDL Payable = SDL Liability
        var sdlPayable = sdlLiability;
        $('#sdl_payable').val(formatCurrency(sdlPayable));

        // UIF Payable = UIF Liability
        var uifPayable = uifLiability;
        $('#uif_payable').val(formatCurrency(uifPayable));

        // ETI Carry Forward = ETI Brought Forward + ETI Calculated - ETI Utilised
        var etiCarryForward = etiBroughtFwd + etiCalculated - etiUtilised;
        $('#eti_carry_forward').val(formatCurrency(etiCarryForward));

        // Total Payable = PAYE Payable + SDL Payable + UIF Payable + Penalty & Interest
        var totalPayable = payePayable + sdlPayable + uifPayable + penaltyInt;
        $('#total_payable').val(formatCurrency(totalPayable));
    }

    // Bind recalculation to all editable currency fields
    $(document).on('input', '.currency-field, .eti-field', function() {
        recalculate();
    });

    // Also recalculate on blur after formatting
    $(document).on('blur', '.currency-field', function() {
        recalculate();
    });


    // ============================================
    // 4. CURRENCY FORMATTING ON BLUR
    // ============================================
    $(document).on('blur', '.currency-input', function() {
        var val = parseCurrency($(this).val());
        $(this).val(formatCurrency(val));
    });

    // On focus: select all text for easy editing
    $(document).on('focus', '.currency-input:not([readonly])', function() {
        $(this).select();
    });


    // ============================================
    // 5. ETI INDICATOR TOGGLE
    // ============================================
    function toggleETI() {
        var isETI = $('input[name="eti_indicator"]:checked').val() === 'Y';

        if (isETI) {
            // Enable ETI fields for input
            $('#eti_brought_forward').prop('readonly', false).addClass('currency-field');
            $('#eti_calculated').prop('readonly', false).addClass('currency-field');
            $('#eti_utilised').prop('readonly', false).addClass('currency-field');
            $('.eti-lock').hide();
        } else {
            // Disable and zero out ETI fields
            $('#eti_brought_forward').prop('readonly', true).removeClass('currency-field').val('0.00');
            $('#eti_calculated').prop('readonly', true).removeClass('currency-field').val('0.00');
            $('#eti_utilised').prop('readonly', true).removeClass('currency-field').val('0.00');
            $('.eti-lock').show();
            recalculate();
        }
    }

    $('input[name="eti_indicator"]').on('change', function() {
        toggleETI();
        recalculate();
    });

    // Initialize ETI state on page load
    toggleETI();


    // ============================================
    // 6. VDP TOGGLE
    // ============================================
    function toggleVDP() {
        var isVDP = $('input[name="vdp_agreement"]:checked').val() === 'Y';

        if (isVDP) {
            $('#vdp_application_no').prop('disabled', false);
            $('.vdp-lock').hide();
        } else {
            $('#vdp_application_no').prop('disabled', true).val('');
            $('.vdp-lock').show();
        }
    }

    $('input[name="vdp_agreement"]').on('change', function() {
        toggleVDP();
    });

    // Initialize VDP state on page load
    toggleVDP();


    // ============================================
    // 7. FORM VALIDATION WITH SWEETALERT2
    // ============================================
    $('#emp201Form').on('submit', function(e) {
        var errors = [];

        // Client must be selected
        var clientVal = $('#client_id').val();
        if (!clientVal || clientVal === '') {
            errors.push('Please select a Client.');
        }

        // Period must be selected
        var periodVal = $('#period_id').val();
        if (!periodVal || periodVal === '') {
            errors.push('Please select a Period.');
        }

        if (errors.length > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '<ul style="text-align:left; font-size:14px;">' +
                      errors.map(function(err) { return '<li>' + err + '</li>'; }).join('') +
                      '</ul>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#003d6b'
            });
            return false;
        }

        // Before submit: strip currency formatting from all currency inputs
        $('.currency-input').each(function() {
            var raw = parseCurrency($(this).val());
            $(this).val(raw.toFixed(2));
        });

        // Enable VDP field if disabled so its value gets submitted
        if ($('#vdp_application_no').prop('disabled')) {
            $('#vdp_application_no').prop('disabled', false);
        }

        return true;
    });


    // ============================================
    // INITIAL: Run calculations on page load (for edit mode)
    // ============================================
    @if(isset($declaration))
        // Trigger populate from selected client (in case data-attributes are present)
        recalculate();

        // Generate payment reference if PAYE and period combo exist
        if ($('#paye_number').val() && $('#period_combo').val()) {
            generatePaymentReference();
        }
    @endif

    // Format all currency fields on load
    $('.currency-input').each(function() {
        var val = parseCurrency($(this).val());
        $(this).val(formatCurrency(val));
    });


    // ============================================
    // SESSION MESSAGES (Success/Error via SweetAlert2)
    // ============================================
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{!! addslashes(session('success')) !!}',
            confirmButtonText: 'OK',
            confirmButtonColor: '#003d6b',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: '<div style="font-size:14px;">{!! addslashes(session('error')) !!}</div>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            html: '<ul style="text-align:left; font-size:14px;">' +
                @foreach($errors->all() as $error)
                    '<li>{{ addslashes($error) }}</li>' +
                @endforeach
                '</ul>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    @endif

});
</script>
@endsection
