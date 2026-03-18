@extends('layouts.default')

@section('title', isset($declaration) ? 'Edit EMP201 Declaration' : 'New EMP201 Declaration')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Currency inputs right-aligned */
    .currency-input { text-align: right !important; }

    /* SARS breadcrumb_white — retain original logo as image, match form colour */
    .sars-bw { border-left-color: #0d3d56 !important; }
    .sars-bw .sars-logo-img { height: 60px; margin-right: 16px; flex-shrink: 0; }

    /* date_master_picker override for form col-3 context */
    .smartdash-form-card .date_master_picker { width: 100%; }
    .smartdash-form-card .date_master_picker input,
    .smartdash-form-card .date_master_picker .flatpickr-input + input { min-width: unset; width: 100%; background: #fff; animation: none; }

    /* Form container padding — whitespace on left and right */
    .emp201-form-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

    /* Upload zones */
    .upload-zone { border: 2px dashed #17A2B8; border-radius: 8px; padding: 15px; text-align: center; min-height: 120px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #fafafa; transition: all 0.2s; }
    .upload-zone:hover { background: #e8f7fa; border-color: #0d3d56; }
    .upload-zone .upload-icon { font-size: 28px; color: #17A2B8; margin-bottom: 8px; }
    .upload-zone .upload-label { font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px; }
</style>
@endpush

@section('content')
<div class="container-fluid emp201-form-wrapper">

    {{-- ============================================
         BREADCRUMB (CIMS System Default)
         ============================================ --}}
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        {{-- Left: Icon + Title + Subtitle --}}
        <div class="page-title">
            <div class="page-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h1>{{ isset($declaration) ? 'Edit EMP201 Declaration' : 'New EMP201 Declaration' }}</h1>
                <p>Monthly Employer Return</p>
            </div>
        </div>
        {{-- Centre: Breadcrumb trail --}}
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimsemp201.index') }}">PAYE</a>
            <span class="separator">/</span>
            <span class="current">EMP201</span>
        </div>
        {{-- Right: Close button --}}
        <a href="{{ route('cimsemp201.index') }}" class="btn button_master_close" style="color:#fff; text-decoration:none;">
            <i class="fa-solid fa-circle-left"></i> Close
        </a>
    </div>

    {{-- ============================================
         SARS LOGO HEADER (breadcrumb_white system default)
         ============================================ --}}
    <div class="breadcrumb_white sars-bw">
        <div class="bw_title_area">
            <img src="{{ asset('public/images/sars_logo.png') }}" alt="SARS Logo" class="sars-logo-img">
            <div>
                <div class="bw_title">Monthly Employer Return</div>
                <div class="bw_subtitle">South African Revenue Service</div>
            </div>
        </div>
        <div class="bw_badge">EMP 201</div>
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
             CARD 1: CLIENT & PERIOD SELECTION
             ============================================ --}}
        <div class="card smartdash-form-card">
            <div class="card-header">
                <h4><i class="fa fa-building"></i> CLIENT & PERIOD SELECTION</h4>
            </div>
            <div class="card-body">

                {{-- Row 1: Client Dropdown (col-9) + Client Code (col-3) --}}
                <div class="row">
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label class="form-label" for="client_id">Client <span class="text-danger">*</span></label>
                            <select
                                id="client_id"
                                name="client_id"
                                class="sd_drop_class"
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
                                    >{{ $client->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="display_client_code">Client Code</label>
                            <input
                                type="text"
                                id="display_client_code"
                                class="form-control sd_readonly"
                                readonly
                                value="{{ old('client_code', $declaration->client_code ?? '') }}"
                                placeholder=""
                            >
                        </div>
                    </div>
                </div>

                {{-- Row 2: Year (col-3), Period (col-5), Status (col-4) --}}
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="tax_year">Year <span class="text-danger">*</span></label>
                            <select
                                id="tax_year"
                                name="tax_year_select"
                                class="sd_drop_class"
                                data-size="10"
                                title="-- Select Year --"
                                style="width: 100%;"
                            >
                                <option value="">-- Select Year --</option>
                                @foreach($taxYears as $year)
                                    <option
                                        value="{{ $year }}"
                                        {{ (old('financial_year', $declaration->financial_year ?? '') == $year) ? 'selected' : '' }}
                                    >{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label" for="period_id">Period <span class="text-danger">*</span></label>
                            <select
                                id="period_id"
                                name="period_id"
                                class="sd_drop_class"
                                data-live-search="true"
                                data-size="10"
                                title="-- Select Period --"
                                style="width: 100%;"
                                {{ (isset($declaration) && $declaration && $declaration->financial_year) ? '' : 'disabled' }}
                            >
                                <option value="">-- Select Year First --</option>
                                @if(isset($periods) && count($periods) > 0)
                                    @foreach($periods as $period)
                                        @php
                                            $parts = explode(' - ', $period->period_name);
                                            $displayName = count($parts) === 2 ? $parts[1] . ' - [ ' . $parts[0] . ' ]' : $period->period_name;
                                        @endphp
                                        <option
                                            value="{{ $period->id }}"
                                            data-period_combo="{{ $period->period_combo }}"
                                            data-tax_year="{{ $period->tax_year }}"
                                            data-financial_year="{{ $period->tax_year ?? '' }}"
                                            data-period_name="{{ $period->period_name }}"
                                            {{ (old('period_id', $declaration->period_id ?? '') == $period->id) ? 'selected' : '' }}
                                        >{{ $displayName }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="emp201_status">Status</label>
                            <select
                                id="emp201_status"
                                name="emp201_status"
                                class="sd_drop_class"
                                data-size="10"
                                title="-- Select Status --"
                                style="width: 100%;"
                            >
                                <option value="">-- Select Status --</option>
                                @if(isset($sarsStatuses))
                                    @foreach($sarsStatuses as $sarsStatus)
                                        <option
                                            value="{{ $sarsStatus->id }}"
                                            {{ (old('emp201_status', $declaration->emp201_status ?? '') == $sarsStatus->id) ? 'selected' : '' }}
                                        >{{ $sarsStatus->status_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Row 3: Date (col-4), Prepared By (col-4), Approved By (col-4) --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="date_master_picker_label">Date</label>
                            <div class="date_master_picker">
                                <input
                                    type="text"
                                    id="declaration_date"
                                    name="declaration_date"
                                    class="datepicker-past"
                                    value="{{ old('declaration_date', isset($declaration->declaration_date) ? \Carbon\Carbon::parse($declaration->declaration_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                    placeholder="Select date..."
                                    readonly
                                >
                                <i class="fa-regular fa-calendar-days dm_icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="prepared_by">Prepared By</label>
                            <select
                                id="prepared_by"
                                name="prepared_by"
                                class="sd_drop_class"
                                data-live-search="true"
                                data-size="10"
                                title="-- Select User --"
                                style="width: 100%;"
                            >
                                <option value="">-- Select User --</option>
                                @foreach($users as $user)
                                    <option
                                        value="{{ $user->first_name }} {{ $user->last_name }}"
                                        {{ (old('prepared_by', $declaration->prepared_by ?? '') == $user->first_name . ' ' . $user->last_name) ? 'selected' : '' }}
                                    >{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="approved_by">Approved By</label>
                            <select
                                id="approved_by"
                                name="approved_by"
                                class="sd_drop_class"
                                data-live-search="true"
                                data-size="10"
                                title="-- Select User --"
                                style="width: 100%;"
                            >
                                <option value="">-- Select User --</option>
                                @foreach($users as $user)
                                    <option
                                        value="{{ $user->first_name }} {{ $user->last_name }}"
                                        {{ (old('approved_by', $declaration->approved_by ?? '') == $user->first_name . ' ' . $user->last_name) ? 'selected' : '' }}
                                    >{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        {{-- ============================================
             CARD 2: DEMOGRAPHICS
             ============================================ --}}
        <div class="card smartdash-form-card">
            <div class="card-header">
                <h4><i class="fa fa-user"></i> DEMOGRAPHICS</h4>
            </div>
            <div class="card-body">

                {{-- Sub-section: Employer Details --}}
                <div class="form-section-title"><i class="fa fa-briefcase"></i> EMPLOYER DETAILS</div>

                {{-- Trading or Other Name --}}
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Trading or Other Name</label>
                            <input type="text" id="trading_name" name="trading_name" class="form-control" readonly
                                   value="{{ old('trading_name', $declaration->trading_name ?? '') }}">
                        </div>
                    </div>
                </div>

                {{-- PAYE / SDL / UIF --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">PAYE Ref No.</label>
                            <input type="text" id="paye_number" name="paye_number" class="form-control" readonly
                                   value="{{ old('paye_number', $declaration->paye_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">SDL Ref No.</label>
                            <input type="text" id="sdl_number" name="sdl_number" class="form-control" readonly
                                   value="{{ old('sdl_number', $declaration->sdl_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">UIF Ref No.</label>
                            <input type="text" id="uif_number" name="uif_number" class="form-control" readonly
                                   value="{{ old('uif_number', $declaration->uif_number ?? '') }}">
                        </div>
                    </div>
                </div>

                {{-- Sub-section: Contact Details --}}
                <div class="form-section-title"><i class="fa fa-address-card"></i> CONTACT DETAILS</div>

                {{-- First Name / Surname / Position --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" readonly
                                   value="{{ old('first_name', $declaration->first_name ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Surname</label>
                            <input type="text" id="surname" name="surname" class="form-control" readonly
                                   value="{{ old('surname', $declaration->surname ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Position Held at Business</label>
                            <input type="text" id="position" name="position" class="form-control" readonly
                                   value="{{ old('position', $declaration->position ?? '') }}">
                        </div>
                    </div>
                </div>

                {{-- Bus Tel / Cell / Email --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Bus Tel No.</label>
                            <input type="text" id="telephone_number" name="telephone_number" class="form-control" readonly
                                   value="{{ old('telephone_number', $declaration->telephone_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Cell No.</label>
                            <input type="text" id="mobile_number" name="mobile_number" class="form-control" readonly
                                   value="{{ old('mobile_number', $declaration->mobile_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" id="email" name="email" class="form-control" readonly
                                   value="{{ old('email', $declaration->email ?? '') }}">
                        </div>
                    </div>
                </div>

            </div>
        </div>


        {{-- ============================================
             CARD 3: FINANCIALS
             ============================================ --}}
        <div class="card smartdash-form-card">
            <div class="card-header">
                <h4><i class="fa fa-calculator"></i> FINANCIALS</h4>
            </div>
            <div class="card-body">

                {{-- Three-column layout: Payroll Tax | ETI Calculation | Total Payable --}}
                <div class="row">
                    {{-- Column 1: Payroll Tax Calculation --}}
                    <div class="col-md-4">
                        <div class="form-section-title"><i class="fa fa-file-invoice-dollar"></i> PAYROLL TAX CALCULATION</div>

                        <div class="mb-3">
                            <label class="form-label">PAYE Liability</label>
                            <input type="text" id="paye_liability" name="paye_liability"
                                   class="form-control currency-input currency-field"
                                   value="{{ old('paye_liability', isset($declaration) ? number_format($declaration->paye_liability, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SDL Liability</label>
                            <input type="text" id="sdl_liability" name="sdl_liability"
                                   class="form-control currency-input currency-field"
                                   value="{{ old('sdl_liability', isset($declaration) ? number_format($declaration->sdl_liability, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">UIF Liability</label>
                            <input type="text" id="uif_liability" name="uif_liability"
                                   class="form-control currency-input currency-field"
                                   value="{{ old('uif_liability', isset($declaration) ? number_format($declaration->uif_liability, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payroll Liability</label>
                            <input type="text" id="payroll_liability" name="payroll_liability"
                                   class="form-control currency-input" readonly
                                   value="{{ old('payroll_liability', isset($declaration) ? number_format($declaration->payroll_liability, 2, '.', ' ') : '0.00') }}">
                        </div>
                    </div>

                    {{-- Column 2: ETI Calculation --}}
                    <div class="col-md-4">
                        <div class="form-section-title"><i class="fa fa-hand-holding-usd"></i> ETI CALCULATION</div>

                        <div class="mb-3">
                            <label class="form-label">ETI Brought Forward</label>
                            <input type="text" id="eti_brought_forward" name="eti_brought_forward"
                                   class="form-control currency-input eti-field" readonly
                                   value="{{ old('eti_brought_forward', isset($declaration) ? number_format($declaration->eti_brought_forward, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ETI Calculated</label>
                            <input type="text" id="eti_calculated" name="eti_calculated"
                                   class="form-control currency-input eti-field" readonly
                                   value="{{ old('eti_calculated', isset($declaration) ? number_format($declaration->eti_calculated, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ETI Utilised</label>
                            <input type="text" id="eti_utilised" name="eti_utilised"
                                   class="form-control currency-input eti-field" readonly
                                   value="{{ old('eti_utilised', isset($declaration) ? number_format($declaration->eti_utilised, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ETI Carry Forward</label>
                            <input type="text" id="eti_carry_forward" name="eti_carry_forward"
                                   class="form-control currency-input" readonly
                                   value="{{ old('eti_carry_forward', isset($declaration) ? number_format($declaration->eti_carry_forward, 2, '.', ' ') : '0.00') }}">
                        </div>
                    </div>

                    {{-- Column 3: Total Payable --}}
                    <div class="col-md-4">
                        <div class="form-section-title"><i class="fa fa-money-bill-wave"></i> TOTAL PAYABLE</div>

                        <div class="mb-3">
                            <label class="form-label">PAYE Payable</label>
                            <input type="text" id="paye_payable" name="paye_payable"
                                   class="form-control currency-input" readonly
                                   value="{{ old('paye_payable', isset($declaration) ? number_format($declaration->paye_payable, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SDL Payable</label>
                            <input type="text" id="sdl_payable" name="sdl_payable"
                                   class="form-control currency-input" readonly
                                   value="{{ old('sdl_payable', isset($declaration) ? number_format($declaration->sdl_payable, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">UIF Payable</label>
                            <input type="text" id="uif_payable" name="uif_payable"
                                   class="form-control currency-input" readonly
                                   value="{{ old('uif_payable', isset($declaration) ? number_format($declaration->uif_payable, 2, '.', ' ') : '0.00') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Penalty & Interest</label>
                            <input type="text" id="penalty_interest" name="penalty_interest"
                                   class="form-control currency-input currency-field"
                                   value="{{ old('penalty_interest', isset($declaration) ? number_format($declaration->penalty_interest, 2, '.', ' ') : '0.00') }}">
                        </div>
                    </div>
                </div>

                {{-- Bottom row: Payment Reference | Payment Period | Check Digit | Total Payable --}}
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Payment Reference No.</label>
                            <input type="text" id="payment_reference" name="payment_reference" class="form-control" readonly
                                   value="{{ old('payment_reference', $declaration->payment_reference ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Period (CCYYMM)</label>
                            <input type="text" id="payment_period" name="payment_period" class="form-control" readonly
                                   value="{{ old('payment_period', $declaration->payment_period ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Check Digit <span class="text-danger">*</span></label>
                            <input type="text" id="check_digit" name="check_digit" class="form-control" maxlength="2"
                                   style="text-align: center; font-weight: 700; font-size: 16px;"
                                   value="{{ old('check_digit', $declaration->check_digit ?? '') }}"
                                   placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Total Payable</label>
                            <input type="text" id="total_payable" name="tax_payable"
                                   class="form-control currency-input" readonly
                                   style="font-weight: 700; font-size: 18px; color: #0d3d56;"
                                   value="{{ old('total_payable', isset($declaration) ? number_format($declaration->tax_payable, 2, '.', ' ') : '0.00') }}">
                        </div>
                    </div>
                </div>

            </div>
        </div>


        {{-- ============================================
             CARD 4: TAX PRACTITIONER & NOTES
             ============================================ --}}
        <div class="card smartdash-form-card">
            <div class="card-header">
                <h4><i class="fa fa-info-circle"></i> ADDITIONAL DETAILS</h4>
            </div>
            <div class="card-body">
                <div class="row">

                    {{-- Tax Practitioner Details - side by side --}}
                    <div class="col-md-6">
                        <div class="form-section-title"><i class="fa fa-id-badge"></i> TAX PRACTITIONER DETAILS</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tax Practitioner Registration No.</label>
                                    <input type="text" id="tax_practitioner_reg_no" name="tax_practitioner_reg_no" class="form-control"
                                           value="{{ old('tax_practitioner_reg_no', $declaration->tax_practitioner_reg_no ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tax Practitioner's Tel No.</label>
                                    <input type="text" id="tax_practitioner_tel" name="tax_practitioner_tel_no" class="form-control"
                                           value="{{ old('tax_practitioner_tel', $declaration->tax_practitioner_tel_no ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Column 2: Notes --}}
                    <div class="col-md-6">
                        <div class="form-section-title"><i class="fa fa-sticky-note"></i> NOTES</div>

                        <div class="mb-3">
                            <label class="form-label">Notes / Comments</label>
                            <textarea
                                id="notes"
                                name="notes"
                                class="form-control"
                                style="min-height: 150px;"
                                placeholder="Add any notes or comments here..."
                            >{{ old('notes', $declaration->notes ?? '') }}</textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {{-- ============================================
             CARD 5: UPLOAD DOCUMENTS
             ============================================ --}}
        <div class="card smartdash-form-card">
            <div class="card-header">
                <h4><i class="fa fa-cloud-upload-alt"></i> UPLOAD DOCUMENTS</h4>
            </div>
            <div class="card-body">
                <small class="text-muted d-block mb-3">Upload PDF documents. Files are versioned - uploading a new file creates a new version.</small>
                <div class="row">

                    {{-- 1. SARS EMP201 Return --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><i class="fa fa-file-pdf text-danger"></i> SARS EMP201 Return</label>
                        <input type="file" id="file_emp201_return" name="file_emp201_return" class="form-control" accept=".pdf">
                        @if(isset($declaration) && $declaration->file_emp201_return_uploaded)
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <span class="badge bg-success font-18"><i class="fa fa-check"></i> Document on file</span>
                                @php
                                    $doc = \Modules\cims_pm_pro\Models\Document::where('file_stored_name', $declaration->file_emp201_return)->first();
                                @endphp
                                @if($doc)
                                    <span class="badge bg-primary sd_background_pink font-18">
                                        <a href="{{ route('cimsdocmanager.view', $doc->id) }}" class="text-white" target="_blank">
                                            <i class="fa fa-download"></i> View Document
                                        </a>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- 2. SARS PAYE Statement --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><i class="fa fa-file-pdf text-danger"></i> SARS PAYE Statement</label>
                        <input type="file" id="file_emp201_statement" name="file_emp201_statement" class="form-control" accept=".pdf">
                        @if(isset($declaration) && $declaration->file_emp201_statement_uploaded)
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <span class="badge bg-success font-18"><i class="fa fa-check"></i> Document on file</span>
                                @php
                                    $doc = \Modules\cims_pm_pro\Models\Document::where('file_stored_name', $declaration->file_emp201_statement)->first();
                                @endphp
                                @if($doc)
                                    <span class="badge bg-primary sd_background_pink font-18">
                                        <a href="{{ route('cimsdocmanager.view', $doc->id) }}" class="text-white" target="_blank">
                                            <i class="fa fa-download"></i> View Document
                                        </a>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- 3. EMP201 Working Papers --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><i class="fa fa-file-alt text-info"></i> EMP201 Working Papers</label>
                        <input type="file" id="file_working_papers" name="file_working_papers" class="form-control" accept=".pdf,.xls,.xlsx,.doc,.docx">
                        @if(isset($declaration) && $declaration->file_working_papers_uploaded)
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <span class="badge bg-success font-18"><i class="fa fa-check"></i> Document on file</span>
                                @php
                                    $doc = \Modules\cims_pm_pro\Models\Document::where('file_stored_name', $declaration->file_working_papers)->first();
                                @endphp
                                @if($doc)
                                    <span class="badge bg-primary sd_background_pink font-18">
                                        <a href="{{ route('cimsdocmanager.view', $doc->id) }}" class="text-white" target="_blank">
                                            <i class="fa fa-download"></i> View Document
                                        </a>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- 4. EMP201 Pack --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><i class="fa fa-file-archive text-warning"></i> EMP201 Pack</label>
                        <input type="file" id="file_emp201_pack" name="file_emp201_pack" class="form-control" accept=".pdf,.zip,.rar">
                        @if(isset($declaration) && $declaration->file_emp201_pack_uploaded)
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <span class="badge bg-success font-18"><i class="fa fa-check"></i> Document on file</span>
                                @php
                                    $doc = \Modules\cims_pm_pro\Models\Document::where('file_stored_name', $declaration->file_emp201_pack)->first();
                                @endphp
                                @if($doc)
                                    <span class="badge bg-primary sd_background_pink font-18">
                                        <a href="{{ route('cimsdocmanager.view', $doc->id) }}" class="text-white" target="_blank">
                                            <i class="fa fa-download"></i> View Document
                                        </a>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>


        {{-- ============================================
             CARD 6: PAYMENT INFORMATION
             ============================================ --}}
        <div class="card smartdash-form-card">
            <div class="card-header">
                <h4><i class="fa fa-credit-card"></i> PAYMENT INFORMATION</h4>
            </div>
            <div class="card-body">
                {{-- Row 1: Date (col-4), Method (col-4), Amount (col-4) --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="date_master_picker_label">Payment Date</label>
                            <div class="date_master_picker">
                                <input type="text" id="payment_date" name="payment_date" class="datepicker-any"
                                       value="{{ old('payment_date', isset($declaration->payment_date) ? \Carbon\Carbon::parse($declaration->payment_date)->format('Y-m-d') : '') }}"
                                       placeholder="Select date..." readonly>
                                <i class="fa-regular fa-calendar-days dm_icon"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="sd_drop_class" data-size="10" title="-- Select --" style="width: 100%;">
                                <option value="">-- Select --</option>
                                <option value="EFT by Client" {{ old('payment_method', $declaration->payment_method ?? '') == 'EFT by Client' ? 'selected' : '' }}>EFT by Client</option>
                                <option value="EFT by Accountant" {{ old('payment_method', $declaration->payment_method ?? '') == 'EFT by Accountant' ? 'selected' : '' }}>EFT by Accountant</option>
                                <option value="eFiling" {{ old('payment_method', $declaration->payment_method ?? '') == 'eFiling' ? 'selected' : '' }}>SARS eFiling</option>
                                <option value="Bank" {{ old('payment_method', $declaration->payment_method ?? '') == 'Bank' ? 'selected' : '' }}>Bank Payment</option>
                                <option value="Other" {{ old('payment_method', $declaration->payment_method ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Amount Paid</label>
                            <input type="text" id="amount_paid" name="amount_paid" class="form-control currency-input"
                                   value="{{ old('amount_paid', isset($declaration->amount_paid) ? number_format($declaration->amount_paid, 2, '.', ' ') : '') }}"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- Row 2: Payment Reference (col-6), Proof of Payment (col-6) --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Payment Reference</label>
                            <input type="text" id="payment_ref_no" name="payment_ref_no" class="form-control"
                                   value="{{ old('payment_ref_no', $declaration->payment_ref_no ?? '') }}"
                                   placeholder="e.g. Bank ref / eFiling ref">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-receipt text-success"></i> Proof of Payment</label>
                            <input type="file" id="file_proof_of_payment" name="file_proof_of_payment" class="form-control" accept=".pdf">
                            @if(isset($declaration) && $declaration->file_proof_of_payment_uploaded)
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <span class="badge bg-success font-18"><i class="fa fa-check"></i> Document on file</span>
                                    @php
                                        $doc = \Modules\cims_pm_pro\Models\Document::where('file_stored_name', $declaration->file_proof_of_payment)->first();
                                    @endphp
                                    @if($doc)
                                        <span class="badge bg-primary sd_background_pink font-18">
                                            <a href="{{ route('cimsdocmanager.view', $doc->id) }}" class="text-white" target="_blank">
                                                <i class="fa fa-download"></i> View Document
                                            </a>
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Row 3: Payment Notes (col-12, 2 rows) --}}
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Payment Notes</label>
                            <textarea id="payment_notes" name="payment_notes" class="form-control" rows="2"
                                      placeholder="Any payment related notes...">{{ old('payment_notes', $declaration->payment_notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        {{-- ============================================
             FORM ACTIONS
             ============================================ --}}
        <div class="card smartdash-form-card">
            <div class="card-body text-center">
                <button class="button_master_save" type="submit" id="btnSaveEmp201">
                    <i class="fa fa-save"></i> Save EMP201
                </button>
                <a href="{{ route('cimsemp201.index') }}" class="button_master_close" style="margin-left: 10px; text-decoration: none;">
                    <i class="fa-solid fa-circle-left"></i> Close
                </a>
            </div>
        </div>

    </form>

</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="/public/smartdash/js/smartdash-dates.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ============================================
    // Initialize flatpickr on date_master_picker (override any material datepicker)
    // ============================================
    var $declDate = $('#declaration_date');
    if ($declDate.data('DateTimePicker')) { $declDate.data('DateTimePicker').destroy(); }
    if ($declDate.data('bootstrapMaterialDatePicker')) { $declDate.bootstrapMaterialDatePicker('destroy'); }
    flatpickr('#declaration_date', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'D, j M Y',
        allowInput: false,
        maxDate: 'today',
        defaultDate: $declDate.val() || 'today'
    });

    // Initialize flatpickr on payment date (date_master_picker)
    var $payDate = $('#payment_date');
    if ($payDate.data('DateTimePicker')) { $payDate.data('DateTimePicker').destroy(); }
    if ($payDate.data('bootstrapMaterialDatePicker')) { $payDate.bootstrapMaterialDatePicker('destroy'); }
    if ($payDate.val()) {
        flatpickr('#payment_date', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'D, j M Y',
            allowInput: false,
            defaultDate: $payDate.val()
        });
    } else {
        flatpickr('#payment_date', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'D, j M Y',
            allowInput: false
        });
    }

    // ============================================
    // Initialize Bootstrap-Select
    // ============================================
    $('select.sd_drop_class').selectpicker({
        liveSearch: true,
        size: 10
    });

    // Format SDL/UIF and phone numbers on page load (edit mode)
    if ($('#sdl_number').val()) { $('#sdl_number').val(formatSdlUif($('#sdl_number').val())); }
    if ($('#uif_number').val()) { $('#uif_number').val(formatSdlUif($('#uif_number').val())); }
    if ($('#telephone_number').val()) { $('#telephone_number').val(formatPhone($('#telephone_number').val())); }
    if ($('#mobile_number').val()) { $('#mobile_number').val(formatPhone($('#mobile_number').val())); }

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
    // HELPER: Format SDL/UIF number as "X ### ### ###"
    // ============================================
    function formatSdlUif(val) {
        if (!val) return '';
        var str = String(val).replace(/\s/g, '');
        // Extract prefix letter and digits
        var prefix = str.match(/^[A-Za-z]/);
        var digits = str.replace(/[^0-9]/g, '');
        if (digits.length === 9) {
            digits = digits.substr(0, 3) + ' ' + digits.substr(3, 3) + ' ' + digits.substr(6, 3);
        }
        return prefix ? prefix[0].toUpperCase() + ' ' + digits : digits;
    }

    // ============================================
    // HELPER: Format phone number as ### ### ####
    // ============================================
    function formatPhone(val) {
        if (!val) return '';
        var digits = String(val).replace(/\D/g, '');
        if (digits.length === 10) {
            return digits.substr(0, 3) + ' ' + digits.substr(3, 3) + ' ' + digits.substr(6, 4);
        }
        return val;
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
    // 0. YEAR SELECTION HANDLER - Load periods by year
    // ============================================
    var isEdit = {{ isset($declaration) && $declaration ? 'true' : 'false' }};
    var editPeriodId = '{{ old('period_id', $declaration->period_id ?? '') }}';

    $('#tax_year').on('changed.bs.select', function(e) {
        var year = $(this).val();
        var $periodSelect = $('#period_id');

        // Update hidden financial_year field
        $('#financial_year').val(year || '');

        if (!year) {
            // Clear and disable period dropdown
            $periodSelect.empty().append('<option value="">-- Select Year First --</option>');
            $periodSelect.prop('disabled', true).selectpicker('refresh');
            return;
        }

        // Show loading state
        $periodSelect.prop('disabled', true);
        $periodSelect.empty().append('<option value="">Loading periods...</option>');
        $periodSelect.selectpicker('refresh');

        // Fetch periods for selected year via AJAX
        $.ajax({
            url: '{{ route("cimsemp201.api.periods") }}',
            data: { tax_year: year },
            dataType: 'json',
            success: function(periods) {
                $periodSelect.empty();
                $periodSelect.append('<option value="">-- Select Period --</option>');

                if (periods.length === 0) {
                    $periodSelect.append('<option value="" disabled>No periods found for ' + year + '</option>');
                } else {
                    $.each(periods, function(i, period) {
                        var selected = (editPeriodId == period.id) ? ' selected' : '';
                        // Format: "March 2025 - [ 202503 ]" from "202503 - March 2025"
                        var displayName = period.period_name || '';
                        var parts = displayName.split(' - ');
                        if (parts.length === 2) {
                            displayName = parts[1] + ' - [ ' + parts[0] + ' ]';
                        }
                        $periodSelect.append(
                            '<option value="' + period.id + '"' +
                            ' data-period_combo="' + (period.period_combo || '') + '"' +
                            ' data-tax_year="' + (period.tax_year || '') + '"' +
                            ' data-financial_year="' + (period.tax_year || '') + '"' +
                            ' data-period_name="' + (period.period_name || '') + '"' +
                            selected + '>' + displayName + '</option>'
                        );
                    });
                }

                $periodSelect.prop('disabled', false).selectpicker('refresh');

                // If editing and period was pre-selected, trigger change to populate fields
                if (editPeriodId && $periodSelect.val()) {
                    $periodSelect.trigger('changed.bs.select');
                }
            },
            error: function() {
                $periodSelect.empty().append('<option value="">Error loading periods</option>');
                $periodSelect.prop('disabled', false).selectpicker('refresh');
            }
        });
    });

    // On edit mode, if year is already selected, enable period and set value
    @if(isset($declaration) && $declaration && $declaration->financial_year)
        $('#period_id').prop('disabled', false);
        @if($declaration->period_id)
            $('#period_id').val('{{ $declaration->period_id }}');
        @endif
        $('#period_id').selectpicker('refresh');
    @endif

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
            $('#display_client_code').val('');
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
        $('#sdl_number').val(formatSdlUif($selected.data('sdl_number') || ''));
        $('#uif_number').val(formatSdlUif($selected.data('uif_number') || ''));

        // Populate Demographics - Contact Details from SARS Representative (director info)
        var clientId = $selected.val();
        $.ajax({
            url: '{{ url("/cims/emp201/api/sars-representative") }}/' + clientId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.found) {
                    $('#first_name').val(data.first_name || '');
                    $('#surname').val(data.surname || '');
                    $('#position').val(data.position || '');
                    $('#telephone_number').val(formatPhone(data.office_phone || $selected.data('phone_business') || ''));
                    $('#mobile_number').val(formatPhone(data.mobile_phone || $selected.data('phone_mobile') || ''));
                    $('#email').val(data.email || $selected.data('email') || '');
                } else {
                    // Fallback to client_master fields if no director found
                    $('#first_name').val($selected.data('sars_rep_first_name') || '');
                    $('#surname').val($selected.data('sars_rep_surname') || '');
                    $('#position').val($selected.data('sars_rep_position') || '');
                    $('#telephone_number').val(formatPhone($selected.data('phone_business') || ''));
                    $('#mobile_number').val(formatPhone($selected.data('phone_mobile') || ''));
                    $('#email').val($selected.data('email') || '');
                }
            },
            error: function() {
                // Fallback to client_master fields on error
                $('#first_name').val($selected.data('sars_rep_first_name') || '');
                $('#surname').val($selected.data('sars_rep_surname') || '');
                $('#position').val($selected.data('sars_rep_position') || '');
                $('#telephone_number').val(formatPhone($selected.data('phone_business') || ''));
                $('#mobile_number').val(formatPhone($selected.data('phone_mobile') || ''));
                $('#email').val($selected.data('email') || '');
            }
        });

        // Populate hidden fields + display client code
        $('#hidden_client_code').val($selected.data('client_code') || '');
        $('#display_client_code').val($selected.data('client_code') || '');
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
    function formatPayeNumber(paye) {
        // Remove all spaces and non-digits
        var digits = paye.replace(/\D/g, '');
        if (digits.length === 10) {
            // Format as XXX XXX XXXX
            return digits.substring(0, 3) + ' ' + digits.substring(3, 6) + ' ' + digits.substring(6, 10);
        }
        return paye; // Return as-is if not 10 digits
    }

    function generatePaymentReference() {
        var payeNumber = $('#paye_number').val() || '';
        var periodCombo = $('#period_combo').val() || '';
        var checkDigit = $('#check_digit').val() || '';

        if (payeNumber && periodCombo) {
            var ref = formatPayeNumber(payeNumber) + ' LC ' + periodCombo;
            if (checkDigit) {
                ref += ' ' + checkDigit;
            }
            $('#payment_reference').val(ref);
        } else {
            $('#payment_reference').val('');
        }
    }


    // ============================================
    // 2b. CHECK DIGIT HANDLER - regenerate payment reference
    // ============================================
    $('#check_digit').on('input', function() {
        generatePaymentReference();
    });

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
    // 5. (Penalty warning & ETI indicator removed)
    // ============================================
    // ETI fields are always editable now

    // ============================================
    // 6. (VDP section removed)
    // ============================================


    // ============================================
    // 7. FORM VALIDATION WITH SWEETALERT2 + DUPLICATE CHECK
    // ============================================
    var duplicateOverrideConfirmed = false;

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

        // Check digit is required
        var checkDigitVal = $('#check_digit').val();
        if (!checkDigitVal || checkDigitVal.trim() === '') {
            errors.push('Please enter the Check Digit.');
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
                confirmButtonColor: '#17A2B8'
            });
            return false;
        }

        // If user already confirmed duplicate override, proceed with submit
        if (duplicateOverrideConfirmed) {
            // Strip currency formatting before submit
            $('.currency-input').each(function() {
                var raw = parseCurrency($(this).val());
                $(this).val(raw.toFixed(2));
            });
            return true;
        }

        // Check for duplicate before submitting
        e.preventDefault();

        var excludeId = '{{ $declaration->id ?? '' }}';

        $.ajax({
            url: '{{ route("cimsemp201.api.check-duplicate") }}',
            data: {
                client_id: clientVal,
                period_id: periodVal,
                exclude_id: excludeId
            },
            dataType: 'json',
            success: function(response) {
                if (response.duplicate) {
                    // Format period name for display
                    var periodName = response.period_name || 'this period';
                    var parts = periodName.split(' - ');
                    var displayPeriod = parts.length === 2 ? parts[1] : periodName;
                    var clientCode = response.client_code || '';

                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Warning',
                        html: '<div style="font-size:15px;">An EMP201 has already been prepared for<br>' +
                              '<strong>' + clientCode + '</strong> — <strong>' + displayPeriod + '</strong><br><br>' +
                              'Do you want to continue?</div>',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Continue',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#17A2B8',
                        cancelButtonColor: '#dc3545',
                        reverseButtons: true
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            // Log the override to audit table
                            $.ajax({
                                url: '{{ route("cimsemp201.api.audit-log") }}',
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    client_id: clientVal,
                                    client_code: clientCode,
                                    return_type: 'emp201',
                                    period_id: periodVal,
                                    period_name: periodName,
                                    action: 'duplicate_override',
                                    notes: 'User confirmed duplicate EMP201 for ' + clientCode + ' - ' + displayPeriod
                                },
                                dataType: 'json',
                                complete: function() {
                                    // Set flag and re-submit the form
                                    duplicateOverrideConfirmed = true;
                                    $('#emp201Form').submit();
                                }
                            });
                        }
                        // If cancelled, do nothing — user stays on form
                    });
                } else {
                    // No duplicate — proceed with submit
                    duplicateOverrideConfirmed = true;
                    $('#emp201Form').submit();
                }
            },
            error: function() {
                // On error, proceed with submit (don't block the user)
                duplicateOverrideConfirmed = true;
                $('#emp201Form').submit();
            }
        });

        return false;
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
            confirmButtonColor: '#17A2B8',
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
@endpush
