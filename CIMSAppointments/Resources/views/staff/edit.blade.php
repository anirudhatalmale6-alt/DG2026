@extends('layouts.default')

@section('title', 'Edit Staff')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.staff.index') }}">Staff</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'staff', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0"><i class="fas fa-user-edit me-2"></i>Edit: {{ $staff->name }}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cimsappointments.staff.update', $staff->id) }}">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $staff->name) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->phone) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Position</label>
                                <input type="text" name="position" class="form-control" value="{{ old('position', $staff->position) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Link to System User</label>
                                <select name="user_id" class="form-select">
                                    <option value="">-- Not Linked --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ old('user_id', $staff->user_id) == $u->id ? 'selected' : '' }}>{{ trim($u->first_name . ' ' . $u->last_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Calendar Color</label>
                                <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $staff->color ?? '#17A2B8') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Services</label>
                            @php $staffServiceIds = $staff->services->pluck('id')->toArray(); @endphp
                            <div class="row">
                                @foreach($services as $svc)
                                    <div class="col-md-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="services[]" value="{{ $svc->id }}" id="svc{{ $svc->id }}"
                                                {{ in_array($svc->id, old('services', $staffServiceIds)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="svc{{ $svc->id }}">{{ $svc->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Update</button>
                            <a href="{{ route('cimsappointments.staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
