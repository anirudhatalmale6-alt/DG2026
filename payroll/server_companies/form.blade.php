@extends('layouts.default')

@section('title', ($company ? 'Edit' : 'Add') . ' Company')

@push('styles')
<style>.payroll-wrapper { max-width: none; margin: 0; padding: 30px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-building"></i></div>
            <div>
                <h1>{{ $company ? 'Edit' : 'Add' }} Company</h1>
                <p>{{ $company ? 'Update company details' : 'Register a new payroll company' }}</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.companies.index') }}">Companies</a>
            <span class="separator">/</span>
            <span class="current">{{ $company ? 'Edit' : 'Add' }}</span>
        </div>
        <a href="{{ route('cimspayroll.companies.index') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ $company ? route('cimspayroll.companies.update', $company->id) : route('cimspayroll.companies.store') }}">
                @csrf
                @if($company) @method('PUT') @endif

                <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id', $company->client_id ?? '') }}">
                <input type="hidden" name="client_code" id="client_code" value="{{ old('client_code', $company->client_code ?? '') }}">

                <div class="card smartdash-form-card">
                    <div class="card-header"><h4><i class="fas fa-building"></i> COMPANY DETAILS</h4></div>
                    <div class="card-body">

                        <!-- Select Client -->
                        <div class="form-section-title"><i class="fa fa-link"></i> SELECT CLIENT FROM CLIENT MASTER</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Client <span class="text-danger">*</span></label>
                                    <select id="client_selector" class="sd_drop_class" data-live-search="true" title="-- Select a Client --" {{ $company ? 'disabled' : '' }}>
                                        @foreach($clients as $c)
                                        <option value="{{ $c->client_id }}" data-code="{{ $c->client_code }}" {{ old('client_id', $company->client_id ?? '') == $c->client_id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Client Code</label>
                                    <input type="text" id="client_code_display" class="form-control" value="{{ old('client_code', $company->client_code ?? '') }}" readonly style="background:#fff;font-weight:700;">
                                </div>
                            </div>
                        </div>

                        <!-- General Information -->
                        <div class="form-section-title"><i class="fa fa-info-circle"></i> GENERAL INFORMATION</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" id="f_company_name" class="form-control cm-field" value="{{ old('company_name', $company->company_name ?? '') }}" required {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trading Name</label>
                                    <input type="text" name="trading_name" id="f_trading_name" class="form-control cm-field" value="{{ old('trading_name', $company->trading_name ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">CIPC Registration Number</label>
                                    <input type="text" name="registration_number" id="f_registration_number" class="form-control cm-field" value="{{ old('registration_number', $company->registration_number ?? '') }}" placeholder="e.g. 2020/123456/07" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" id="f_email" class="form-control cm-field" value="{{ old('email', $company->email ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" id="f_phone" class="form-control cm-field" value="{{ old('phone', $company->phone ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="form-section-title"><i class="fa fa-map-marker-alt"></i> ADDRESS</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address Line 1</label>
                                    <input type="text" name="address_line1" id="f_address_line1" class="form-control cm-field" value="{{ old('address_line1', $company->address_line1 ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" name="address_line2" id="f_address_line2" class="form-control cm-field" value="{{ old('address_line2', $company->address_line2 ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" id="f_city" class="form-control cm-field" value="{{ old('city', $company->city ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Province</label>
                                    <input type="text" name="province" id="f_province" class="form-control cm-field" value="{{ old('province', $company->province ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" id="f_postal_code" class="form-control cm-field" value="{{ old('postal_code', $company->postal_code ?? '') }}" maxlength="10" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- SARS -->
                        <div class="form-section-title"><i class="fa fa-landmark"></i> SARS REGISTRATION</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">PAYE Reference Number</label>
                                    <input type="text" name="paye_reference" id="f_paye_reference" class="form-control cm-field" value="{{ old('paye_reference', $company->paye_reference ?? '') }}" placeholder="e.g. 7000000000" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">UIF Reference Number</label>
                                    <input type="text" name="uif_reference" id="f_uif_reference" class="form-control cm-field" value="{{ old('uif_reference', $company->uif_reference ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">SDL Reference Number</label>
                                    <input type="text" name="sdl_reference" id="f_sdl_reference" class="form-control cm-field" value="{{ old('sdl_reference', $company->sdl_reference ?? '') }}" {{ ($company && $company->client_id) ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="form-section-title"><i class="fa fa-clock"></i> WORKING HOURS (BCEA DEFAULTS)</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Pay Frequency <span class="text-danger">*</span></label>
                                    <select name="pay_frequency" class="sd_drop_class" required>
                                        <option value="monthly" {{ old('pay_frequency', $company->pay_frequency ?? 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="fortnightly" {{ old('pay_frequency', $company->pay_frequency ?? '') === 'fortnightly' ? 'selected' : '' }}>Fortnightly</option>
                                        <option value="weekly" {{ old('pay_frequency', $company->pay_frequency ?? '') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Hours / Month <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="normal_hours_month" class="form-control" value="{{ old('normal_hours_month', $company->normal_hours_month ?? '195.00') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Days / Month <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="normal_days_month" class="form-control" value="{{ old('normal_days_month', $company->normal_days_month ?? '21.67') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Hours / Day <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="normal_hours_day" class="form-control" value="{{ old('normal_hours_day', $company->normal_hours_day ?? '9.00') }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Tax Registrations -->
                        <div class="form-section-title"><i class="fa fa-toggle-on"></i> STATUS & TAX REGISTRATIONS</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="is_active" class="sd_drop_class" required>
                                        <option value="1" {{ old('is_active', $company->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active', $company->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">PAYE Registered</label>
                                    <select name="is_paye" class="sd_drop_class">
                                        <option value="1" {{ old('is_paye', $company->is_paye ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('is_paye', $company->is_paye ?? 1) == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">UIF Registered</label>
                                    <select name="is_uif" class="sd_drop_class">
                                        <option value="1" {{ old('is_uif', $company->is_uif ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('is_uif', $company->is_uif ?? 1) == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">SDL Registered</label>
                                    <select name="is_sdl" class="sd_drop_class">
                                        <option value="1" {{ old('is_sdl', $company->is_sdl ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('is_sdl', $company->is_sdl ?? 1) == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 16px;">
                            <button type="submit" class="btn {{ $company ? 'button_master_update' : 'button_master_save' }}"><i class="fa fa-save"></i> {{ $company ? 'Update Company' : 'Save Company' }}</button>
                            <a href="{{ route('cimspayroll.companies.index') }}" class="btn button_master_cancel"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize all selectpickers
    $('.sd_drop_class').selectpicker();

    @if($errors->any())
    CIMSAlert.error('<ul style="text-align:left;margin:0;padding-left:20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>');
    @endif

    var $selector = $('#client_selector');
    if ($selector.length && !$selector.prop('disabled')) {
        $selector.on('changed.bs.select', function() {
            var clientId = $(this).val();
            if (!clientId) {
                // Clear all fields and make editable
                $('.cm-field').each(function() {
                    $(this).val('').removeAttr('readonly').css('background', '');
                });
                $('#client_id').val('');
                $('#client_code').val('');
                $('#client_code_display').val('');
                return;
            }

            $selector.prop('disabled', true).selectpicker('refresh');

            $.getJSON('{{ url("cims/payroll/companies/client-lookup") }}/' + clientId, function(data) {
                $('#client_id').val(clientId);
                $('#client_code').val(data.client_code || '');
                $('#client_code_display').val(data.client_code || '');

                var fieldMap = {
                    'f_company_name': data.company_name,
                    'f_trading_name': data.trading_name,
                    'f_registration_number': data.registration_number,
                    'f_email': data.email,
                    'f_phone': data.phone,
                    'f_paye_reference': data.paye_reference,
                    'f_uif_reference': data.uif_reference,
                    'f_sdl_reference': data.sdl_reference,
                    'f_address_line1': data.address_line1,
                    'f_address_line2': data.address_line2,
                    'f_city': data.city,
                    'f_province': data.province,
                    'f_postal_code': data.postal_code
                };

                for (var id in fieldMap) {
                    $('#' + id).val(fieldMap[id] || '').attr('readonly', 'readonly');
                }

                $selector.prop('disabled', false).selectpicker('refresh');
            }).fail(function() {
                CIMSAlert.error('Failed to load client data. Please try again.');
                $selector.prop('disabled', false).selectpicker('refresh');
            });
        });
    }
});
</script>
@endpush
