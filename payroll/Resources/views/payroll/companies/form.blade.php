@extends('layouts.default')

@section('title', ($company ? 'Edit' : 'Add') . ' Company')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
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

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ $company ? route('cimspayroll.companies.update', $company->id) : route('cimspayroll.companies.store') }}">
                @csrf
                @if($company) @method('PUT') @endif

                <div class="card smartdash-form-card">
                    <div class="card-header"><h4><i class="fas fa-building"></i> COMPANY DETAILS</h4></div>
                    <div class="card-body">

                        <!-- General Information -->
                        <div class="form-section-title"><i class="fa fa-info-circle"></i> GENERAL INFORMATION</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $company->company_name ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trading Name</label>
                                    <input type="text" name="trading_name" class="form-control" value="{{ old('trading_name', $company->trading_name ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">CIPC Registration Number</label>
                                    <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number', $company->registration_number ?? '') }}" placeholder="e.g. 2020/123456/07">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $company->email ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="form-section-title"><i class="fa fa-map-marker-alt"></i> ADDRESS</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address Line 1</label>
                                    <input type="text" name="address_line1" class="form-control" value="{{ old('address_line1', $company->address_line1 ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" name="address_line2" class="form-control" value="{{ old('address_line2', $company->address_line2 ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $company->city ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Province</label>
                                    <select name="province" class="form-control">
                                        <option value="">-- Select Province --</option>
                                        @foreach(['Eastern Cape','Free State','Gauteng','KwaZulu-Natal','Limpopo','Mpumalanga','North West','Northern Cape','Western Cape'] as $p)
                                        <option value="{{ $p }}" {{ old('province', $company->province ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $company->postal_code ?? '') }}" maxlength="10">
                                </div>
                            </div>
                        </div>

                        <!-- SARS -->
                        <div class="form-section-title"><i class="fa fa-landmark"></i> SARS REGISTRATION</div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">PAYE Reference Number</label>
                                    <input type="text" name="paye_reference" class="form-control" value="{{ old('paye_reference', $company->paye_reference ?? '') }}" placeholder="e.g. 7000000000">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">UIF Reference Number</label>
                                    <input type="text" name="uif_reference" class="form-control" value="{{ old('uif_reference', $company->uif_reference ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">SDL Reference Number</label>
                                    <input type="text" name="sdl_reference" class="form-control" value="{{ old('sdl_reference', $company->sdl_reference ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="form-section-title"><i class="fa fa-clock"></i> WORKING HOURS (BCEA DEFAULTS)</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Pay Frequency <span class="text-danger">*</span></label>
                                    <select name="pay_frequency" class="form-control" required>
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

                        @if($company)
                        <!-- Status -->
                        <div class="form-section-title"><i class="fa fa-toggle-on"></i> STATUS</div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="is_active" class="form-control" required>
                                        <option value="1" {{ old('is_active', $company->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active', $company->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

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
