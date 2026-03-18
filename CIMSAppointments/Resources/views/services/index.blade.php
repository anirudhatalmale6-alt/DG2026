@extends('layouts.default')

@section('title', 'Manage Services')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Services</li>
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
            @include('cims_appointments::partials.sidebar', ['activePage' => 'services', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><i class="fas fa-concierge-bell me-2"></i>Services ({{ $stats['total'] }})</h4>
                    <a href="{{ route('cimsappointments.services.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>Add Service</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Duration</th>
                                    <th>Price/Hr</th>
                                    <th>Appointments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $svc)
                                    <tr>
                                        <td>
                                            @if($svc->color)
                                                <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $svc->color }};margin-right:8px;"></span>
                                            @endif
                                            <strong>{{ $svc->name }}</strong>
                                            @if($svc->description)<br><small class="text-muted">{{ Str::limit($svc->description, 60) }}</small>@endif
                                        </td>
                                        <td>{{ $svc->getMinHours() }}-{{ $svc->getMaxHours() }}h</td>
                                        <td>
                                            @if($svc->is_chargeable)
                                                R {{ number_format($svc->price_per_hour, 2) }}
                                            @else
                                                <span class="text-muted">Free</span>
                                            @endif
                                        </td>
                                        <td>{{ $svc->appointments_count }}</td>
                                        <td>
                                            @if($svc->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-warning">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('cimsappointments.services.edit', $svc->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                            @if($svc->is_active)
                                                <form method="POST" action="{{ route('cimsappointments.services.deactivate', $svc->id) }}" class="d-inline">@csrf @method('PUT')
                                                    <button class="btn btn-sm btn-outline-warning" title="Deactivate"><i class="fas fa-pause"></i></button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('cimsappointments.services.activate', $svc->id) }}" class="d-inline">@csrf @method('PUT')
                                                    <button class="btn btn-sm btn-outline-success" title="Activate"><i class="fas fa-play"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No services created yet. Click "Add Service" to get started.</td></tr>
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
