@extends('layouts.default')

@section('title', 'All Appointments')

@push('styles')
<style>
.badge-pending { background: #ffc107; color: #333; }
.badge-confirmed { background: #17a2b8; color: #fff; }
.badge-completed { background: #28a745; color: #fff; }
.badge-cancelled { background: #dc3545; color: #fff; }
.badge-no_show { background: #6c757d; color: #fff; }
.badge-unpaid { background: #dc3545; color: #fff; }
.badge-paid { background: #28a745; color: #fff; }
.badge-waived { background: #6c757d; color: #fff; }
.badge-invoiced { background: #17a2b8; color: #fff; }
.filter-bar { background: #f8f9fa; border-radius: 10px; padding: 15px 20px; margin-bottom: 20px; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">All Appointments</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-xl-3 col-xxl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'appointments', 'counts' => ['pending' => 0]])
        </div>

        <div class="col-xl-9 col-xxl-9">
            {{-- Filter Bar --}}
            <div class="filter-bar">
                <form method="GET" action="{{ route('cimsappointments.appointments.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach(\Modules\CIMSAppointments\Models\Appointment::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;">Staff</label>
                        <select name="staff_id" class="form-select form-select-sm">
                            <option value="">All Staff</option>
                            @foreach($staffList as $s)
                                <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;">From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;">To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" style="font-size:11px;font-weight:700;text-transform:uppercase;">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Client name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                    </div>
                </form>
            </div>

            {{-- Appointments Table --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Appointments</h4>
                    <span class="badge bg-primary">{{ $appointments->total() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Client</th>
                                    <th>Service</th>
                                    <th>Staff</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appt)
                                    <tr>
                                        <td>{{ $appt->getFormattedDate() }}</td>
                                        <td><strong>{{ date('H:i', strtotime($appt->start_time)) }}</strong></td>
                                        <td>
                                            <a href="{{ route('cimsappointments.appointments.show', $appt->id) }}">{{ $appt->client_name ?? 'N/A' }}</a>
                                            @if($appt->client_code)<br><small class="text-muted">{{ $appt->client_code }}</small>@endif
                                        </td>
                                        <td>{{ $appt->service ? $appt->service->name : 'N/A' }}</td>
                                        <td>{{ $appt->staff ? $appt->staff->name : 'N/A' }}</td>
                                        <td>{{ $appt->duration_hours }}h</td>
                                        <td><span class="badge badge-{{ $appt->status }}">{{ $appt->getStatusLabel() }}</span></td>
                                        <td>
                                            @if($appt->is_chargeable && $appt->amount > 0)
                                                R {{ number_format($appt->amount, 2) }}
                                                <br><span class="badge badge-{{ $appt->payment_status }}" style="font-size:10px;">{{ $appt->getPaymentStatusLabel() }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('cimsappointments.appointments.show', $appt->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('cimsappointments.appointments.edit', $appt->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-calendar-times fa-2x text-muted mb-2 d-block"></i>
                                            No appointments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($appointments->hasPages())
                    <div class="card-footer">
                        {{ $appointments->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
