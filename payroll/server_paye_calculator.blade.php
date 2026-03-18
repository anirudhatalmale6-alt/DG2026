@extends('layouts.default')

@section('title', 'PAYE Calculator')

@push('styles')
<style>
.payroll-wrapper { max-width: none; margin: 0; padding: 30px; }

/* Calculator card */
.calc-card { border: none; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
.calc-card .card-header { background: linear-gradient(135deg, #004D40, #00796B); color: #fff; border-radius: 10px 10px 0 0; padding: 16px 20px; }
.calc-card .card-header h4 { margin: 0; font-size: 15px; font-weight: 700; letter-spacing: 0.3px; }

/* Input area */
.calc-input-group { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
.calc-input-group .calc-field { flex: 1 1 200px; min-width: 180px; }
.calc-input-group .calc-field label { display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; color: #5b676d; margin-bottom: 6px; }
.calc-input-group .calc-field input,
.calc-input-group .calc-field select { width: 100%; height: 42px; border: 2px solid #dee2e6; border-radius: 8px; padding: 0 12px; font-size: 15px; font-weight: 600; color: #333; transition: border-color 0.2s; box-sizing: border-box; }
.calc-input-group .calc-field input:focus,
.calc-input-group .calc-field select:focus { border-color: #00796B; outline: none; }
.calc-input-group .calc-btn { flex: 0 0 auto; }
.calc-input-group .calc-btn .btn { height: 42px; padding: 0 30px; font-size: 14px; font-weight: 700; }

/* Results */
.calc-results { display: none; }
.calc-results.active { display: block; }
.result-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px; }
.result-box { background: #fff; border: 1px solid #e8ecef; border-radius: 10px; padding: 18px; box-shadow: 0 1px 6px rgba(0,0,0,0.04); }
.result-box h5 { margin: 0 0 12px 0; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; color: #5b676d; border-bottom: 2px solid #eee; padding-bottom: 8px; }
.result-row { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; font-size: 14px; }
.result-row .label { color: #5b676d; }
.result-row .value { font-weight: 700; color: #333; font-variant-numeric: tabular-nums; }
.result-row.highlight { background: #E0F2F1; margin: 0 -18px; padding: 8px 18px; border-radius: 6px; }
.result-row.highlight .label { color: #004D40; font-weight: 700; }
.result-row.highlight .value { color: #004D40; font-size: 16px; }
.result-row.total { border-top: 2px solid #004D40; margin-top: 6px; padding-top: 10px; }
.result-row.total .label { font-weight: 700; color: #004D40; }
.result-row.total .value { font-weight: 800; color: #004D40; font-size: 16px; }

/* Summary banner */
.summary-banner { background: linear-gradient(135deg, #004D40, #00796B); border-radius: 10px; padding: 24px; color: #fff; margin-bottom: 20px; }
.summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; }
.summary-item { text-align: center; }
.summary-item .s-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.8; margin-bottom: 4px; }
.summary-item .s-value { font-size: 22px; font-weight: 800; }

/* Bracket table */
.bracket-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.bracket-table th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; color: #5b676d; padding: 8px; background: #f8f9fa; border-bottom: 2px solid #dee2e6; }
.bracket-table td { padding: 8px; font-size: 13px; border-bottom: 1px solid #eee; }
.bracket-table tr.active-bracket { background: #E0F2F1; font-weight: 700; }
.bracket-table .cims_money_format { text-align: right; padding-right: 3px; font-variant-numeric: tabular-nums; }

/* Loading spinner */
.calc-loading { display: none; text-align: center; padding: 40px; }
.calc-loading.active { display: block; }
.calc-loading i { font-size: 32px; color: #00796B; }
</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-calculator"></i></div>
            <div><h1>PAYE Calculator</h1><p>Cost-to-company tax simulator — SARS {{ cims_tax_year() }}</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">PAYE Calculator</span>
        </div>
        <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    <!-- Calculator Input -->
    <div class="row">
        <div class="col-12">
            <div class="card calc-card">
                <div class="card-header"><h4><i class="fas fa-calculator"></i> CALCULATE PAYE</h4></div>
                <div class="card-body" style="padding: 24px;">
                    <div class="calc-input-group">
                        <div class="calc-field" style="flex: 2 1 300px;">
                            <label>Monthly Gross Salary (R)</label>
                            <input type="text" id="calcGross" placeholder="e.g. 25 000" autocomplete="off">
                        </div>
                        <div class="calc-field">
                            <label>Age Group</label>
                            <select id="calcAge" class="sd_drop_class">
                                <option value="30">Under 65</option>
                                <option value="67">65 to 74</option>
                                <option value="78">75 and over</option>
                            </select>
                        </div>
                        <div class="calc-field">
                            <label>Pay Frequency</label>
                            <select id="calcFreq" class="sd_drop_class">
                                <option value="12">Monthly</option>
                                <option value="26">Bi-weekly</option>
                                <option value="52">Weekly</option>
                            </select>
                        </div>
                        <div class="calc-btn">
                            <button type="button" class="btn button_master_add" id="btnCalculate" onclick="doCalculate()"><i class="fas fa-calculator"></i> Calculate</button>
                        </div>
                        <div class="calc-btn">
                            <button type="button" class="btn button_master_close" onclick="resetCalc()" style="height:42px;padding:0 20px;color:#fff;"><i class="fas fa-eraser"></i> Clear</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div class="calc-loading" id="calcLoading">
        <i class="fas fa-spinner fa-spin"></i>
        <p style="color:#5b676d;margin-top:10px;">Calculating...</p>
    </div>

    <!-- Results -->
    <div class="calc-results" id="calcResults">
        <!-- Summary Banner -->
        <div class="summary-banner" id="summaryBanner"></div>

        <!-- Detail Boxes -->
        <div class="result-grid" id="resultGrid"></div>

        <!-- Tax Brackets -->
        <div class="row">
            <div class="col-12">
                <div class="card calc-card">
                    <div class="card-header"><h4><i class="fas fa-layer-group"></i> TAX BRACKETS — SARS <span id="bracketYear"></span></h4></div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-responsive">
                            <table class="bracket-table" id="bracketTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="cims_money_format">From (R)</th>
                                        <th class="cims_money_format">To (R)</th>
                                        <th class="cims_money_format">Rate</th>
                                        <th class="cims_money_format">Base Tax (R)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function formatMoney(val) {
    var n = parseFloat(val) || 0;
    var parts = n.toFixed(2).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return parts.join('.');
}

// Format input as you type
document.getElementById('calcGross').addEventListener('input', function() {
    var raw = this.value.replace(/[^0-9.]/g, '');
    if (raw) {
        var parts = raw.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        this.value = parts.join('.');
    }
});

document.getElementById('calcGross').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') doCalculate();
});

function doCalculate() {
    var grossRaw = document.getElementById('calcGross').value.replace(/[^0-9.]/g, '');
    var gross = parseFloat(grossRaw);
    if (!gross || gross <= 0) {
        CIMSAlert.error('Please enter a valid monthly gross salary.');
        return;
    }

    var age = document.getElementById('calcAge').value;
    var freq = document.getElementById('calcFreq').value;

    document.getElementById('calcResults').classList.remove('active');
    document.getElementById('calcLoading').classList.add('active');

    var url = '{{ route("cimspayroll.paye-calculator.calculate") }}';
    fetch(url + '?gross=' + gross + '&age=' + age + '&frequency=' + freq, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        document.getElementById('calcLoading').classList.remove('active');
        if (data.success) {
            renderResults(data);
            document.getElementById('calcResults').classList.add('active');
        } else {
            CIMSAlert.error(data.message || 'Calculation error');
        }
    })
    .catch(function(err) {
        document.getElementById('calcLoading').classList.remove('active');
        CIMSAlert.error('Server error — please try again.');
    });
}

function renderResults(d) {
    var r = d.data;

    // Summary banner
    var html = '<div class="summary-grid">';
    html += '<div class="summary-item"><div class="s-label">Gross Salary</div><div class="s-value">R ' + formatMoney(r.gross_monthly) + '</div></div>';
    html += '<div class="summary-item"><div class="s-label">PAYE Tax</div><div class="s-value">R ' + formatMoney(r.monthly_paye) + '</div></div>';
    html += '<div class="summary-item"><div class="s-label">Net Take-Home</div><div class="s-value">R ' + formatMoney(r.net_pay) + '</div></div>';
    html += '<div class="summary-item"><div class="s-label">Total Cost to Company</div><div class="s-value">R ' + formatMoney(r.cost_to_company) + '</div></div>';
    html += '</div>';
    document.getElementById('summaryBanner').innerHTML = html;

    // Detail boxes
    var grid = '';

    // Income box
    grid += '<div class="result-box"><h5><i class="fas fa-coins"></i> Income</h5>';
    grid += resultRow('Monthly Gross', r.gross_monthly);
    grid += resultRow('Annual Gross (x12)', r.annual_income);
    grid += resultRow('Tax Year', r.tax_year, true);
    grid += resultRow('Age Group', r.age_label, true);
    grid += '</div>';

    // PAYE box
    grid += '<div class="result-box"><h5><i class="fas fa-percentage"></i> PAYE Calculation</h5>';
    grid += resultRow('Tax Bracket', r.bracket_label, true);
    grid += resultRow('Annual Tax (before rebates)', r.annual_tax_before_rebates);
    grid += resultRow('Less: Primary Rebate', r.primary_rebate);
    if (parseFloat(r.secondary_rebate) > 0) grid += resultRow('Less: Secondary Rebate (65+)', r.secondary_rebate);
    if (parseFloat(r.tertiary_rebate) > 0) grid += resultRow('Less: Tertiary Rebate (75+)', r.tertiary_rebate);
    grid += resultRow('Annual Tax (after rebates)', r.annual_tax_after_rebates);
    grid += resultRowHighlight('Monthly PAYE', r.monthly_paye);
    grid += '</div>';

    // Deductions box
    grid += '<div class="result-box"><h5><i class="fas fa-minus-circle"></i> Employee Deductions (Monthly)</h5>';
    grid += resultRow('PAYE Income Tax', r.monthly_paye);
    grid += resultRow('UIF (Employee 1%)', r.uif_employee);
    grid += resultRowTotal('Total Deductions', r.total_employee_deductions);
    grid += resultRowHighlight('Net Take-Home Pay', r.net_pay);
    grid += '</div>';

    // Employer box
    grid += '<div class="result-box"><h5><i class="fas fa-building"></i> Employer Contributions (Monthly)</h5>';
    grid += resultRow('UIF (Employer 1%)', r.uif_employer);
    grid += resultRow('SDL (1%)', r.sdl);
    grid += resultRowTotal('Total Employer Cost', r.total_employer_contributions);
    grid += resultRowHighlight('Total Cost to Company', r.cost_to_company);
    grid += '</div>';

    document.getElementById('resultGrid').innerHTML = grid;

    // Bracket table
    document.getElementById('bracketYear').textContent = r.tax_year;
    var tbody = '';
    for (var i = 0; i < r.brackets.length; i++) {
        var b = r.brackets[i];
        tbody += '<tr class="' + (b.active ? 'active-bracket' : '') + '">';
        tbody += '<td>' + (i + 1) + '</td>';
        tbody += '<td class="cims_money_format">' + formatMoney(b.min) + '</td>';
        tbody += '<td class="cims_money_format">' + formatMoney(b.max) + '</td>';
        tbody += '<td class="cims_money_format">' + b.rate + '%</td>';
        tbody += '<td class="cims_money_format">' + formatMoney(b.base_tax) + '</td>';
        tbody += '</tr>';
    }
    document.getElementById('bracketTable').querySelector('tbody').innerHTML = tbody;
}

function resultRow(label, value, isText) {
    var display = isText ? value : ('R ' + formatMoney(value));
    return '<div class="result-row"><span class="label">' + label + '</span><span class="value">' + display + '</span></div>';
}
function resultRowHighlight(label, value) {
    return '<div class="result-row highlight"><span class="label">' + label + '</span><span class="value">R ' + formatMoney(value) + '</span></div>';
}
function resultRowTotal(label, value) {
    return '<div class="result-row total"><span class="label">' + label + '</span><span class="value">R ' + formatMoney(value) + '</span></div>';
}

function resetCalc() {
    document.getElementById('calcGross').value = '';
    document.getElementById('calcAge').selectedIndex = 0;
    document.getElementById('calcFreq').selectedIndex = 0;
    document.getElementById('calcResults').classList.remove('active');
    document.getElementById('calcLoading').classList.remove('active');
    document.getElementById('calcGross').focus();
}
</script>
@endpush
