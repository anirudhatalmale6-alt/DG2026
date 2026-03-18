@extends('layouts.default')

@section('title', 'Edit Appointment')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Edit #{{ $appointment->id }}</li>
        </ol>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'appointments', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-edit me-2"></i>Edit Appointment #{{ $appointment->id }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Client:</strong> {{ $appointment->client_name ?? 'N/A' }}
                        @if($appointment->client_code) ({{ $appointment->client_code }}) @endif
                    </div>

                    <form method="POST" action="{{ route('cimsappointments.appointments.update', $appointment->id) }}">
                        @csrf @method('PUT')

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Service</label>
                                <select name="service_id" class="form-select">
                                    @foreach($services as $svc)
                                        <option value="{{ $svc->id }}" {{ $appointment->service_id == $svc->id ? 'selected' : '' }}>{{ $svc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Staff Member</label>
                                <select name="staff_id" class="form-select">
                                    @foreach($staffList as $st)
                                        <option value="{{ $st->id }}" {{ $appointment->staff_id == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-select" onchange="toggleCancelReason(this.value)">
                                    @foreach(\Modules\CIMSAppointments\Models\Appointment::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ $appointment->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" name="appointment_date" class="form-control" value="{{ $appointment->appointment_date->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="{{ substr($appointment->start_time, 0, 5) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Duration (Hours)</label>
                                <select name="duration_hours" class="form-select">
                                    @for($h = 1; $h <= 8; $h++)
                                        <option value="{{ $h }}" {{ $appointment->duration_hours == $h ? 'selected' : '' }}>{{ $h }} Hour{{ $h > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Payment Status</label>
                                <select name="payment_status" class="form-select">
                                    @foreach(\Modules\CIMSAppointments\Models\Appointment::PAYMENT_STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ $appointment->payment_status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="cancelReasonSection" style="{{ $appointment->status === 'cancelled' ? '' : 'display:none;' }}">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cancellation Reason</label>
                                <input type="text" name="cancellation_reason" class="form-control" value="{{ $appointment->cancellation_reason }}" placeholder="Reason for cancellation...">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Client Notes</label>
                                <textarea name="notes" class="form-control" rows="3">{{ $appointment->notes }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Internal Notes</label>
                                <textarea name="internal_notes" class="form-control" rows="3">{{ $appointment->internal_notes }}</textarea>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Update Appointment</button>
                            <a href="{{ route('cimsappointments.appointments.show', $appointment->id) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCancelReason(status) {
    document.getElementById('cancelReasonSection').style.display = status === 'cancelled' ? 'block' : 'none';
}
</script>
@endpush
