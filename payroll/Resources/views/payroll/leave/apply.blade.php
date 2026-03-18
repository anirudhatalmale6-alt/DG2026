@extends('layouts.default')

@section('title', 'Apply for Leave')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-calendar-plus"></i></div>
            <div><h1>Apply for Leave</h1><p>Submit a new leave application</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.leave.applications') }}">Leave Applications</a>
            <span class="separator">/</span>
            <span class="current">Apply</span>
        </div>
        <a href="{{ route('cimspayroll.leave.applications') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
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
        <form method="POST" action="{{ route('cimspayroll.leave.applications.store') }}">
            @csrf
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-calendar-plus"></i> LEAVE APPLICATION</h4></div>
                <div class="card-body">

                    <div class="form-section-title"><i class="fa fa-user"></i> EMPLOYEE & LEAVE TYPE</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->first_name }} {{ $emp->last_name }} (#{{ $emp->employee_number }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                                <select name="leave_type_id" class="form-control" required>
                                    <option value="">-- Select Leave Type --</option>
                                    @foreach($leaveTypes as $lt)
                                    <option value="{{ $lt->id }}" {{ old('leave_type_id') == $lt->id ? 'selected' : '' }}>{{ $lt->name }} ({{ $lt->days_per_year }} days/yr{{ $lt->is_paid ? '' : ' — Unpaid' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-calendar"></i> DATES</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}" required onchange="calcDays()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}" required onchange="calcDays()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Working Days Requested <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="days_requested" id="days_requested" class="form-control" value="{{ old('days_requested') }}" required min="0.5">
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-comment"></i> REASON</div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Reason / Notes</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Optional — reason for leave">{{ old('reason') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn button_master_save"><i class="fa fa-paper-plane"></i> Submit Application</button>
                        <a href="{{ route('cimspayroll.leave.applications') }}" class="btn button_master_cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
function calcDays() {
    var start = document.getElementById('start_date').value;
    var end = document.getElementById('end_date').value;
    if (start && end) {
        var s = new Date(start);
        var e = new Date(end);
        if (e >= s) {
            var days = 0;
            var d = new Date(s);
            while (d <= e) {
                var dow = d.getDay();
                if (dow !== 0 && dow !== 6) days++; // Exclude weekends
                d.setDate(d.getDate() + 1);
            }
            document.getElementById('days_requested').value = days;
        }
    }
}
</script>
@endpush
