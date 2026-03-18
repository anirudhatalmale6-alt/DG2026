@extends('layouts.default')

@section('title', 'Appointments Dashboard')
@section('header_title', 'Appointments')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.appt-stats-row { margin-bottom: 28px; }
.appt-stat-card {
    border-radius: 12px;
    padding: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 130px;
}
.appt-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}
.appt-stat-card.today { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.appt-stat-card.week { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.appt-stat-card.completed { background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%); }
.appt-stat-card.revenue { background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); }
.appt-stat-card.pending { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.appt-stat-card.services { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); }
.appt-stat-card .stat-label { font-size: 13px; font-weight: 500; opacity: 0.9; margin-bottom: 8px; }
.appt-stat-card .stat-number { font-size: 36px; font-weight: 700; margin: 0; line-height: 1.1; }
.appt-stat-card .stat-icon {
    position: absolute; right: 18px; bottom: 20px; font-size: 50px; opacity: 0.3;
}

.appt-table th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #555; font-weight: 700; }
.appt-table td { vertical-align: middle; }
.badge-pending { background: #ffc107; color: #333; }
.badge-confirmed { background: #17a2b8; color: #fff; }
.badge-completed { background: #28a745; color: #fff; }
.badge-cancelled { background: #dc3545; color: #fff; }
.badge-no_show { background: #6c757d; color: #fff; }
.quick-action-btn { padding: 4px 10px; font-size: 12px; border-radius: 6px; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="appt-stats-row">
        <div class="row">
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="appt-stat-card today">
                    <div class="stat-label">Today's Appointments</div>
                    <div class="stat-number">{{ $stats['today_count'] ?? 0 }}</div>
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="appt-stat-card week">
                    <div class="stat-label">This Week</div>
                    <div class="stat-number">{{ $stats['week_count'] ?? 0 }}</div>
                    <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="appt-stat-card completed">
                    <div class="stat-label">Month Completed</div>
                    <div class="stat-number">{{ $stats['month_completed'] ?? 0 }}</div>
                    <div class="stat-icon"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="appt-stat-card revenue">
                    <div class="stat-label">Month Revenue</div>
                    <div class="stat-number">R {{ number_format($stats['month_revenue'] ?? 0, 0) }}</div>
                    <div class="stat-icon"><i class="fas fa-coins"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="appt-stat-card pending">
                    <div class="stat-label">Pending</div>
                    <div class="stat-number">{{ $stats['pending_count'] ?? 0 }}</div>
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="appt-stat-card services">
                    <div class="stat-label">Active Services</div>
                    <div class="stat-number">{{ $stats['total_services'] ?? 0 }}</div>
                    <div class="stat-icon"><i class="fas fa-concierge-bell"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Sidebar --}}
        <div class="col-xl-3 col-xxl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'dashboard', 'counts' => ['pending' => $stats['pending_count'] ?? 0]])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-xxl-9">
            {{-- Today's Appointments --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><i class="fas fa-calendar-day me-2 text-primary"></i>Today's Appointments</h4>
                    <a href="{{ route('cimsappointments.appointments.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Book New
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($todayAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 appt-table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Client</th>
                                        <th>Service</th>
                                        <th>Staff</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAppointments as $appt)
                                        <tr>
                                            <td><strong>{{ date('H:i', strtotime($appt->start_time)) }}</strong></td>
                                            <td>
                                                <div>{{ $appt->client_name ?? 'N/A' }}</div>
                                                @if($appt->client_code)
                                                    <small class="text-muted">{{ $appt->client_code }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $appt->service ? $appt->service->name : 'N/A' }}</td>
                                            <td>{{ $appt->staff ? $appt->staff->name : 'N/A' }}</td>
                                            <td>{{ $appt->duration_hours }}h</td>
                                            <td>
                                                <span class="badge badge-{{ $appt->status }}">{{ $appt->getStatusLabel() }}</span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle quick-action-btn" data-bs-toggle="dropdown">
                                                        Action
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="{{ route('cimsappointments.appointments.show', $appt->id) }}"><i class="fas fa-eye me-2"></i>View</a></li>
                                                        @if($appt->canConfirm())
                                                            <li>
                                                                <form method="POST" action="{{ route('cimsappointments.appointments.status', $appt->id) }}">
                                                                    @csrf @method('PUT')
                                                                    <input type="hidden" name="status" value="confirmed">
                                                                    <button class="dropdown-item text-primary"><i class="fas fa-check me-2"></i>Confirm</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        @if($appt->canComplete())
                                                            <li>
                                                                <form method="POST" action="{{ route('cimsappointments.appointments.status', $appt->id) }}">
                                                                    @csrf @method('PUT')
                                                                    <input type="hidden" name="status" value="completed">
                                                                    <button class="dropdown-item text-success"><i class="fas fa-check-double me-2"></i>Complete</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        @if($appt->canCancel())
                                                            <li>
                                                                <form method="POST" action="{{ route('cimsappointments.appointments.status', $appt->id) }}">
                                                                    @csrf @method('PUT')
                                                                    <input type="hidden" name="status" value="cancelled">
                                                                    <button class="dropdown-item text-danger"><i class="fas fa-times me-2"></i>Cancel</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        @if($appt->canCancel())
                                                            <li>
                                                                <form method="POST" action="{{ route('cimsappointments.appointments.status', $appt->id) }}">
                                                                    @csrf @method('PUT')
                                                                    <input type="hidden" name="status" value="no_show">
                                                                    <button class="dropdown-item text-secondary"><i class="fas fa-user-slash me-2"></i>No Show</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No appointments scheduled for today.</p>
                            <a href="{{ route('cimsappointments.appointments.create') }}" class="btn btn-sm btn-primary">Book an Appointment</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Upcoming This Week --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-calendar-week me-2 text-success"></i>Upcoming This Week</h4>
                </div>
                <div class="card-body p-0">
                    @if($weekAppointments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 appt-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Client</th>
                                        <th>Service</th>
                                        <th>Staff</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($weekAppointments as $appt)
                                        <tr>
                                            <td>{{ $appt->getFormattedDate() }}</td>
                                            <td>{{ date('H:i', strtotime($appt->start_time)) }}</td>
                                            <td>
                                                <a href="{{ route('cimsappointments.appointments.show', $appt->id) }}">
                                                    {{ $appt->client_name ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>{{ $appt->service ? $appt->service->name : 'N/A' }}</td>
                                            <td>{{ $appt->staff ? $appt->staff->name : 'N/A' }}</td>
                                            <td><span class="badge badge-{{ $appt->status }}">{{ $appt->getStatusLabel() }}</span></td>
                                            <td>
                                                @if($appt->is_chargeable && $appt->amount > 0)
                                                    R {{ number_format($appt->amount, 2) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">No upcoming appointments this week.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
