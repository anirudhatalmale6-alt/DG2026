@extends('layouts.default')

@section('title', 'New Pay Run')

@push('styles')
<style>
.payroll-wrapper { max-width: none; margin: 0; padding: 30px; }

/* SmartDash Icon & Badge header style */
.sd-card-header-badge { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; background: #fff; border-bottom: 1px solid #eee; border-radius: 10px 10px 0 0; }
.sd-card-header-badge .hdr-left { display: flex; align-items: center; gap: 16px; }
.sd-card-header-badge .hdr-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #004D40, #00796B); border-radius: 12px; display: flex; align-items: center; justify-content: center; }
.sd-card-header-badge .hdr-icon i { font-size: 20px; color: #fff; }
.sd-card-header-badge .hdr-text h4 { margin: 0; font-size: 16px; font-weight: 700; color: #333; }
.sd-card-header-badge .hdr-text p { margin: 0; font-size: 12px; color: #888; }
.sd-card-header-badge .hdr-badge { font-size: 18px; font-weight: 800; color: #004D40; letter-spacing: 1px; text-transform: uppercase; }

/* Premium card */
.pr-card { border: none; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); margin-bottom: 20px; }
.pr-card .card-body { padding: 24px; }

/* Employee table */
.emp-table { width: 100%; border-collapse: collapse; }
.emp-table thead th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; color: #5b676d; padding: 10px 12px; background: #f8f9fa; border-bottom: 2px solid #dee2e6; }
.emp-table tbody td { padding: 10px 12px; font-size: 14px; border-bottom: 1px solid #eee; vertical-align: middle; }
.emp-table tbody tr:hover { background: #f0faff; }
.emp-table .cims_money_format { text-align: right; padding-right: 3px; font-variant-numeric: tabular-nums; }

/* Employee count badge */
.emp-count-badge { display: inline-flex; align-items: center; justify-content: center; background: #00796B; color: #fff; font-size: 13px; font-weight: 700; width: 28px; height: 28px; border-radius: 50%; margin-left: 8px; }

/* Select all checkbox */
.check-all-row { background: #E0F2F1; }
.check-all-row td { font-weight: 700; color: #004D40; }

/* Loading */
.emp-loading { text-align: center; padding: 40px; color: #888; }
.emp-loading i { font-size: 32px; color: #00796B; }

/* Empty state */
.emp-empty { text-align: center; padding: 40px; color: #999; }
.emp-empty i { font-size: 48px; margin-bottom: 12px; display: block; }

/* Period pills */
.period-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
.period-pill { display: flex; align-items: center; justify-content: center; padding: 10px 8px; border: 2px solid #dee2e6; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #555; transition: all 0.2s; text-align: center; }
.period-pill:hover { border-color: #00796B; background: #f0faff; color: #004D40; }
.period-pill.active { border-color: #004D40; background: #004D40; color: #fff; }
.period-pill.disabled { opacity: 0.4; cursor: not-allowed; pointer-events: none; }
.period-pill .pill-month { display: block; font-size: 14px; font-weight: 700; }
.period-pill .pill-year { display: block; font-size: 11px; opacity: 0.7; }
</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-play-circle"></i></div>
            <div><h1>New Pay Run</h1><p>Create a monthly pay run for processing</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.pay-runs.index') }}">Pay Runs</a>
            <span class="separator">/</span>
            <span class="current">New</span>
        </div>
        <a href="{{ route('cimspayroll.pay-runs.index') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
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

    <form method="POST" action="{{ route('cimspayroll.pay-runs.store') }}" id="payRunForm">
        @csrf
        <input type="hidden" name="pay_period" id="hid_pay_period" value="">
        <input type="hidden" name="period_start" id="hid_period_start" value="">
        <input type="hidden" name="period_end" id="hid_period_end" value="">

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- SECTION 1: PAY RUN SETUP                       --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="card pr-card">
            <div class="sd-card-header-badge">
                <div class="hdr-left">
                    <div class="hdr-icon"><i class="fas fa-play-circle"></i></div>
                    <div class="hdr-text">
                        <h4>Pay Run Setup</h4>
                        <p>Select company and pay period to begin</p>
                    </div>
                </div>
                <div class="hdr-badge">NEW PAY RUN</div>
            </div>
            <div class="card-body">
                {{-- Company Selection --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;color:#5b676d;">Company <span class="text-danger">*</span></label>
                            <select name="company_id" id="selCompany" class="sd_drop_class" required onchange="onCompanyChange()">
                                <option value="">-- Select Company --</option>
                                @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;color:#5b676d;">Description</label>
                            <input type="text" name="description" class="form-control" placeholder="Optional description for this pay run" value="{{ old('description') }}" style="height:42px;">
                        </div>
                    </div>
                </div>

                {{-- Pay Period - Financial Year Grid --}}
                <div style="margin-top:8px;">
                    <label class="form-label" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;color:#5b676d;">Pay Period — Tax Year {{ cims_tax_year() }} <span style="font-weight:400;text-transform:none;color:#888;">(1 March {{ cims_tax_year() - 1 }} to 28 February {{ cims_tax_year() }})</span></label>
                    <div class="period-grid" id="periodGrid">
                        @php
                            $taxYear = cims_tax_year();
                            $startYear = $taxYear - 1;
                            $months = [
                                ['month' => 3, 'year' => $startYear, 'label' => 'March'],
                                ['month' => 4, 'year' => $startYear, 'label' => 'April'],
                                ['month' => 5, 'year' => $startYear, 'label' => 'May'],
                                ['month' => 6, 'year' => $startYear, 'label' => 'June'],
                                ['month' => 7, 'year' => $startYear, 'label' => 'July'],
                                ['month' => 8, 'year' => $startYear, 'label' => 'August'],
                                ['month' => 9, 'year' => $startYear, 'label' => 'September'],
                                ['month' => 10, 'year' => $startYear, 'label' => 'October'],
                                ['month' => 11, 'year' => $startYear, 'label' => 'November'],
                                ['month' => 12, 'year' => $startYear, 'label' => 'December'],
                                ['month' => 1, 'year' => $taxYear, 'label' => 'January'],
                                ['month' => 2, 'year' => $taxYear, 'label' => 'February'],
                            ];
                            $currentMonth = (int) date('n');
                            $currentYear = (int) date('Y');
                        @endphp
                        @foreach($months as $m)
                        <div class="period-pill" onclick="selectPeriod(this, {{ $m['month'] }}, {{ $m['year'] }}, '{{ $m['label'] }}')" data-month="{{ $m['month'] }}" data-year="{{ $m['year'] }}">
                            <div>
                                <span class="pill-month">{{ $m['label'] }}</span>
                                <span class="pill-year">{{ $m['year'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- SECTION 2: EMPLOYEES                           --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="card pr-card" id="empCard" style="display:none;">
            <div class="sd-card-header-badge">
                <div class="hdr-left">
                    <div class="hdr-icon"><i class="fas fa-users"></i></div>
                    <div class="hdr-text">
                        <h4>Employees <span class="emp-count-badge" id="empCountBadge">0</span></h4>
                        <p>Active employees for the selected company</p>
                    </div>
                </div>
                <div class="hdr-badge" id="empCompanyBadge">EMPLOYEES</div>
            </div>

            {{-- Loading --}}
            <div class="emp-loading" id="empLoading" style="display:none;">
                <i class="fas fa-spinner fa-spin"></i>
                <p style="margin-top:10px;">Loading employees...</p>
            </div>

            {{-- Employee Table --}}
            <div class="card-body" style="padding:0;" id="empTableWrap" style="display:none;">
                <div class="table-responsive">
                    <table class="emp-table" id="empTable">
                        <thead>
                            <tr>
                                <th style="width:40px;text-align:center;"><input type="checkbox" id="chkAll" onchange="toggleAll(this)" checked></th>
                                <th>Employee No</th>
                                <th>Name</th>
                                <th>Pay Type</th>
                                <th class="cims_money_format">Basic Salary / Rate</th>
                                <th style="text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="empBody"></tbody>
                    </table>
                </div>

                {{-- Action Buttons --}}
                <div style="padding:20px 24px;border-top:2px solid #eee;display:flex;gap:12px;align-items:center;">
                    <button type="submit" class="btn button_master_save" id="btnCreateRun" disabled><i class="fa fa-play-circle"></i> Create Pay Run</button>
                    <a href="{{ route('cimspayroll.pay-runs.index') }}" class="btn button_master_cancel"><i class="fa fa-times"></i> Cancel</a>
                    <span id="selectedCount" style="margin-left:auto;font-size:13px;color:#5b676d;"></span>
                </div>
            </div>

            {{-- Empty State --}}
            <div class="emp-empty" id="empEmpty" style="display:none;">
                <i class="fas fa-user-slash"></i>
                <p>No active employees found for this company.</p>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
var selectedPeriod = null;

function selectPeriod(el, month, year, label) {
    // Remove active from all pills
    document.querySelectorAll('.period-pill').forEach(function(p) { p.classList.remove('active'); });
    el.classList.add('active');

    // Set hidden fields
    var monthStr = String(month).padStart(2, '0');
    var payPeriod = year + '-' + monthStr;
    var firstDay = year + '-' + monthStr + '-01';
    var lastDay = new Date(year, month, 0).getDate();
    var lastDate = year + '-' + monthStr + '-' + String(lastDay).padStart(2, '0');

    document.getElementById('hid_pay_period').value = payPeriod;
    document.getElementById('hid_period_start').value = firstDay;
    document.getElementById('hid_period_end').value = lastDate;
    selectedPeriod = { month: month, year: year, label: label };

    checkReady();
}

function onCompanyChange() {
    var companyId = document.getElementById('selCompany').value;
    var empCard = document.getElementById('empCard');

    if (!companyId) {
        empCard.style.display = 'none';
        return;
    }

    // Show card with loading
    empCard.style.display = 'block';
    document.getElementById('empLoading').style.display = 'block';
    document.getElementById('empTableWrap').style.display = 'none';
    document.getElementById('empEmpty').style.display = 'none';

    // Get company name for badge
    var sel = document.getElementById('selCompany');
    var companyName = sel.options[sel.selectedIndex].text;
    document.getElementById('empCompanyBadge').textContent = companyName.toUpperCase();

    // Fetch employees via AJAX
    var url = '{{ route("cimspayroll.pay-runs.employees") }}?company_id=' + companyId;
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('empLoading').style.display = 'none';

        if (data.employees && data.employees.length > 0) {
            renderEmployees(data.employees);
            document.getElementById('empTableWrap').style.display = 'block';
        } else {
            document.getElementById('empEmpty').style.display = 'block';
        }
        checkReady();
    })
    .catch(function() {
        document.getElementById('empLoading').style.display = 'none';
        document.getElementById('empEmpty').style.display = 'block';
        document.getElementById('empEmpty').querySelector('p').textContent = 'Error loading employees. Please try again.';
    });
}

function renderEmployees(employees) {
    var tbody = document.getElementById('empBody');
    var html = '';

    for (var i = 0; i < employees.length; i++) {
        var e = employees[i];
        var payLabel = e.pay_type === 'salaried' ? 'Salaried' : 'Hourly';
        var salaryDisplay = e.pay_type === 'salaried'
            ? ('R ' + formatMoney(e.basic_salary))
            : ('R ' + formatMoney(e.hourly_rate) + '/hr');

        html += '<tr>';
        html += '<td style="text-align:center;"><input type="checkbox" name="employee_ids[]" value="' + e.id + '" class="emp-check" onchange="updateCount()" checked></td>';
        html += '<td><strong>' + escHtml(e.employee_number) + '</strong></td>';
        html += '<td>' + escHtml(e.first_name) + ' ' + escHtml(e.last_name) + '</td>';
        html += '<td><span class="cims-tag cims-tag-' + (e.pay_type === 'salaried' ? 'active' : 'info') + '">' + payLabel + '</span></td>';
        html += '<td class="cims_money_format">' + salaryDisplay + '</td>';
        html += '<td style="text-align:center;"><span class="cims-tag cims-tag-active">Active</span></td>';
        html += '</tr>';
    }

    tbody.innerHTML = html;
    document.getElementById('empCountBadge').textContent = employees.length;
    document.getElementById('chkAll').checked = true;
    updateCount();
}

function toggleAll(chk) {
    var boxes = document.querySelectorAll('.emp-check');
    boxes.forEach(function(b) { b.checked = chk.checked; });
    updateCount();
}

function updateCount() {
    var checked = document.querySelectorAll('.emp-check:checked').length;
    var total = document.querySelectorAll('.emp-check').length;
    document.getElementById('selectedCount').textContent = checked + ' of ' + total + ' employees selected';
    checkReady();
}

function checkReady() {
    var companyId = document.getElementById('selCompany').value;
    var period = document.getElementById('hid_pay_period').value;
    var checked = document.querySelectorAll('.emp-check:checked').length;
    var btn = document.getElementById('btnCreateRun');

    if (companyId && period && checked > 0) {
        btn.disabled = false;
    } else {
        btn.disabled = true;
    }
}

function formatMoney(val) {
    var n = parseFloat(val) || 0;
    var parts = n.toFixed(2).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return parts.join('.');
}

function escHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Auto-select current month on load
document.addEventListener('DOMContentLoaded', function() {
    var now = new Date();
    var curMonth = now.getMonth() + 1;
    var curYear = now.getFullYear();
    var pills = document.querySelectorAll('.period-pill');
    pills.forEach(function(p) {
        if (parseInt(p.dataset.month) === curMonth && parseInt(p.dataset.year) === curYear) {
            p.click();
        }
    });
});
</script>
@endpush
