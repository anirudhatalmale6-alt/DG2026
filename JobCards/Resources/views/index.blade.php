@extends('layouts.default')

@section('content')
<style>
    .jc-list { font-family: 'Poppins', sans-serif; }
    .jc-list .page-header { margin-bottom: 24px; }
    .jc-list .filter-bar {
        background: #fff; border-radius: 12px; padding: 16px 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #eef2f7; margin-bottom: 20px;
    }
    .jc-list .filter-bar select, .jc-list .filter-bar input {
        border-radius: 8px; border: 1px solid #dde2e8; font-size: 13px; padding: 8px 12px;
        font-family: 'Poppins', sans-serif;
    }
    .jc-list .table-card {
        background: #fff; border-radius: 12px; overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #eef2f7;
    }
    .jc-list .table { margin: 0; }
    .jc-list .table thead th {
        background: #1a1a2e; color: #fff; font-size: 12px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border: none;
    }
    .jc-list .table tbody td {
        padding: 14px 16px; font-size: 13px; color: #333; vertical-align: middle;
        border-bottom: 1px solid #f5f5f5;
    }
    .jc-list .table tbody tr:hover { background: #f8f9ff; }
    .jc-list .status-badge {
        display: inline-block; padding: 4px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .jc-list .priority-dot {
        width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px;
    }
    .jc-list .progress-bar-mini {
        width: 80px; height: 6px; background: #eef2f7; border-radius: 3px; overflow: hidden; display: inline-block;
    }
    .jc-list .progress-bar-mini .fill { height: 100%; border-radius: 3px; }
    .jc-list .btn-action {
        padding: 4px 10px; border-radius: 6px; font-size: 12px; border: none;
        cursor: pointer; transition: all 0.2s;
    }
</style>

<div class="jc-list">
    <div class="d-flex justify-content-between align-items-center page-header">
        <div>
            <h3 style="font-weight:700;color:#1a1a2e;margin:0;">Job Cards</h3>
            <p style="color:#7f8c8d;margin:4px 0 0;font-size:13px;">{{ $total }} job card(s) found</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('jobcards.dashboard') }}" class="btn btn-sm" style="background:#1a1a2e;color:#fff;border-radius:8px;font-size:13px;padding:8px 16px;">
                <i class="fa fa-tachometer mr-1"></i> Dashboard
            </a>
            <a href="{{ route('jobcards.create') }}" class="btn btn-sm" style="background:#E91E8C;color:#fff;border-radius:8px;font-size:13px;padding:8px 16px;">
                <i class="fa fa-plus mr-1"></i> New Job Card
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('jobcards.index') }}" class="d-flex gap-3 align-items-end flex-wrap">
            <div>
                <label style="font-size:11px;font-weight:600;color:#7f8c8d;text-transform:uppercase;">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Job code, client..." class="form-control form-control-sm">
            </div>
            <div>
                <label style="font-size:11px;font-weight:600;color:#7f8c8d;text-transform:uppercase;">Status</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $key => $s)
                        <option value="{{ $key }}" {{ ($filters['status'] ?? '') === $key ? 'selected' : '' }}>{{ $s['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:11px;font-weight:600;color:#7f8c8d;text-transform:uppercase;">Job Type</label>
                <select name="job_type_id" class="form-control form-control-sm">
                    <option value="">All Types</option>
                    @foreach($jobTypes as $jt)
                        <option value="{{ $jt->id }}" {{ ($filters['job_type_id'] ?? '') == $jt->id ? 'selected' : '' }}>{{ $jt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:11px;font-weight:600;color:#7f8c8d;text-transform:uppercase;">Assigned To</label>
                <select name="assigned_to" class="form-control form-control-sm">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ ($filters['assigned_to'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-sm" style="background:#1a1a2e;color:#fff;border-radius:8px;padding:8px 16px;font-size:13px;">
                    <i class="fa fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('jobcards.index') }}" class="btn btn-sm" style="background:#eef2f7;color:#666;border-radius:8px;padding:8px 16px;font-size:13px;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Job Code</th>
                    <th>Client</th>
                    <th>Job Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Assigned To</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobCards as $jc)
                @php
                    $sc = $statuses[$jc->status] ?? ['color' => '#6c757d', 'label' => $jc->status, 'icon' => 'fa-file'];
                    $pc = config("job_cards.priorities.{$jc->priority}", ['color' => '#007bff', 'label' => $jc->priority]);
                    $isOverdue = $jc->due_date && $jc->due_date < now()->format('Y-m-d') && !in_array($jc->status, ['completed','submitted','cancelled']);
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('jobcards.show', $jc->id) }}" style="font-weight:600;color:#E91E8C;text-decoration:none;">{{ $jc->job_code }}</a>
                    </td>
                    <td>
                        <strong>{{ $jc->company_name }}</strong>
                        <br><span style="font-size:11px;color:#aaa;">{{ $jc->client_code }}</span>
                    </td>
                    <td>{{ $jc->job_type_name }}</td>
                    <td>
                        <span class="priority-dot" style="background:{{ $pc['color'] }};"></span>
                        {{ $pc['label'] }}
                    </td>
                    <td>
                        <span class="status-badge" style="background:{{ $sc['color'] }}22;color:{{ $sc['color'] }};">
                            <i class="fa {{ $sc['icon'] }} mr-1"></i>{{ $sc['label'] }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress-bar-mini">
                                <div class="fill" style="width:{{ $jc->completion_percentage }}%;background:{{ $jc->completion_percentage >= 100 ? '#28a745' : '#E91E8C' }};"></div>
                            </div>
                            <span style="font-size:11px;color:#7f8c8d;">{{ number_format($jc->completion_percentage, 0) }}%</span>
                        </div>
                    </td>
                    <td style="font-size:12px;">{{ $jc->assigned_first_name }} {{ $jc->assigned_last_name }}</td>
                    <td style="font-size:12px;{{ $isOverdue ? 'color:#dc3545;font-weight:600;' : '' }}">
                        {{ $jc->due_date ? \Carbon\Carbon::parse($jc->due_date)->format('d/m/Y') : '-' }}
                        @if($isOverdue) <i class="fa fa-exclamation-circle ml-1"></i> @endif
                    </td>
                    <td>
                        <a href="{{ route('jobcards.show', $jc->id) }}" class="btn-action" style="background:#eef2f7;color:#1a1a2e;" title="View">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#aaa;">
                        <i class="fa fa-briefcase" style="font-size:40px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                        No job cards found. <a href="{{ route('jobcards.create') }}" style="color:#E91E8C;">Create one now</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
