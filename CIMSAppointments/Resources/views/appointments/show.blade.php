@extends('layouts.default')

@section('title', 'Appointment Details')

@push('styles')
<style>
.badge-pending { background: #ffc107; color: #333; }
.badge-confirmed { background: #17a2b8; color: #fff; }
.badge-completed { background: #28a745; color: #fff; }
.badge-cancelled { background: #dc3545; color: #fff; }
.badge-no_show { background: #6c757d; color: #fff; }
.detail-card { border-radius: 12px; border: none; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
.detail-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #888; font-weight: 700; margin-bottom: 4px; }
.detail-value { font-size: 15px; font-weight: 500; color: #333; margin-bottom: 15px; }
.status-actions { display: flex; gap: 8px; flex-wrap: wrap; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.appointments.index') }}">All</a></li>
            <li class="breadcrumb-item active">#{{ $appointment->id }}</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-xl-3 col-xxl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'appointments', 'counts' => ['pending' => 0]])
        </div>

        <div class="col-xl-9 col-xxl-9">
            <div class="row">
                {{-- Main Details --}}
                <div class="col-md-8">
                    <div class="card detail-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-calendar-check me-2"></i>Appointment #{{ $appointment->id }}
                            </h4>
                            <span class="badge badge-{{ $appointment->status }}" style="font-size:14px;">{{ $appointment->getStatusLabel() }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-label">Client</div>
                                    <div class="detail-value">
                                        {{ $appointment->client_name ?? 'N/A' }}
                                        @if($appointment->client_code)
                                            <span class="badge bg-info">{{ $appointment->client_code }}</span>
                                        @endif
                                    </div>

                                    <div class="detail-label">Client Email</div>
                                    <div class="detail-value">{{ $appointment->client_email ?? 'N/A' }}</div>

                                    <div class="detail-label">Client Phone</div>
                                    <div class="detail-value">{{ $appointment->client_phone ?? 'N/A' }}</div>

                                    <div class="detail-label">Service</div>
                                    <div class="detail-value">{{ $appointment->service ? $appointment->service->name : 'N/A' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-label">Date</div>
                                    <div class="detail-value">{{ $appointment->getFormattedDate() }}</div>

                                    <div class="detail-label">Time</div>
                                    <div class="detail-value">{{ $appointment->getFormattedTime() }} ({{ $appointment->duration_hours }} hour{{ $appointment->duration_hours > 1 ? 's' : '' }})</div>

                                    <div class="detail-label">Staff Member</div>
                                    <div class="detail-value">{{ $appointment->staff ? $appointment->staff->name : 'N/A' }}</div>

                                    <div class="detail-label">Created By</div>
                                    <div class="detail-value">
                                        {{ $appointment->creator ? trim($appointment->creator->first_name . ' ' . $appointment->creator->last_name) : 'System' }}
                                        <br><small class="text-muted">{{ $appointment->created_at ? $appointment->created_at->format('d M Y H:i') : '' }}</small>
                                    </div>
                                </div>
                            </div>

                            @if($appointment->notes)
                                <div class="detail-label">Client Notes</div>
                                <div class="detail-value">{{ $appointment->notes }}</div>
                            @endif

                            @if($appointment->internal_notes)
                                <div class="detail-label">Internal Notes</div>
                                <div class="detail-value"><em>{{ $appointment->internal_notes }}</em></div>
                            @endif

                            @if($appointment->cancellation_reason)
                                <div class="detail-label">Cancellation Reason</div>
                                <div class="detail-value text-danger">{{ $appointment->cancellation_reason }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Side Panel --}}
                <div class="col-md-4">
                    {{-- Charging --}}
                    @if($appointment->is_chargeable)
                        <div class="card detail-card mb-3">
                            <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-receipt me-2"></i>Charging</h5></div>
                            <div class="card-body">
                                <div class="detail-label">Amount</div>
                                <div class="detail-value" style="font-size:24px;font-weight:700;color:#28a745;">R {{ number_format($appointment->amount, 2) }}</div>

                                <div class="detail-label">Payment Status</div>
                                <div class="detail-value">
                                    <span class="badge badge-{{ $appointment->payment_status }}">{{ $appointment->getPaymentStatusLabel() }}</span>
                                </div>

                                @if($appointment->invoice_id)
                                    <div class="detail-label">Invoice</div>
                                    <div class="detail-value">#{{ $appointment->invoice_id }}</div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Email Status --}}
                    <div class="card detail-card mb-3">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-envelope me-2"></i>Emails</h5></div>
                        <div class="card-body">
                            <div class="detail-label">Confirmation</div>
                            <div class="detail-value">
                                @if($appointment->confirmation_sent_at)
                                    <span class="text-success"><i class="fas fa-check me-1"></i>Sent {{ $appointment->confirmation_sent_at->format('d M Y H:i') }}</span>
                                @else
                                    <span class="text-muted">Not sent</span>
                                @endif
                            </div>

                            <div class="detail-label">Reminder</div>
                            <div class="detail-value">
                                @if($appointment->reminder_sent_at)
                                    <span class="text-success"><i class="fas fa-check me-1"></i>Sent {{ $appointment->reminder_sent_at->format('d M Y H:i') }}</span>
                                @else
                                    <span class="text-muted">Not sent yet</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="card detail-card">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-bolt me-2"></i>Actions</h5></div>
                        <div class="card-body">
                            <div class="status-actions">
                                <a href="{{ route('cimsappointments.appointments.edit', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>

                                @if($appointment->canConfirm())
                                    <form method="POST" action="{{ route('cimsappointments.appointments.status', $appointment->id) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button class="btn btn-sm btn-info"><i class="fas fa-check me-1"></i>Confirm</button>
                                    </form>
                                @endif

                                @if($appointment->canComplete())
                                    <form method="POST" action="{{ route('cimsappointments.appointments.status', $appointment->id) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button class="btn btn-sm btn-success"><i class="fas fa-check-double me-1"></i>Complete</button>
                                    </form>
                                @endif

                                @if($appointment->canCancel())
                                    <form method="POST" action="{{ route('cimsappointments.appointments.status', $appointment->id) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="no_show">
                                        <button class="btn btn-sm btn-secondary"><i class="fas fa-user-slash me-1"></i>No Show</button>
                                    </form>
                                    <form method="POST" action="{{ route('cimsappointments.appointments.status', $appointment->id) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-times me-1"></i>Cancel</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
