@extends('layouts.default')

@section('title', 'Staff: ' . $staff->name)

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.staff.index') }}">Staff</a></li>
            <li class="breadcrumb-item active">{{ $staff->name }}</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'staff', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <div class="row">
                {{-- Staff Info --}}
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>{{ $staff->name }}</h5></div>
                        <div class="card-body">
                            <p><strong>Position:</strong> {{ $staff->position ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $staff->email ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $staff->phone ?? 'N/A' }}</p>
                            <p><strong>Status:</strong> @if($staff->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-warning">Inactive</span>@endif</p>
                            <p><strong>Services:</strong></p>
                            @foreach($staff->services as $svc)
                                <span class="badge bg-info mb-1">{{ $svc->name }}</span>
                            @endforeach
                            <div class="mt-3">
                                <a href="{{ route('cimsappointments.staff.edit', $staff->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit me-1"></i>Edit</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Availability --}}
                <div class="col-md-8 mb-3">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-clock me-2"></i>Weekly Availability</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('cimsappointments.staff.availability.update', $staff->id) }}">
                                @csrf @method('PUT')
                                <table class="table table-sm">
                                    <thead><tr><th>Day</th><th>Active</th><th>Start</th><th>End</th><th>Slots</th></tr></thead>
                                    <tbody>
                                        @php $dayNames = \Modules\CIMSAppointments\Models\StaffAvailability::DAYS; @endphp
                                        @for($d = 0; $d <= 5; $d++)
                                            @php $avail = $staff->availability->where('day_of_week', $d)->first(); @endphp
                                            <tr>
                                                <td><strong>{{ $dayNames[$d] }}</strong></td>
                                                <td>
                                                    <input type="hidden" name="availability[{{ $d }}][day_of_week]" value="{{ $d }}">
                                                    <input type="checkbox" name="availability[{{ $d }}][is_active]" value="1" {{ ($avail && $avail->is_active) ? 'checked' : '' }}>
                                                </td>
                                                <td><input type="time" name="availability[{{ $d }}][start_time]" class="form-control form-control-sm" value="{{ $avail ? substr($avail->start_time, 0, 5) : '08:00' }}" style="width:120px;"></td>
                                                <td><input type="time" name="availability[{{ $d }}][end_time]" class="form-control form-control-sm" value="{{ $avail ? substr($avail->end_time, 0, 5) : '17:00' }}" style="width:120px;"></td>
                                                <td>
                                                    @if($avail && $avail->is_active)
                                                        @php $slots = $avail->getTimeSlots(); @endphp
                                                        {{ count($slots) }} slots ({{ $slots[0] ?? '' }} - {{ substr($avail->end_time, 0, 5) }})
                                                    @else
                                                        <span class="text-muted">Off</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save me-1"></i>Save Availability</button>
                            </form>
                        </div>
                    </div>

                    {{-- Blocked Dates --}}
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-ban me-2"></i>Blocked Dates</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('cimsappointments.staff.blocked-dates.store', $staff->id) }}" class="row g-2 mb-3">
                                @csrf
                                <div class="col-md-4">
                                    <input type="date" name="blocked_date" class="form-control form-control-sm" min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Reason (e.g. Annual Leave)">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-sm btn-warning w-100"><i class="fas fa-plus me-1"></i>Block Date</button>
                                </div>
                            </form>
                            @if($staff->blockedDates->count() > 0)
                                <table class="table table-sm">
                                    <thead><tr><th>Date</th><th>Reason</th><th></th></tr></thead>
                                    <tbody>
                                        @foreach($staff->blockedDates->sortBy('blocked_date') as $bd)
                                            <tr>
                                                <td>{{ $bd->blocked_date->format('d M Y') }}</td>
                                                <td>{{ $bd->reason ?? '-' }}</td>
                                                <td>
                                                    <form method="POST" action="{{ route('cimsappointments.staff.blocked-dates.destroy', [$staff->id, $bd->id]) }}" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted mb-0">No blocked dates.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Recent Appointments --}}
                    <div class="card mt-3">
                        <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Recent Appointments</h5></div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Date</th><th>Time</th><th>Client</th><th>Service</th><th>Status</th></tr></thead>
                                <tbody>
                                    @forelse($staff->appointments as $appt)
                                        <tr>
                                            <td>{{ $appt->getFormattedDate() }}</td>
                                            <td>{{ date('H:i', strtotime($appt->start_time)) }}</td>
                                            <td><a href="{{ route('cimsappointments.appointments.show', $appt->id) }}">{{ $appt->client_name ?? 'N/A' }}</a></td>
                                            <td>{{ $appt->service ? $appt->service->name : 'N/A' }}</td>
                                            <td><span class="badge badge-{{ $appt->status }}">{{ $appt->getStatusLabel() }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-3">No appointments yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
