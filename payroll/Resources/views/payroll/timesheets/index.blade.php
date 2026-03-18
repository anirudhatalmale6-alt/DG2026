@extends('layouts.default')

@section('title', 'Timesheets')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-clock"></i></div>
            <div><h1>Timesheets</h1><p>Record employee hours worked per pay period</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Timesheets</span>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('cimspayroll.timesheets.create') }}" class="btn button_master_add"><i class="fa fa-plus"></i> New Timesheet</a>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Filters -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
            <div class="card-body">
                <form method="GET" action="{{ route('cimspayroll.timesheets.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="company_id" class="form-control">
                                <option value="">-- All Companies --</option>
                                @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="month" name="period_month" class="form-control" value="{{ request('period_month') }}" placeholder="Period">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">-- All Status --</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div></div>

    <!-- Timesheets Table -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-list"></i> TIMESHEETS</h4></div>
            <div class="card-body" style="padding:0;">
                @if($timesheets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Employee</th>
                                <th>Company</th>
                                <th>Period</th>
                                <th style="text-align:right;">Normal</th>
                                <th style="text-align:right;">OT 1.5x</th>
                                <th style="text-align:right;">OT 2x</th>
                                <th style="text-align:right;">Sun</th>
                                <th style="text-align:right;">PH</th>
                                <th style="text-align:right;">Days W</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timesheets as $ts)
                            <tr>
                                <td><strong>{{ $ts->employee->first_name ?? '' }} {{ $ts->employee->last_name ?? '' }}</strong><br><small class="text-muted">#{{ $ts->employee->employee_number ?? '' }}</small></td>
                                <td>{{ $ts->employee->company->company_name ?? '—' }}</td>
                                <td>{{ $ts->period_start->format('d M') }} — {{ $ts->period_end->format('d M Y') }}</td>
                                <td style="text-align:right;">{{ number_format($ts->normal_hours, 1) }}</td>
                                <td style="text-align:right;">{{ $ts->overtime_15x_hours > 0 ? number_format($ts->overtime_15x_hours, 1) : '—' }}</td>
                                <td style="text-align:right;">{{ $ts->overtime_2x_hours > 0 ? number_format($ts->overtime_2x_hours, 1) : '—' }}</td>
                                <td style="text-align:right;">{{ $ts->sunday_hours > 0 ? number_format($ts->sunday_hours, 1) : '—' }}</td>
                                <td style="text-align:right;">{{ $ts->public_holiday_hours > 0 ? number_format($ts->public_holiday_hours, 1) : '—' }}</td>
                                <td style="text-align:right;">{{ number_format($ts->days_worked, 1) }}</td>
                                <td>
                                    @php $sc = ['draft' => 'secondary', 'submitted' => 'warning', 'approved' => 'success']; @endphp
                                    <span class="badge bg-{{ $sc[$ts->status] ?? 'secondary' }}">{{ ucfirst($ts->status) }}</span>
                                </td>
                                <td>
                                    @if($ts->status !== 'approved')
                                    <a href="{{ route('cimspayroll.timesheets.edit', $ts->id) }}" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;"><i class="fa fa-edit"></i></a>
                                    <form method="POST" action="{{ route('cimspayroll.timesheets.approve', $ts->id) }}" style="display:inline;" onsubmit="return confirm('Approve this timesheet?');">
                                        @csrf
                                        <button type="submit" class="btn button_master_save" style="padding:4px 12px;font-size:12px;"><i class="fa fa-check"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('cimspayroll.timesheets.destroy', $ts->id) }}" style="display:inline;" onsubmit="return confirm('Delete?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn button_master_delete" style="padding:4px 12px;font-size:12px;"><i class="fa fa-trash"></i></button>
                                    </form>
                                    @else
                                    <span class="text-muted" style="font-size:12px;">Locked</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $timesheets->withQueryString()->links() }}</div>
                @else
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-clock" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                    <p>No timesheets found. Click <strong>New Timesheet</strong> to create one.</p>
                </div>
                @endif
            </div>
        </div>
    </div></div>
</div>
@endsection
