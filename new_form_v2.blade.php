@extends('layouts.default')

@section('title', isset($declaration) ? 'Edit EMP201 Declaration' : 'New EMP201 Declaration')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* Currency inputs right-aligned */
    .currency-input { text-align: right !important; }

    /* SARS logo header */
    .sars-logo-header { background: #fff; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .sars-logo-header img { height: 60px; margin-right: 20px; }
    .sars-logo-header .sars-title { font-size: 20px; font-weight: 700; color: #0d3d56; }
    .sars-logo-header .sars-subtitle { font-size: 13px; color: #666; }

    /* Upload zones */
    .upload-zone { border: 2px dashed #17A2B8; border-radius: 8px; padding: 15px; text-align: center; min-height: 120px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #fafafa; transition: all 0.2s; }
    .upload-zone:hover { background: #e8f7fa; border-color: #0d3d56; }
    .upload-zone .upload-icon { font-size: 28px; color: #17A2B8; margin-bottom: 8px; }
    .upload-zone .upload-label { font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ============================================
         BREADCRUMB
         ============================================ --}}
    <div class="row page-titles">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="javascript:void(0)">Compliance</a></li>
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#17A2B8" href="javascript:void(0)">EMP201</a></li>
            </ol>
            <a href="{{ route('cimsemp201.index') }}" class="btn sd_btn">
                <i class="fa fa-list"></i> All EMP201
            </a>
        </div>
    </div>

    {{-- ============================================
         SARS LOGO HEADER
         ============================================ --}}
    <div class="sars-logo-header">
        <img src="{{ asset('public/images/sars_logo.png') }}" alt="SARS Logo">
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
             CARD 1: CLIENT & PERIOD SELECTION
             ============================================ --}}
        <div class="card smartdash-form-card">
            <div class="card-header">
                <h4><i class="fa fa-building"></i> CLIENT & PERIOD SELECTION</h4>
            </div>
            <div class="card-body">

                {{-- Row 1: Client Dropdown --}}
                <div class="row">
                    <div class="col-12">
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
                                    >{{ $client->client_code }} - {{ $client->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Period, Date & Prepared By --}}
                <div class="row">
                    <div class="col-md-4">
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
                            >
                                <option value="">-- Select Period --</option>
                                @foreach($periods as $period)
                                    <option
                                        value="{{ $period->id }}"
                                        data-period_combo="{{ $period->period_combo }}"
                                        data-tax_year="{{ $period->tax_year }}"
                                        data-financial_year="{{ $period->tax_year ?? '' }}"
                                        data-period_name="{{ $period->period_name }}"
                                        {{ (old('period_id', $declaration->period_id ?? '') == $period->id) ? 'selected' : '' }}
                                    >{{ $period->period_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="declaration_date">Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <input
                                    type="text"
                                    id="declaration_date"
                                    name="declaration_date"
                                    class="form-control datepicker-past"
                                    value="{{ old('declaration_date', isset($declaration->declaration_date) ? \Carbon\Carbon::parse($declaration->declaration_date)->format('D, j M Y') : now()->format('D, j M Y')) }}"
                                    placeholder="Select date"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="prepared_by">Prepared By</label>
                            <input
                                type="text"
                                id="prepared_by"
                                name="prepared_by"
                                class="form-control"
                                value="{{ old('prepared_by', $declaration->prepared_by ?? auth()->user()->name ?? '') }}"
                                placeholder="Name of person preparing this return"
                            >
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

                {{-- Bottom row: Payment Reference | Payment Period | Total Payable --}}
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Payment Reference No.</label>
                            <input type="text" id="payment_reference" name="payment_reference" class="form-control" readonly
                                   value="{{ old('payment_reference', $declaration->payment_reference ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Payment Period (CCYYMM)</label>
                            <input type="text" id="payment_period" name="payment_period" class="form-control" readonly
                                   value="{{ old('payment_period', $declaration->payment_period ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Total Payable</label>
                            <input type="text" id="total_payable" name="tax_payable"
                                   class="form-control currency-input" readonly
                                   style="font-weight: 700; font-size: 18px; color: #0d3d56;"
                                   value="{{ old('total_payable', isset($declaration) ? number_format($declaration->total_payable, 2, '.', ' ') : '0.00') }}">
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
                <div class="row">

                    {{-- 1. SARS EMP201 Return --}}
                    <div class="col-md-3">
                        <div class="upload-zone">
                            <i class="fa fa-file-pdf upload-icon"></i>
                            <span class="upload-label">SARS EMP201 Return</span>
                            <input type="file" id="file_emp201_return" name="file_emp201_return"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   onchange="showFileName(this, 'fname_emp201_return')"
                                   style="width:100%; font-size:12px;">
                            <span id="fname_emp201_return" style="font-size:12px; color:#666; word-break:break-all; margin-top:5px;">
                                @if(isset($declaration) && $declaration->file_emp201_return)
                                    {{ $declaration->file_emp201_return }}
                                @else
                                    No file selected
                                @endif
                            </span>
                            @if(isset($declaration) && $declaration->file_emp201_return)
                                <a href="{{ asset('uploads/emp201/' . $declaration->file_emp201_return) }}"
                                   target="_blank" class="btn btn-sm sd_btn" style="margin-top:8px; padding:4px 12px !important; font-size:12px !important;">
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
                                   onchange="showFileName(this, 'fname_emp201_statement')"
                                   style="width:100%; font-size:12px;">
                            <span id="fname_emp201_statement" style="font-size:12px; color:#666; word-break:break-all; margin-top:5px;">
                                @if(isset($declaration) && $declaration->file_emp201_statement)
                                    {{ $declaration->file_emp201_statement }}
                                @else
                                    No file selected
                                @endif
                            </span>
                            @if(isset($declaration) && $declaration->file_emp201_statement)
                                <a href="{{ asset('uploads/emp201/' . $declaration->file_emp201_statement) }}"
                                   target="_blank" class="btn btn-sm sd_btn" style="margin-top:8px; padding:4px 12px !important; font-size:12px !important;">
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
                                   onchange="showFileName(this, 'fname_working_papers')"
                                   style="width:100%; font-size:12px;">
                            <span id="fname_working_papers" style="font-size:12px; color:#666; word-break:break-all; margin-top:5px;">
                                @if(isset($declaration) && $declaration->file_working_papers)
                                    {{ $declaration->file_working_papers }}
                                @else
                                    No file selected
                                @endif
                            </span>
                            @if(isset($declaration) && $declaration->file_working_papers)
                                <a href="{{ asset('uploads/emp201/' . $declaration->file_working_papers) }}"
                                   target="_blank" class="btn btn-sm sd_btn" style="margin-top:8px; padding:4px 12px !important; font-size:12px !important;">
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
                                   onchange="showFileName(this, 'fname_emp201_pack')"
                                   style="width:100%; font-size:12px;">
                            <span id="fname_emp201_pack" style="font-size:12px; color:#666; word-break:break-all; margin-top:5px;">
                                @if(isset($declaration) && $declaration->file_emp201_pack)
                                    {{ $declaration->file_emp201_pack }}
                                @else
                                    No file selected
                                @endif
                            </span>
                            @if(isset($declaration) && $declaration->file_emp201_pack)
                                <a href="{{ asset('uploads/emp201/' . $declaration->file_emp201_pack) }}"
                                   target="_blank" class="btn btn-sm sd_btn" style="margin-top:8px; padding:4px 12px !important; font-size:12px !important;">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            @endif
                        </div>
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
                <div class="row">

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Payment Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" id="payment_date" name="payment_date" class="form-control datepicker-past"
                                       value="{{ old('payment_date', isset($declaration->payment_date) ? \Carbon\Carbon::parse($declaration->payment_date)->format('D, j M Y') : '') }}"
                                       placeholder="Select date">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="EFT" {{ old('payment_method', $declaration->payment_method ?? '') == 'EFT' ? 'selected' : '' }}>EFT</option>
                                <option value="eFiling" {{ old('payment_method', $declaration->payment_method ?? '') == 'eFiling' ? 'selected' : '' }}>SARS eFiling</option>
                                <option value="Bank" {{ old('payment_method', $declaration->payment_method ?? '') == 'Bank' ? 'selected' : '' }}>Bank Payment</option>
                                <option value="Other" {{ old('payment_method', $declaration->payment_method ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Amount Paid</label>
                            <input type="text" id="amount_paid" name="amount_paid" class="form-control currency-input"
                                   value="{{ old('amount_paid', isset($declaration->amount_paid) ? number_format($declaration->amount_paid, 2, '.', ' ') : '') }}"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Payment Reference</label>
                            <input type="text" id="payment_ref_no" name="payment_ref_no" class="form-control"
                                   value="{{ old('payment_ref_no', $declaration->payment_ref_no ?? '') }}"
                                   placeholder="e.g. Bank ref / eFiling ref">
                        </div>
                    </div>

                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="upload-zone">
                            <i class="fa fa-receipt upload-icon"></i>
                            <span class="upload-label">Proof of Payment</span>
                            <input type="file" id="file_proof_of_payment" name="file_proof_of_payment"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   onchange="showFileName(this, 'fname_proof_of_payment')"
                                   style="width:100%; font-size:12px;">
                            <span id="fname_proof_of_payment" style="font-size:12px; color:#666; word-break:break-all; margin-top:5px;">
                                @if(isset($declaration) && $declaration->file_proof_of_payment)
                                    {{ $declaration->file_proof_of_payment }}
                                @else
                                    No file selected
                                @endif
                            </span>
                            @if(isset($declaration) && $declaration->file_proof_of_payment)
                                <a href="{{ asset('uploads/emp201/' . $declaration->file_proof_of_payment) }}"
                                   target="_blank" class="btn btn-sm sd_btn" style="margin-top:8px; padding:4px 12px !important; font-size:12px !important;">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Payment Notes</label>
                            <textarea id="payment_notes" name="payment_notes" class="form-control"
                                      style="min-height: 100px;"
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
                <button class="btn sd_btn" type="submit" id="btnSaveEmp201">
                    <i class="fa fa-save"></i> Save EMP201
                </button>
                <a href="{{ route('cimsemp201.index') }}" class="btn sd_btn_secondary" style="margin-left: 10px;">
                    <i class="fa fa-times"></i> Cancel
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
    // Initialize Bootstrap-Select
    // ============================================
    $('select.sd_drop_class').selectpicker({
        liveSearch: true,
        size: 10
    });

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
    // 5. (Penalty warning & ETI indicator removed)
    // ============================================
    // ETI fields are always editable now

    // ============================================
    // 6. (VDP section removed)
    // ============================================


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
                confirmButtonColor: '#17A2B8'
            });
            return false;
        }

        // Before submit: strip currency formatting from all currency inputs
        $('.currency-input').each(function() {
            var raw = parseCurrency($(this).val());
            $(this).val(raw.toFixed(2));
        });

        // (VDP section removed)

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
