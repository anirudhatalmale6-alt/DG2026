@extends('layouts.default')

@section('title', 'Add Staff Member')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.staff.index') }}">Staff</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'staff', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0"><i class="fas fa-user-plus me-2"></i>Add Staff Member</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cimsappointments.staff.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Position</label>
                                <input type="text" name="position" class="form-control" value="{{ old('position') }}" placeholder="e.g. Tax Consultant">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Link to System User</label>
                                <select name="user_id" class="form-select">
                                    <option value="">-- Not Linked --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ trim($u->first_name . ' ' . $u->last_name) }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Calendar Color</label>
                                <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', '#17A2B8') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Services This Staff Can Provide</label>
                            <div class="row">
                                @foreach($services as $svc)
                                    <div class="col-md-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="services[]" value="{{ $svc->id }}" id="svc{{ $svc->id }}"
                                                {{ in_array($svc->id, old('services', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="svc{{ $svc->id }}">{{ $svc->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>Default availability will be set to Mon-Fri 8am-5pm, Saturday 9am-1pm. You can customize this after creating the staff member.
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Add Staff Member</button>
                            <a href="{{ route('cimsappointments.staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
