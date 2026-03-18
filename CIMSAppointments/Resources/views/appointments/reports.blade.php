@extends('layouts.default')

@section('title', 'Appointment Reports')

@push('styles')
<style>
.report-card { border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
.report-stat { text-align: center; padding: 20px; }
.report-stat .number { font-size: 28px; font-weight: 700; }
.report-stat .label { font-size: 12px; text-transform: uppercase; color: #888; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'reports', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            {{-- Filter --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:11px;font-weight:700;">FROM</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:11px;font-weight:700;">TO</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:11px;font-weight:700;">STAFF</label>
                            <select name="staff_id" class="form-select form-select-sm">
                                <option value="">All Staff</option>
                                @foreach($staffList as $s)
                                    <option value="{{ $s->id }}" {{ $staffId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-chart-bar me-1"></i>Generate Report</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Summary --}}
            <div class="row mb-3">
                <div class="col-md-2"><div class="card report-card"><div class="report-stat"><div class="number text-primary">{{ $summary['total'] }}</div><div class="label">Total</div></div></div></div>
                <div class="col-md-2"><div class="card report-card"><div class="report-stat"><div class="number text-success">{{ $summary['completed'] }}</div><div class="label">Completed</div></div></div></div>
                <div class="col-md-2"><div class="card report-card"><div class="report-stat"><div class="number text-danger">{{ $summary['cancelled'] }}</div><div class="label">Cancelled</div></div></div></div>
                <div class="col-md-2"><div class="card report-card"><div class="report-stat"><div class="number text-secondary">{{ $summary['no_show'] }}</div><div class="label">No Show</div></div></div></div>
                <div class="col-md-2"><div class="card report-card"><div class="report-stat"><div class="number text-info">{{ $summary['total_hours'] }}h</div><div class="label">Hours</div></div></div></div>
                <div class="col-md-2"><div class="card report-card"><div class="report-stat"><div class="number text-success">R {{ number_format($summary['total_revenue'], 0) }}</div><div class="label">Revenue</div></div></div></div>
            </div>

            <div class="row">
                {{-- By Staff --}}
                <div class="col-md-6 mb-3">
                    <div class="card report-card">
                        <div class="card-header"><h5 class="card-title mb-0">By Staff Member</h5></div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead><tr><th>Staff</th><th>Total</th><th>Done</th><th>Revenue</th></tr></thead>
                                <tbody>
                                    @forelse($byStaff as $row)
                                        <tr>
                                            <td>{{ $row['staff_name'] }}</td>
                                            <td>{{ $row['total'] }}</td>
                                            <td>{{ $row['completed'] }}</td>
                                            <td>R {{ number_format($row['revenue'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- By Service --}}
                <div class="col-md-6 mb-3">
                    <div class="card report-card">
                        <div class="card-header"><h5 class="card-title mb-0">By Service</h5></div>
                        <div class="card-body p-0">
                            <table class="table mb-0">
                                <thead><tr><th>Service</th><th>Total</th><th>Done</th><th>Revenue</th></tr></thead>
                                <tbody>
                                    @forelse($byService as $row)
                                        <tr>
                                            <td>{{ $row['service_name'] }}</td>
                                            <td>{{ $row['total'] }}</td>
                                            <td>{{ $row['completed'] }}</td>
                                            <td>R {{ number_format($row['revenue'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- By Day --}}
            <div class="card report-card">
                <div class="card-header"><h5 class="card-title mb-0">Busiest Days</h5></div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead><tr><th>Day</th><th>Appointments</th><th>Bar</th></tr></thead>
                        <tbody>
                            @php $maxDay = $byDayOfWeek->max('count') ?: 1; @endphp
                            @forelse($byDayOfWeek as $row)
                                <tr>
                                    <td>{{ $row['day_name'] }}</td>
                                    <td>{{ $row['count'] }}</td>
                                    <td style="width:50%;">
                                        <div style="height:20px;background:linear-gradient(90deg,#17A2B8,#28a745);border-radius:4px;width:{{ ($row['count']/$maxDay)*100 }}%;"></div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
