@extends('cimsclients::layouts.default')

@section('title', isset($client) ? 'Edit Client' : 'New Client')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="/public/smartdash/vendor/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet">
<link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.form-section { margin-bottom: 30px; }
.form-section-header {
    background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px 8px 0 0;
    font-weight: 600;
    font-size: 15px;
}
.form-section-body {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-top: none;
    padding: 20px;
    border-radius: 0 0 8px 8px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row page-titles mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0)">CIMS</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimsclients.index') }}">Clients</a></li>
                <li class="breadcrumb-item active">{{ isset($client) ? 'Edit' : 'New' }}</li>
            </ol>
            <a href="{{ route('cimsclients.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to List
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {!! session('error') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ isset($client) ? route('cimsclients.update', $client->id) : route('cimsclients.store') }}" method="POST" id="clientForm">
        @csrf
        @if(isset($client))
            @method('PUT')
        @endif

        <!-- Company Details Section -->
        <div class="form-section">
            <div class="form-section-header">
                <i class="fa fa-building me-2"></i> Company Details
            </div>
            <div class="form-section-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Registered Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="registered_company_name" class="form-control" required
                               value="{{ old('registered_company_name', $client->registered_company_name ?? '') }}"
                               placeholder="Full registered name">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Trading Name</label>
                        <input type="text" name="trading_name" class="form-control"
                               value="{{ old('trading_name', $client->trading_name ?? '') }}"
                               placeholder="Trading as...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Client Code</label>
                        <input type="text" name="client_code" class="form-control"
                               value="{{ old('client_code', $client->client_code ?? '') }}"
                               placeholder="ABC100">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Financial Year End</label>
                        <select name="financial_year_end" class="form-select">
                            <option value="">--</option>
                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                <option value="{{ $month }}" {{ old('financial_year_end', $client->financial_year_end ?? '') == $month ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Company Reg Number</label>
                        <input type="text" name="company_reg_number" class="form-control"
                               value="{{ old('company_reg_number', $client->company_reg_number ?? '') }}"
                               placeholder="2024/123456/07">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Company Type</label>
                        <select name="company_type_code" class="form-select">
                            <option value="">--</option>
                            <option value="07" {{ old('company_type_code', $client->company_type_code ?? '') == '07' ? 'selected' : '' }}>Private Company (Pty) Ltd</option>
                            <option value="21" {{ old('company_type_code', $client->company_type_code ?? '') == '21' ? 'selected' : '' }}>Non-Profit Company (NPC)</option>
                            <option value="06" {{ old('company_type_code', $client->company_type_code ?? '') == '06' ? 'selected' : '' }}>Public Company Ltd</option>
                            <option value="23" {{ old('company_type_code', $client->company_type_code ?? '') == '23' ? 'selected' : '' }}>Personal Liability Company Inc</option>
                            <option value="CC" {{ old('company_type_code', $client->company_type_code ?? '') == 'CC' ? 'selected' : '' }}>Close Corporation CC</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Date of Registration</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            <input type="text" name="date_of_registration" class="form-control datepicker-past"
                                   value="{{ old('date_of_registration', $client->date_of_registration ?? '') }}"
                                   placeholder="Select date">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">BizPortal Number</label>
                        <input type="text" name="bizportal_number" class="form-control"
                               value="{{ old('bizportal_number', $client->bizportal_number ?? '') }}"
                               placeholder="BizPortal reference">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Registration Section -->
        <div class="form-section">
            <div class="form-section-header">
                <i class="fa fa-file-invoice me-2"></i> Tax Registration
            </div>
            <div class="form-section-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Income Tax Number</label>
                        <input type="text" name="income_tax_number" class="form-control"
                               value="{{ old('income_tax_number', $client->income_tax_number ?? '') }}"
                               placeholder="9xxxxxxxxxx">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Income Tax Reg Date</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            <input type="text" name="income_tax_reg_date" class="form-control datepicker-past"
                                   value="{{ old('income_tax_reg_date', $client->income_tax_reg_date ?? '') }}"
                                   placeholder="Select date">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Year of Liability</label>
                        <input type="text" name="income_tax_year_of_liability" class="form-control"
                               value="{{ old('income_tax_year_of_liability', $client->income_tax_year_of_liability ?? '') }}"
                               placeholder="e.g., 2024">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">VAT Number</label>
                        <input type="text" name="vat_number" class="form-control"
                               value="{{ old('vat_number', $client->vat_number ?? '') }}"
                               placeholder="4xxxxxxxxxx">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">VAT Reg Date</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            <input type="text" name="vat_reg_date" class="form-control datepicker-past"
                                   value="{{ old('vat_reg_date', $client->vat_reg_date ?? '') }}"
                                   placeholder="Select date">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">VAT Cycle</label>
                        <select name="vat_cycle" class="form-select">
                            <option value="">--</option>
                            <option value="Monthly" {{ old('vat_cycle', $client->vat_cycle ?? '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="Bi-Monthly" {{ old('vat_cycle', $client->vat_cycle ?? '') == 'Bi-Monthly' ? 'selected' : '' }}>Bi-Monthly</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Registration Section -->
        <div class="form-section">
            <div class="form-section-header">
                <i class="fa fa-users me-2"></i> Payroll Registration
            </div>
            <div class="form-section-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">PAYE Number</label>
                        <input type="text" name="paye_number" class="form-control"
                               value="{{ old('paye_number', $client->paye_number ?? '') }}"
                               placeholder="7xxxxxxxxxx">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">SDL Number</label>
                        <input type="text" name="sdl_number" class="form-control"
                               value="{{ old('sdl_number', $client->sdl_number ?? '') }}"
                               placeholder="Lxxxxxxxxxx">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">UIF Number</label>
                        <input type="text" name="uif_number" class="form-control"
                               value="{{ old('uif_number', $client->uif_number ?? '') }}"
                               placeholder="Uxxxxxxxxxx">
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="form-section">
            <div class="form-section-header">
                <i class="fa fa-phone me-2"></i> Contact Information
            </div>
            <div class="form-section-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control"
                               value="{{ old('contact_person', $client->contact_person ?? '') }}"
                               placeholder="Full name">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Phone</label>
                        <input type="tel" name="contact_phone" class="form-control"
                               value="{{ old('contact_phone', $client->contact_phone ?? '') }}"
                               placeholder="Phone number">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control"
                               value="{{ old('contact_email', $client->contact_email ?? '') }}"
                               placeholder="email@company.co.za">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Physical Address</label>
                        <textarea name="physical_address" class="form-control" rows="2"
                                  placeholder="Street address">{{ old('physical_address', $client->physical_address ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Postal Address</label>
                        <textarea name="postal_address" class="form-control" rows="2"
                                  placeholder="Postal address">{{ old('postal_address', $client->postal_address ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="form-section">
            <div class="form-section-header">
                <i class="fa fa-sticky-note me-2"></i> Notes
            </div>
            <div class="form-section-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <textarea name="notes" class="form-control" rows="4"
                                  placeholder="Any additional notes...">{{ old('notes', $client->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-between mt-4 mb-5">
            <a href="{{ route('cimsclients.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-times me-2"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save me-2"></i> {{ isset($client) ? 'Update Client' : 'Save Client' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script src="/public/smartdash/vendor/moment/moment.min.js"></script>
<script src="/public/smartdash/vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
<script src="/public/smartdash/js/smartdash-dates.js"></script>
@endpush
