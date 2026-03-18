@extends('layouts.default')

@section('title', 'Appointment Settings')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'settings', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <form method="POST" action="{{ route('cimsappointments.settings.update') }}">
                @csrf

                {{-- General Settings --}}
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-building me-2"></i>Company Information</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Company Name</label>
                                <input type="text" name="company_name" class="form-control" value="{{ $settings['company_name'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Company Email</label>
                                <input type="email" name="company_email" class="form-control" value="{{ $settings['company_email'] ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Company Phone</label>
                                <input type="text" name="company_phone" class="form-control" value="{{ $settings['company_phone'] ?? '' }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Company Address</label>
                                <input type="text" name="company_address" class="form-control" value="{{ $settings['company_address'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Email Settings --}}
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-envelope me-2"></i>Email Notifications</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="confirmation_email_enabled" value="1" id="confEmail"
                                        {{ ($settings['confirmation_email_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="confEmail">Confirmation Email</label>
                                </div>
                                <small class="text-muted">Sent when appointment is booked</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="reminder_email_enabled" value="1" id="remEmail"
                                        {{ ($settings['reminder_email_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="remEmail">Reminder Email</label>
                                </div>
                                <small class="text-muted">Sent before the appointment</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="cancellation_email_enabled" value="1" id="canEmail"
                                        {{ ($settings['cancellation_email_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="canEmail">Cancellation Email</label>
                                </div>
                                <small class="text-muted">Sent when appointment is cancelled</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Reminder Hours Before</label>
                                <input type="number" name="reminder_hours_before" class="form-control" value="{{ $settings['reminder_hours_before'] ?? 24 }}" min="1" max="72">
                                <small class="text-muted">How many hours before the appointment to send reminder</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Booking Settings --}}
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-calendar-check me-2"></i>Booking Rules</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Booking Buffer (Hours)</label>
                                <input type="number" name="booking_buffer_hours" class="form-control" value="{{ $settings['booking_buffer_hours'] ?? 2 }}" min="0" max="48">
                                <small class="text-muted">Minimum notice required for new bookings</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Cancellation Policy (Hours)</label>
                                <input type="number" name="cancellation_policy_hours" class="form-control" value="{{ $settings['cancellation_policy_hours'] ?? 24 }}" min="0" max="72">
                                <small class="text-muted">Minimum notice for free cancellation</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Default Slot Duration (Min)</label>
                                <select name="default_slot_duration" class="form-select">
                                    <option value="60" {{ ($settings['default_slot_duration'] ?? 60) == 60 ? 'selected' : '' }}>60 min (1 hour)</option>
                                    <option value="30" {{ ($settings['default_slot_duration'] ?? 60) == 30 ? 'selected' : '' }}>30 min</option>
                                    <option value="120" {{ ($settings['default_slot_duration'] ?? 60) == 120 ? 'selected' : '' }}>120 min (2 hours)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Settings</button>
            </form>

            {{-- Global Blocked Dates --}}
            <div class="card mt-4">
                <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-ban me-2"></i>Global Blocked Dates (All Staff)</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cimsappointments.settings.blocked-dates.store') }}" class="row g-2 mb-3">
                        @csrf
                        <div class="col-md-3">
                            <input type="date" name="blocked_date" class="form-control form-control-sm" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason (e.g. Public Holiday)">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-warning"><i class="fas fa-plus me-1"></i>Add Global Block</button>
                        </div>
                    </form>

                    @if($globalBlockedDates->count() > 0)
                        <table class="table table-sm">
                            <thead><tr><th>Date</th><th>Reason</th><th></th></tr></thead>
                            <tbody>
                                @foreach($globalBlockedDates as $bd)
                                    <tr>
                                        <td>{{ $bd->blocked_date->format('d M Y (l)') }}</td>
                                        <td>{{ $bd->reason ?? '-' }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('cimsappointments.settings.blocked-dates.destroy', $bd->id) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">No global blocked dates.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
