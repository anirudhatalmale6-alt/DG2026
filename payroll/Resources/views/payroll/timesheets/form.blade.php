@extends('layouts.default')

@section('title', ($timesheet ? 'Edit' : 'New') . ' Timesheet')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-clock"></i></div>
            <div>
                <h1>{{ $timesheet ? 'Edit' : 'New' }} Timesheet</h1>
                <p>{{ $timesheet ? 'Update timesheet hours' : 'Record hours for an employee' }}</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.timesheets.index') }}">Timesheets</a>
            <span class="separator">/</span>
            <span class="current">{{ $timesheet ? 'Edit' : 'New' }}</span>
        </div>
        <a href="{{ route('cimspayroll.timesheets.index') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row"><div class="col-12">
        <form method="POST" action="{{ $timesheet ? route('cimspayroll.timesheets.update', $timesheet->id) : route('cimspayroll.timesheets.store') }}">
            @csrf
            @if($timesheet) @method('PUT') @endif

            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-clock"></i> TIMESHEET ENTRY</h4></div>
                <div class="card-body">

                    <div class="form-section-title"><i class="fa fa-user"></i> EMPLOYEE & PERIOD</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $timesheet->employee_id ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }} (#{{ $emp->employee_number }}) — {{ $emp->company->company_name ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Period Start <span class="text-danger">*</span></label>
                                <input type="date" name="period_start" class="form-control" value="{{ old('period_start', $timesheet ? $timesheet->period_start->format('Y-m-d') : date('Y-m-01')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Period End <span class="text-danger">*</span></label>
                                <input type="date" name="period_end" class="form-control" value="{{ old('period_end', $timesheet ? $timesheet->period_end->format('Y-m-d') : date('Y-m-t')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-clock"></i> HOURS WORKED</div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Normal Hours <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="normal_hours" class="form-control" value="{{ old('normal_hours', $timesheet->normal_hours ?? '195.00') }}" required min="0">
                                <small class="text-muted">BCEA: 195 hrs/month</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">OT 1.5x Hours</label>
                                <input type="number" step="0.5" name="overtime_15x_hours" class="form-control" value="{{ old('overtime_15x_hours', $timesheet->overtime_15x_hours ?? '0') }}" min="0">
                                <small class="text-muted">Weekday overtime</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">OT 2x Hours</label>
                                <input type="number" step="0.5" name="overtime_2x_hours" class="form-control" value="{{ old('overtime_2x_hours', $timesheet->overtime_2x_hours ?? '0') }}" min="0">
                                <small class="text-muted">Double time</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Sunday Hours</label>
                                <input type="number" step="0.5" name="sunday_hours" class="form-control" value="{{ old('sunday_hours', $timesheet->sunday_hours ?? '0') }}" min="0">
                                <small class="text-muted">Sunday work</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Public Holiday</label>
                                <input type="number" step="0.5" name="public_holiday_hours" class="form-control" value="{{ old('public_holiday_hours', $timesheet->public_holiday_hours ?? '0') }}" min="0">
                                <small class="text-muted">PH hours</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-calendar-alt"></i> DAYS</div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Days Worked <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="days_worked" class="form-control" value="{{ old('days_worked', $timesheet->days_worked ?? '21.67') }}" required min="0">
                                <small class="text-muted">BCEA: 21.67/month</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Days Absent</label>
                                <input type="number" step="0.5" name="days_absent" class="form-control" value="{{ old('days_absent', $timesheet->days_absent ?? '0') }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Days Leave</label>
                                <input type="number" step="0.5" name="days_leave" class="form-control" value="{{ old('days_leave', $timesheet->days_leave ?? '0') }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-sticky-note"></i> NOTES</div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes">{{ old('notes', $timesheet->notes ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn {{ $timesheet ? 'button_master_update' : 'button_master_save' }}"><i class="fa fa-save"></i> {{ $timesheet ? 'Update Timesheet' : 'Save Timesheet' }}</button>
                        <a href="{{ route('cimspayroll.timesheets.index') }}" class="btn button_master_cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div></div>
</div>
@endsection
