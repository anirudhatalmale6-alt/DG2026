@extends('layouts.default')

@section('title', isset($declaration) ? 'Edit EMP201' : 'Create EMP201')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ============================================== */
/* PAGE HEADER - Teal Gradient                    */
/* ============================================== */
.smartdash-page-header {
    background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
    border-radius: 12px;
    padding: 20px 28px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.25);
}
.smartdash-page-header .page-title {
    display: flex;
    align-items: center;
    gap: 15px;
}
.smartdash-page-header .page-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.smartdash-page-header .page-title h1 {
    font-size: 26px;
    font-weight: 800;
    margin: 0;
    letter-spacing: 0.5px;
}
.smartdash-page-header .page-title p {
    font-size: 13px;
    margin: 4px 0 0 0;
    opacity: 0.9;
}
.smartdash-page-header .page-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}
.smartdash-page-header .page-breadcrumb a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.smartdash-page-header .page-breadcrumb a:hover {
    color: #fff;
}
.smartdash-page-header .page-breadcrumb .separator {
    opacity: 0.5;
}
.smartdash-page-header .page-breadcrumb .current {
    font-weight: 700;
    color: #fff;
}
.smartdash-page-header .page-actions {
    display: flex;
    gap: 10px;
}
.smartdash-page-header .btn-page-action {
    background: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
.smartdash-page-header .btn-page-action:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
    color: #fff;
}

@media (max-width: 992px) {
    .smartdash-page-header {
        flex-direction: column;
        text-align: center;
    }
    .smartdash-page-header .page-title {
        flex-direction: column;
    }
    .smartdash-page-header .page-title h1 {
        font-size: 22px;
    }
}

/* ============================================== */
/* FORM CARD & SECTION STYLING                    */
/* ============================================== */
.emp201-form-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    margin-bottom: 24px;
}
.emp201-form-card .card-body {
    padding: 28px;
}

/* Section Header Bars - Teal Theme */
.section-header-bar {
    background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
    padding: 18px 28px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
}
.section-header-bar h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.section-header-bar i {
    font-size: 22px;
    opacity: 0.9;
}

/* Section Header - Inline (no bar) */
.section-header-inline {
    border-bottom: 3px solid #17A2B8;
    padding-bottom: 12px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.section-header-inline h5 {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: #0d3d56;
    letter-spacing: 0.3px;
}
.section-header-inline i {
    color: #17A2B8;
    font-size: 18px;
}

/* Form Controls */
.emp201-form .form-group {
    margin-bottom: 20px;
}
.emp201-form .form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
    display: block;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.emp201-form .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 15px;
    font-weight: 600;
    color: #334155;
    transition: all 0.2s ease;
    height: auto;
}
.emp201-form .form-control:focus {
    border-color: #17A2B8;
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
    outline: none;
}
.emp201-form .form-control[readonly],
.emp201-form .form-control.readonly-field {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: default;
    border-color: #e9ecef;
}
.emp201-form .form-control.currency-input {
    text-align: right;
    font-size: 16px;
    font-weight: 700;
    color: #0d3d56;
    padding-right: 20px;
}
.emp201-form .form-control.currency-input[readonly] {
    color: #17A2B8;
    font-weight: 800;
}

/* Bootstrap-Select Overrides */
.emp201-form .bootstrap-select .btn {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 15px;
    font-weight: 600;
    color: #334155;
    height: auto;
    background: #fff;
    transition: all 0.2s ease;
}
.emp201-form .bootstrap-select .btn:focus,
.emp201-form .bootstrap-select.show .btn {
    border-color: #17A2B8;
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15);
    outline: none;
}
.emp201-form .bootstrap-select .dropdown-menu {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    padding: 8px;
}
.emp201-form .bootstrap-select .dropdown-item {
    border-radius: 8px;
    padding: 10px 14px;
    font-weight: 500;
    transition: all 0.15s ease;
}
.emp201-form .bootstrap-select .dropdown-item:hover,
.emp201-form .bootstrap-select .dropdown-item.active {
    background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
    color: #0d3d56;
}

/* File Upload Styling */
.file-upload-wrapper {
    position: relative;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    background: #fafbfc;
    cursor: pointer;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.file-upload-wrapper:hover {
    border-color: #17A2B8;
    background: #f0fdfa;
}
.file-upload-wrapper i {
    font-size: 32px;
    color: #94a3b8;
    transition: color 0.3s;
}
.file-upload-wrapper:hover i {
    color: #17A2B8;
}
.file-upload-wrapper .upload-label {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
}
.file-upload-wrapper .upload-hint {
    font-size: 11px;
    color: #94a3b8;
}
.file-upload-wrapper input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.file-upload-wrapper .file-name {
    font-size: 12px;
    font-weight: 600;
    color: #17A2B8;
    margin-top: 4px;
    word-break: break-all;
}
.file-upload-wrapper.has-file {
    border-color: #17A2B8;
    background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
}
.file-upload-wrapper.has-file i {
    color: #17A2B8;
}

/* Existing file indicator */
.existing-file-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #2563eb;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    margin-top: 8px;
}
.existing-file-badge i {
    font-size: 12px;
}

/* Submit Button */
.btn-emp201-submit {
    background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
    border: none;
    color: #fff;
    padding: 16px 48px;
    font-weight: 700;
    border-radius: 12px;
    font-size: 16px;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 14px rgba(23, 162, 184, 0.4);
    transition: all 0.3s ease;
    text-transform: uppercase;
}
.btn-emp201-submit:hover {
    background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.5);
    color: #fff;
}
.btn-emp201-submit:active {
    transform: translateY(0);
}
.btn-emp201-submit i {
    margin-right: 8px;
}

/* Section Spacing */
.form-section {
    padding: 28px;
}
.form-section + .section-header-bar {
    margin-top: 0;
}

/* Period / Financial Year Display */
.period-display-bar {
    background: linear-gradient(135deg, #0d3d56 0%, #1a5a6e 100%);
    border-radius: 12px;
    padding: 16px 24px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}
.period-display-bar .period-text {
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.period-display-bar .period-code {
    font-size: 14px;
    font-weight: 500;
    opacity: 0.8;
}

/* Hidden fields row */
.hidden-fields {
    display: none;
}

/* Validation */
.emp201-form .form-control.is-invalid {
    border-color: #ef4444;
}
.emp201-form .invalid-feedback {
    font-size: 12px;
    font-weight: 500;
}

/* Totals Row */
.totals-highlight {
    background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
    border: 2px solid #17A2B8;
    border-radius: 12px;
    padding: 16px 20px;
}
.totals-highlight label {
    color: #0d3d56 !important;
    font-weight: 700 !important;
}
.totals-highlight .form-control {
    border-color: #17A2B8;
    background: #fff;
    color: #0d3d56 !important;
    font-size: 18px !important;
    font-weight: 800 !important;
}

/* Footer */
.smartdash-footer {
    background: linear-gradient(135deg, #0d3d56 0%, #1a5a6e 100%);
    border-radius: 12px;
    padding: 20px 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 -4px 20px rgba(23, 162, 184, 0.15);
}
.smartdash-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #17A2B8 0%, #20c997 50%, #17A2B8 100%);
}
.smartdash-footer .footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}
.smartdash-footer .footer-left {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.smartdash-footer .footer-branding {
    display: flex;
    align-items: center;
    gap: 10px;
}
.smartdash-footer .footer-logo {
    font-size: 18px;
    font-weight: 800;
    background: linear-gradient(135deg, #17A2B8 0%, #20c997 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.smartdash-footer .footer-version {
    background: rgba(23, 162, 184, 0.3);
    color: #7dd3e8;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.smartdash-footer .footer-copyright {
    font-size: 12px;
    color: #94a3b8;
}

@media (max-width: 768px) {
    .smartdash-footer .footer-content {
        flex-direction: column;
        text-align: center;
    }
    .form-section {
        padding: 16px;
    }
    .section-header-bar {
        padding: 14px 16px;
    }
    .section-header-bar h4 {
        font-size: 16px;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ============================================== --}}
    {{-- PAGE HEADER - Breadcrumb & Actions             --}}
    {{-- ============================================== --}}
    <div class="smartdash-page-header mb-4">
        <div class="page-title">
            <div class="page-icon">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h1>{{ isset($declaration) ? 'EDIT EMP201' : 'CREATE EMP201' }}</h1>
                <p>{{ isset($declaration) ? 'Update EMP201 monthly employee tax declaration' : 'Create a new EMP201 monthly employee tax declaration' }}</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/dashboard"><i class="fa fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimsemp201.index') }}">EMP201</a>
            <span class="separator">/</span>
            <span class="current">{{ isset($declaration) ? 'Edit' : 'Create' }}</span>
        </div>
        <div class="page-actions">
            <a href="{{ route('cimsemp201.index') }}" class="btn-page-action" title="Back to EMP201 List">
                <i class="fa fa-arrow-left"></i>
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
            <strong><i class="fa fa-exclamation-triangle"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ============================================== --}}
    {{-- MAIN FORM                                      --}}
    {{-- ============================================== --}}
    <form
        action="{{ isset($declaration) ? route('cimsemp201.update', $declaration->id) : route('cimsemp201.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="emp201-form"
        id="emp201Form"
    >
        @csrf
        @if(isset($declaration))
            @method('PUT')
        @endif

        {{-- Hidden fields for computed values --}}
        <input type="hidden" name="financial_year" id="financial_year" value="{{ old('financial_year', $declaration->financial_year ?? '') }}">
        <input type="hidden" name="period_combo" id="period_combo" value="{{ old('period_combo', $declaration->period_combo ?? '') }}">
        <input type="hidden" name="client_user_id" id="client_user_id" value="{{ old('client_user_id', $declaration->client_user_id ?? '') }}">

        {{-- ============================================== --}}
        {{-- SECTION 1: COMPANY DETAILS                     --}}
        {{-- ============================================== --}}
        <div class="emp201-form-card">
            <div class="section-header-bar">
                <i class="fa-solid fa-building"></i>
                <h4>Company Details</h4>
            </div>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="client_id">Company Name / Trading Name <span class="text-danger">*</span></label>
                            <select
                                id="client_id_select"
                                name="client_id"
                                class="form-control default-select sd_drop_class"
                                data-live-search="true"
                                data-size="8"
                                title="-- Select a Client --"
                                required
                            >
                                <option value="">-- Select a Client --</option>
                                @foreach($clients as $client)
                                    <option
                                        value="{{ $client->client_id }}"
                                        {{ old('client_id', $declaration->client_id ?? '') == $client->client_id ? 'selected' : '' }}
                                        data-company-name="{{ $client->company_name }}"
                                        data-client-code="{{ $client->client_code }}"
                                        data-reg-number="{{ $client->company_reg_number }}"
                                        data-vat-number="{{ $client->vat_number }}"
                                        data-tax-number="{{ $client->tax_number }}"
                                        data-paye-number="{{ $client->paye_number }}"
                                        data-sdl-number="{{ $client->sdl_number }}"
                                        data-uif-number="{{ $client->uif_number }}"
                                        data-sars-title="{{ $client->sars_rep_title }}"
                                        data-sars-initial="{{ $client->sars_rep_initial }}"
                                        data-sars-first-name="{{ $client->sars_rep_first_name }}"
                                        data-sars-surname="{{ $client->sars_rep_surname }}"
                                        data-sars-position="{{ $client->sars_rep_position }}"
                                        data-phone-business="{{ $client->phone_business }}"
                                        data-phone-mobile="{{ $client->phone_mobile }}"
                                        data-phone-whatsapp="{{ $client->phone_whatsapp }}"
                                        data-email="{{ $client->email }}"
                                        data-email-admin="{{ $client->email_admin }}"
                                    >{{ $client->company_name }} ({{ $client->client_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client_code">Client Code</label>
                            <input type="text" id="client_code" name="client_code" class="form-control readonly-field" readonly
                                value="{{ old('client_code', $declaration->client_code ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="company_number">Company Registration Number</label>
                            <input type="text" id="company_number" name="company_number" class="form-control readonly-field" readonly
                                value="{{ old('company_number', $declaration->company_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vat_number">VAT Number</label>
                            <input type="text" id="vat_number" name="vat_number" class="form-control readonly-field" readonly
                                value="{{ old('vat_number', $declaration->vat_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pay_period">Pay Period <span class="text-danger">*</span></label>
                            <select
                                id="pay_period"
                                name="pay_period"
                                class="form-control default-select sd_drop_class"
                                data-live-search="true"
                                data-size="8"
                                title="-- Select Period --"
                                required
                            >
                                <option value="">-- Select Period --</option>
                                @foreach($periods as $period)
                                    <option
                                        value="{{ $period->id }}"
                                        {{ old('pay_period', $declaration->pay_period ?? '') == $period->id ? 'selected' : '' }}
                                        data-period-name="{{ $period->period_name }}"
                                        data-tax-year="{{ $period->tax_year }}"
                                        data-period-combo="{{ $period->period_combo }}"
                                        data-display-order="{{ $period->display_order }}"
                                    >{{ $period->period_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Period Display Bar --}}
                <div class="period-display-bar" id="periodDisplayBar" style="{{ (isset($declaration) && $declaration->pay_period) ? '' : 'display:none;' }}">
                    <div>
                        <div class="period-text" id="periodDisplayText">
                            {{ isset($declaration) ? ($declaration->period_name ?? 'Select a period') : 'Select a period' }}
                        </div>
                        <div class="period-code" id="periodDisplayCode">
                            {{ isset($declaration) ? ('Tax Year: ' . ($declaration->financial_year ?? '')) : '' }}
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 40px; font-weight: 800; opacity: 0.3;">EMP201</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION 2: TAX REFERENCES                      --}}
        {{-- ============================================== --}}
        <div class="emp201-form-card">
            <div class="form-section">
                <div class="section-header-inline">
                    <i class="fa-solid fa-hashtag"></i>
                    <h5>Tax References</h5>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="income_tax_number">Income Tax Number</label>
                            <input type="text" id="income_tax_number" name="income_tax_number" class="form-control readonly-field" readonly
                                value="{{ old('income_tax_number', $declaration->income_tax_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="paye_number">PAYE No</label>
                            <input type="text" id="paye_number" name="paye_number" class="form-control readonly-field" readonly
                                value="{{ old('paye_number', $declaration->paye_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sdl_number">SDL No</label>
                            <input type="text" id="sdl_number" name="sdl_number" class="form-control readonly-field" readonly
                                value="{{ old('sdl_number', $declaration->sdl_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="uif_number">UIF No</label>
                            <input type="text" id="uif_number" name="uif_number" class="form-control readonly-field" readonly
                                value="{{ old('uif_number', $declaration->uif_number ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION 3: PUBLIC OFFICER / CONTACT            --}}
        {{-- ============================================== --}}
        <div class="emp201-form-card">
            <div class="section-header-bar">
                <i class="fa-solid fa-user-tie"></i>
                <h4>Public Officer / Contact Person</h4>
            </div>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" class="form-control readonly-field" readonly
                                value="{{ old('title', $declaration->title ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="initial">Initial</label>
                            <input type="text" id="initial" name="initial" class="form-control readonly-field" readonly
                                value="{{ old('initial', $declaration->initial ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control readonly-field" readonly
                                value="{{ old('first_name', $declaration->first_name ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="surname">Surname</label>
                            <input type="text" id="surname" name="surname" class="form-control readonly-field" readonly
                                value="{{ old('surname', $declaration->surname ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" id="position" name="position" class="form-control readonly-field" readonly
                                value="{{ old('position', $declaration->position ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="telephone_number"><i class="fa fa-phone text-muted"></i> Office Number</label>
                            <input type="text" id="telephone_number" name="telephone_number" class="form-control readonly-field" readonly
                                value="{{ old('telephone_number', $declaration->telephone_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mobile_number"><i class="fa fa-mobile-alt text-muted"></i> Mobile Number</label>
                            <input type="text" id="mobile_number" name="mobile_number" class="form-control readonly-field" readonly
                                value="{{ old('mobile_number', $declaration->mobile_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="whatsapp_number"><i class="fa-brands fa-whatsapp text-muted"></i> WhatsApp Number</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control readonly-field" readonly
                                value="{{ old('whatsapp_number', $declaration->whatsapp_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="home_number"><i class="fa fa-home text-muted"></i> Home Number</label>
                            <input type="text" id="home_number" name="home_number" class="form-control readonly-field" readonly
                                value="{{ old('home_number', $declaration->home_number ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="email"><i class="fa fa-envelope text-muted"></i> Email Address</label>
                            <input type="email" id="email" name="email" class="form-control readonly-field" readonly
                                value="{{ old('email', $declaration->email ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION 4: PAYROLL TAX                         --}}
        {{-- ============================================== --}}
        <div class="emp201-form-card">
            <div class="section-header-bar">
                <i class="fa-solid fa-calculator"></i>
                <h4>Payroll Tax</h4>
            </div>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pay_liability">PAYE Liability <span class="text-danger">*</span></label>
                            <input type="text" id="pay_liability" name="pay_liability"
                                class="form-control currency-input liability-field two-decimals"
                                placeholder="0.00" required
                                value="{{ old('pay_liability', $declaration->pay_liability ?? '0.00') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sdl_liability">SDL Liability <span class="text-danger">*</span></label>
                            <input type="text" id="sdl_liability" name="sdl_liability"
                                class="form-control currency-input liability-field two-decimals"
                                placeholder="0.00" required
                                value="{{ old('sdl_liability', $declaration->sdl_liability ?? '0.00') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="uif_liability">UIF Liability <span class="text-danger">*</span></label>
                            <input type="text" id="uif_liability" name="uif_liability"
                                class="form-control currency-input liability-field two-decimals"
                                placeholder="0.00" required
                                value="{{ old('uif_liability', $declaration->uif_liability ?? '0.00') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION 5: PENALTIES                           --}}
        {{-- ============================================== --}}
        <div class="emp201-form-card">
            <div class="form-section">
                <div class="section-header-inline">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <h5>Penalties & Interest</h5>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="penalty">Penalty</label>
                            <input type="text" id="penalty" name="penalty"
                                class="form-control currency-input liability-field two-decimals"
                                placeholder="0.00"
                                value="{{ old('penalty', $declaration->penalty ?? '0.00') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="interest">Interest</label>
                            <input type="text" id="interest" name="interest"
                                class="form-control currency-input liability-field two-decimals"
                                placeholder="0.00"
                                value="{{ old('interest', $declaration->interest ?? '0.00') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="other">Other</label>
                            <input type="text" id="other" name="other"
                                class="form-control currency-input liability-field two-decimals"
                                placeholder="0.00"
                                value="{{ old('other', $declaration->other ?? '0.00') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION 6: REFERENCE & STATUS                  --}}
        {{-- ============================================== --}}
        <div class="emp201-form-card">
            <div class="form-section">
                <div class="section-header-inline">
                    <i class="fa-solid fa-file-circle-check"></i>
                    <h5>Reference & Status</h5>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="payment_reference">Payment Reference Number</label>
                            <input type="text" id="payment_reference" name="payment_reference"
                                class="form-control readonly-field" readonly
                                value="{{ old('payment_reference', $declaration->payment_reference ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="payment_reference_number">Check Digit</label>
                            <input type="text" id="payment_reference_number" name="payment_reference_number"
                                class="form-control" maxlength="2" style="text-align: center; font-weight: 700; font-size: 18px;"
                                value="{{ old('payment_reference_number', $declaration->payment_reference_number ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select
                                id="status"
                                name="status"
                                class="form-control default-select sd_drop_class"
                                required
                            >
                                <option value="">-- Select Status --</option>
                                <option value="1" {{ old('status', $declaration->status ?? '') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', isset($declaration) && $declaration->status === 0 ? '0' : ($declaration->status ?? '')) == '0' && (old('status') !== null || isset($declaration)) ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group totals-highlight">
                            <label for="tax_payable">Tax Payable (Auto-calculated)</label>
                            <input type="text" id="tax_payable" name="tax_payable"
                                class="form-control currency-input" readonly
                                value="{{ old('tax_payable', $declaration->tax_payable ?? '0.00') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION 7: PAYMENT                             --}}
        {{-- ============================================== --}}
        <div class="emp201-form-card">
            <div class="section-header-bar">
                <i class="fa-solid fa-credit-card"></i>
                <h4>Payment</h4>
            </div>
            <div class="form-section">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_date">Payment Date</label>
                            <input type="date" id="payment_date" name="payment_date"
                                class="form-control"
                                value="{{ old('payment_date', $declaration->payment_date ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_type">Payment Type</label>
                            <select
                                id="payment_type"
                                name="payment_type"
                                class="form-control default-select sd_drop_class"
                            >
                                <option value="">-- Select Payment Type --</option>
                                <option value="cash" {{ old('payment_type', $declaration->payment_type ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="master_card" {{ old('payment_type', $declaration->payment_type ?? '') == 'master_card' ? 'selected' : '' }}>Master Card</option>
                                <option value="bank_transfer" {{ old('payment_type', $declaration->payment_type ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="mobile_payment" {{ old('payment_type', $declaration->payment_type ?? '') == 'mobile_payment' ? 'selected' : '' }}>Mobile Payment</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_amount">Payment Amount</label>
                            <input type="text" id="payment_amount" name="payment_amount"
                                class="form-control currency-input two-decimals"
                                placeholder="0.00"
                                value="{{ old('payment_amount', $declaration->payment_amount ?? '0.00') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group totals-highlight">
                            <label for="balance_outstanding">Balance Outstanding</label>
                            <input type="text" id="balance_outstanding" name="balance_outstanding"
                                class="form-control currency-input" readonly
                                value="{{ old('balance_outstanding', $declaration->balance_outstanding ?? '0.00') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SECTION 8: UPLOAD DOCUMENTS                    --}}
        {{-- ============================================== --}}
        <div class="emp201-form-card">
            <div class="section-header-bar">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <h4>Upload Documents</h4>
            </div>
            <div class="form-section">
                <div class="row">
                    {{-- SARS EMP201 Return --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>SARS EMP201 Return</label>
                            <div class="file-upload-wrapper" id="upload_sars_emp201_return">
                                <i class="fa-solid fa-file-arrow-up"></i>
                                <span class="upload-label">Click to upload</span>
                                <span class="upload-hint">PDF, DOC, DOCX, Images</span>
                                <span class="file-name" id="filename_sars_emp201_return"></span>
                                <input type="file" name="sars_emp201_return" id="sars_emp201_return"
                                    accept="image/*,.pdf,.doc,.docx"
                                    onchange="handleFileSelect(this, 'upload_sars_emp201_return', 'filename_sars_emp201_return')">
                            </div>
                            @if(isset($declaration) && $declaration->file_emp201_return)
                                <div class="existing-file-badge">
                                    <i class="fa fa-paperclip"></i>
                                    {{ $declaration->file_emp201_return }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- SARS PAYE Statement --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>SARS PAYE Statement</label>
                            <div class="file-upload-wrapper" id="upload_sars_paye_statement">
                                <i class="fa-solid fa-file-arrow-up"></i>
                                <span class="upload-label">Click to upload</span>
                                <span class="upload-hint">PDF, DOC, DOCX, Images</span>
                                <span class="file-name" id="filename_sars_paye_statement"></span>
                                <input type="file" name="sars_paye_statement" id="sars_paye_statement"
                                    accept="image/*,.pdf,.doc,.docx"
                                    onchange="handleFileSelect(this, 'upload_sars_paye_statement', 'filename_sars_paye_statement')">
                            </div>
                            @if(isset($declaration) && $declaration->file_emp201_statement)
                                <div class="existing-file-badge">
                                    <i class="fa fa-paperclip"></i>
                                    {{ $declaration->file_emp201_statement }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- EMP201 Working Papers --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>EMP201 Working Papers</label>
                            <div class="file-upload-wrapper" id="upload_emp201_working_papers">
                                <i class="fa-solid fa-file-arrow-up"></i>
                                <span class="upload-label">Click to upload</span>
                                <span class="upload-hint">PDF, DOC, DOCX, Images</span>
                                <span class="file-name" id="filename_emp201_working_papers"></span>
                                <input type="file" name="emp201_working_papers" id="emp201_working_papers"
                                    accept="image/*,.pdf,.doc,.docx"
                                    onchange="handleFileSelect(this, 'upload_emp201_working_papers', 'filename_emp201_working_papers')">
                            </div>
                            @if(isset($declaration) && $declaration->file_emp201_working_papers)
                                <div class="existing-file-badge">
                                    <i class="fa fa-paperclip"></i>
                                    {{ $declaration->file_emp201_working_papers }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- EMP201 Pack --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>EMP201 Pack</label>
                            <div class="file-upload-wrapper" id="upload_emp201_pack">
                                <i class="fa-solid fa-file-arrow-up"></i>
                                <span class="upload-label">Click to upload</span>
                                <span class="upload-hint">PDF, DOC, DOCX, Images</span>
                                <span class="file-name" id="filename_emp201_pack"></span>
                                <input type="file" name="emp201_pack" id="emp201_pack"
                                    accept="image/*,.pdf,.doc,.docx"
                                    onchange="handleFileSelect(this, 'upload_emp201_pack', 'filename_emp201_pack')">
                            </div>
                            @if(isset($declaration) && $declaration->file_emp201_pack)
                                <div class="existing-file-badge">
                                    <i class="fa fa-paperclip"></i>
                                    {{ $declaration->file_emp201_pack }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================== --}}
        {{-- SUBMIT BUTTON                                  --}}
        {{-- ============================================== --}}
        <div class="text-center mb-4">
            <button type="submit" class="btn-emp201-submit" id="submitBtn">
                <i class="fa-solid fa-paper-plane"></i>
                {{ isset($declaration) ? 'UPDATE EMP201 DECLARATION' : 'SUBMIT EMP201 DECLARATION' }}
            </button>
        </div>

    </form>

    {{-- ============================================== --}}
    {{-- FOOTER                                         --}}
    {{-- ============================================== --}}
    <div class="smartdash-footer mt-4">
        <div class="footer-content">
            <div class="footer-left">
                <div class="footer-branding">
                    <span class="footer-logo">SmartDash</span>
                    <span class="footer-version">v1.0</span>
                </div>
                <div class="footer-copyright">
                    &copy; {{ date('Y') }} SmartDash. All rights reserved.
                </div>
            </div>
            <div class="footer-right" style="display:flex; align-items:center;">
                <div style="font-size: 11px; color: #64748b;">
                    Last updated: {{ now()->format('M j, Y H:i') }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ================================================
    // 1. CLIENT SELECTION HANDLER
    // ================================================
    var clientSelect = document.getElementById('client_id_select');
    if (clientSelect) {
        clientSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                clearClientFields();
                return;
            }

            // Populate Company Details
            document.getElementById('client_code').value = selectedOption.getAttribute('data-client-code') || '';
            document.getElementById('company_number').value = selectedOption.getAttribute('data-reg-number') || '';
            document.getElementById('vat_number').value = selectedOption.getAttribute('data-vat-number') || '';
            document.getElementById('client_user_id').value = selectedOption.value || '';

            // Populate Tax References
            document.getElementById('income_tax_number').value = selectedOption.getAttribute('data-tax-number') || '';
            document.getElementById('paye_number').value = selectedOption.getAttribute('data-paye-number') || '';
            document.getElementById('sdl_number').value = selectedOption.getAttribute('data-sdl-number') || '';
            document.getElementById('uif_number').value = selectedOption.getAttribute('data-uif-number') || '';

            // Populate Public Officer / Contact
            document.getElementById('title').value = selectedOption.getAttribute('data-sars-title') || '';
            document.getElementById('initial').value = selectedOption.getAttribute('data-sars-initial') || '';
            document.getElementById('first_name').value = selectedOption.getAttribute('data-sars-first-name') || '';
            document.getElementById('surname').value = selectedOption.getAttribute('data-sars-surname') || '';
            document.getElementById('position').value = selectedOption.getAttribute('data-sars-position') || '';
            document.getElementById('telephone_number').value = selectedOption.getAttribute('data-phone-business') || '';
            document.getElementById('mobile_number').value = selectedOption.getAttribute('data-phone-mobile') || '';
            document.getElementById('whatsapp_number').value = selectedOption.getAttribute('data-phone-whatsapp') || '';
            document.getElementById('home_number').value = '';
            document.getElementById('email').value = selectedOption.getAttribute('data-email-admin') || selectedOption.getAttribute('data-email') || '';

            // Update Payment Reference
            updatePaymentReference();
        });
    }

    // Also support AJAX-based client fetch for richer data
    if (clientSelect) {
        clientSelect.addEventListener('change', function() {
            var clientId = this.value;
            if (!clientId) return;

            fetch('/cims/emp201/api/client/' + clientId, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(function(response) {
                if (!response.ok) return null;
                return response.json();
            })
            .then(function(data) {
                if (!data) return;

                // Override with server-side data if available
                if (data.client_code) document.getElementById('client_code').value = data.client_code;
                if (data.company_reg_number) document.getElementById('company_number').value = data.company_reg_number;
                if (data.vat_number) document.getElementById('vat_number').value = data.vat_number;
                if (data.tax_number) document.getElementById('income_tax_number').value = data.tax_number;
                if (data.paye_number) document.getElementById('paye_number').value = data.paye_number;
                if (data.sdl_number) document.getElementById('sdl_number').value = data.sdl_number;
                if (data.uif_number) document.getElementById('uif_number').value = data.uif_number;
                if (data.sars_rep_title) document.getElementById('title').value = data.sars_rep_title;
                if (data.sars_rep_initial) document.getElementById('initial').value = data.sars_rep_initial;
                if (data.sars_rep_first_name) document.getElementById('first_name').value = data.sars_rep_first_name;
                if (data.sars_rep_surname) document.getElementById('surname').value = data.sars_rep_surname;
                if (data.sars_rep_position) document.getElementById('position').value = data.sars_rep_position;
                if (data.phone_business) document.getElementById('telephone_number').value = data.phone_business;
                if (data.phone_mobile) document.getElementById('mobile_number').value = data.phone_mobile;
                if (data.phone_whatsapp) document.getElementById('whatsapp_number').value = data.phone_whatsapp;
                if (data.email_admin || data.email) document.getElementById('email').value = data.email_admin || data.email;

                // Re-update payment reference after AJAX
                updatePaymentReference();
            })
            .catch(function(err) {
                // Silently fail - data attributes already populated the fields
                console.log('AJAX client fetch not available, using data attributes instead.');
            });
        });
    }

    function clearClientFields() {
        var fieldIds = [
            'client_code', 'company_number', 'vat_number', 'income_tax_number',
            'paye_number', 'sdl_number', 'uif_number', 'title', 'initial',
            'first_name', 'surname', 'position', 'telephone_number',
            'mobile_number', 'whatsapp_number', 'home_number', 'email',
            'payment_reference'
        ];
        fieldIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.value = '';
        });
        document.getElementById('client_user_id').value = '';
    }

    // ================================================
    // 2. PERIOD SELECTION HANDLER
    // ================================================
    var periodSelect = document.getElementById('pay_period');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                document.getElementById('financial_year').value = '';
                document.getElementById('period_combo').value = '';
                document.getElementById('periodDisplayBar').style.display = 'none';
                updatePaymentReference();
                return;
            }

            var periodName = selectedOption.getAttribute('data-period-name') || selectedOption.text;
            var taxYear = selectedOption.getAttribute('data-tax-year') || '';
            var periodCombo = selectedOption.getAttribute('data-period-combo') || '';

            document.getElementById('financial_year').value = taxYear;
            document.getElementById('period_combo').value = periodCombo;

            // Update period display bar
            var displayBar = document.getElementById('periodDisplayBar');
            displayBar.style.display = 'flex';
            document.getElementById('periodDisplayText').textContent = periodName;
            document.getElementById('periodDisplayCode').textContent = 'Tax Year: ' + taxYear + (periodCombo ? ' | Period: ' + periodCombo : '');

            // Update payment reference
            updatePaymentReference();
        });
    }

    // ================================================
    // 3. AUTO-CALCULATE TAX PAYABLE
    // ================================================
    function calculateTaxPayable() {
        var sum = 0;
        var liabilityFields = document.querySelectorAll('.liability-field');
        liabilityFields.forEach(function(input) {
            var value = input.value.replace(/\s/g, '').replace(/,/g, '');
            var parsed = parseFloat(value);
            if (!isNaN(parsed)) {
                sum += parsed;
            }
        });

        var formattedSum = formatCurrency(sum);
        document.getElementById('tax_payable').value = formattedSum;

        // Recalculate balance outstanding
        calculateBalanceOutstanding();
    }

    // Attach listeners to all liability fields
    var liabilityFields = document.querySelectorAll('.liability-field');
    liabilityFields.forEach(function(input) {
        input.addEventListener('input', calculateTaxPayable);
        input.addEventListener('change', calculateTaxPayable);
    });

    // ================================================
    // 4. AUTO-CALCULATE BALANCE OUTSTANDING
    // ================================================
    function calculateBalanceOutstanding() {
        var taxPayableStr = document.getElementById('tax_payable').value.replace(/\s/g, '').replace(/,/g, '');
        var paymentAmountStr = document.getElementById('payment_amount').value.replace(/\s/g, '').replace(/,/g, '');

        var taxPayable = parseFloat(taxPayableStr) || 0;
        var paymentAmount = parseFloat(paymentAmountStr) || 0;

        var balance = taxPayable - paymentAmount;
        document.getElementById('balance_outstanding').value = formatCurrency(balance);
    }

    var paymentAmountField = document.getElementById('payment_amount');
    if (paymentAmountField) {
        paymentAmountField.addEventListener('input', calculateBalanceOutstanding);
        paymentAmountField.addEventListener('change', calculateBalanceOutstanding);
    }

    // ================================================
    // 5. NUMERIC FORMATTING (2 Decimal Places)
    // ================================================
    function formatCurrency(value) {
        var num = parseFloat(value);
        if (isNaN(num)) num = 0;
        var fixed = num.toFixed(2);
        var parts = fixed.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return parts[0] + '.' + parts[1];
    }

    var twoDecimalFields = document.querySelectorAll('.two-decimals');
    twoDecimalFields.forEach(function(input) {
        input.addEventListener('input', function() {
            var value = this.value;
            // Remove everything except digits and decimal point
            value = value.replace(/[^0-9.]/g, '');

            // Ensure only one decimal point
            var parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
                parts = value.split('.');
            }

            // Limit to 2 decimal places
            if (parts.length === 2) {
                parts[1] = parts[1].slice(0, 2);
                value = parts.join('.');
            }

            // Format integer part with space separators
            var integerPart = parts[0];
            var formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

            if (parts.length === 2) {
                this.value = formattedInteger + '.' + parts[1];
            } else {
                this.value = formattedInteger;
            }
        });

        input.addEventListener('blur', function() {
            var value = this.value.replace(/\s/g, '');
            if (value !== '' && !isNaN(parseFloat(value))) {
                this.value = formatCurrency(value);
            } else if (value === '') {
                this.value = '0.00';
            }
        });
    });

    // ================================================
    // 6. AUTO-GENERATE PAYMENT REFERENCE
    // ================================================
    function updatePaymentReference() {
        var payeNumber = document.getElementById('paye_number').value || '';
        var periodCombo = document.getElementById('period_combo').value || '';

        var reference = '';
        if (payeNumber && periodCombo) {
            reference = payeNumber + ' LC ' + periodCombo;
        } else if (payeNumber) {
            reference = payeNumber + ' LC ';
        }

        document.getElementById('payment_reference').value = reference;
    }

    // ================================================
    // 7. CHECK DIGIT - Limit to 2 numeric characters
    // ================================================
    var checkDigitField = document.getElementById('payment_reference_number');
    if (checkDigitField) {
        checkDigitField.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);
        });
    }

    // ================================================
    // 8. INITIALIZE: Pre-populate on edit mode
    // ================================================
    @if(isset($declaration))
        // Trigger calculations on page load for edit mode
        calculateTaxPayable();
        calculateBalanceOutstanding();

        // Show period display bar if period is selected
        var periodSelectEl = document.getElementById('pay_period');
        if (periodSelectEl && periodSelectEl.value) {
            var selectedPeriodOption = periodSelectEl.options[periodSelectEl.selectedIndex];
            if (selectedPeriodOption && selectedPeriodOption.value) {
                var displayBar = document.getElementById('periodDisplayBar');
                displayBar.style.display = 'flex';
            }
        }
    @else
        // Initialize tax payable on create mode
        calculateTaxPayable();
    @endif

    // ================================================
    // 9. FORM SUBMISSION VALIDATION
    // ================================================
    var form = document.getElementById('emp201Form');
    if (form) {
        form.addEventListener('submit', function(e) {
            var clientId = document.getElementById('client_id_select').value;
            var payPeriod = document.getElementById('pay_period').value;

            if (!clientId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select a client before submitting.',
                    confirmButtonColor: '#17A2B8'
                });
                return false;
            }

            if (!payPeriod) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select a pay period before submitting.',
                    confirmButtonColor: '#17A2B8'
                });
                return false;
            }

            // Strip formatting from currency fields before submit
            var currencyInputs = document.querySelectorAll('.currency-input');
            currencyInputs.forEach(function(input) {
                input.value = input.value.replace(/\s/g, '');
            });
        });
    }

    // ================================================
    // SUCCESS / ERROR FLASH MESSAGES (SweetAlert2)
    // ================================================
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
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
            html: '<div style="font-size: 16px;">{!! addslashes(session('error')) !!}</div>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545',
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    @endif

});

// ================================================
// FILE UPLOAD HANDLER (Global function)
// ================================================
function handleFileSelect(input, wrapperId, filenameId) {
    var wrapper = document.getElementById(wrapperId);
    var filenameSpan = document.getElementById(filenameId);

    if (input.files && input.files[0]) {
        var fileName = input.files[0].name;
        filenameSpan.textContent = fileName;
        wrapper.classList.add('has-file');

        // Update icon based on file type
        var ext = fileName.split('.').pop().toLowerCase();
        var icon = wrapper.querySelector('i');
        if (ext === 'pdf') {
            icon.className = 'fa-solid fa-file-pdf';
            icon.style.color = '#ef4444';
        } else if (ext === 'doc' || ext === 'docx') {
            icon.className = 'fa-solid fa-file-word';
            icon.style.color = '#2563eb';
        } else {
            icon.className = 'fa-solid fa-file-image';
            icon.style.color = '#17A2B8';
        }

        // Update label text
        var labelSpan = wrapper.querySelector('.upload-label');
        if (labelSpan) labelSpan.textContent = 'File selected';
    } else {
        filenameSpan.textContent = '';
        wrapper.classList.remove('has-file');
        var icon = wrapper.querySelector('i');
        icon.className = 'fa-solid fa-file-arrow-up';
        icon.style.color = '';
        var labelSpan = wrapper.querySelector('.upload-label');
        if (labelSpan) labelSpan.textContent = 'Click to upload';
    }
}
</script>
@endpush
