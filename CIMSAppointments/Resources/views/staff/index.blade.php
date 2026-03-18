@extends('layouts.default')

@section('title', 'Manage Staff')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Staff</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'staff', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><i class="fas fa-users me-2"></i>Staff Members ({{ $stats['total'] }})</h4>
                    <a href="{{ route('cimsappointments.staff.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Add Staff</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr><th>Name</th><th>Email</th><th>Position</th><th>Services</th><th>Availability</th><th>Appointments</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $s)
                                    <tr>
                                        <td>
                                            @if($s->color)<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{ $s->color }};margin-right:6px;"></span>@endif
                                            <strong>{{ $s->name }}</strong>
                                        </td>
                                        <td>{{ $s->email ?? '-' }}</td>
                                        <td>{{ $s->position ?? '-' }}</td>
                                        <td>
                                            @foreach($s->services as $svc)
                                                <span class="badge bg-light text-dark" style="font-size:11px;">{{ $svc->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @php $activeDays = $s->availability->where('is_active', true)->pluck('day_of_week')->toArray(); @endphp
                                            @foreach(['M','T','W','T','F','S'] as $i => $day)
                                                <span style="font-size:11px;font-weight:700;color:{{ in_array($i, $activeDays) ? '#28a745' : '#ccc' }};">{{ $day }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $s->appointments_count }}</td>
                                        <td>
                                            @if($s->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-warning">Inactive</span>@endif
                                        </td>
                                        <td>
                                            <a href="{{ route('cimsappointments.staff.show', $s->id) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('cimsappointments.staff.edit', $s->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center py-4 text-muted">No staff members added yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
