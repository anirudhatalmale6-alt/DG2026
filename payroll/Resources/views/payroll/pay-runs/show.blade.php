@extends('layouts.default')

@section('title', 'Pay Run — ' . $payRun->pay_period)

@push('styles')
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.summary-card { text-align: center; padding: 16px; border-radius: 8px; background: #f8f9fa; }
.summary-card h3 { margin: 0; font-size: 22px; }
.summary-card small { color: #888; font-size: 12px; }
.line-items-table th, .line-items-table td { padding: 4px 8px; font-size: 12px; }
.income-row { background: #e8f5e9; }
.deduction-row { background: #fce4ec; }
.employer-row { background: #e3f2fd; }
</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-play-circle"></i></div>
            <div>
                <h1>Pay Run: {{ $payRun->pay_period }}</h1>
                <p>{{ $payRun->company->company_name ?? '—' }} — {{ $payRun->period_start->format('d M') }} to {{ $payRun->period_end->format('d M Y') }}</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.pay-runs.index') }}">Pay Runs</a>
            <span class="separator">/</span>
            <span class="current">{{ $payRun->pay_period }}</span>
        </div>
        <div style="display:flex;gap:8px;">
            @if($payRun->status === 'draft' || $payRun->status === 'processed')
            <form method="POST" action="{{ route('cimspayroll.pay-runs.process', $payRun->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn button_master_save" onclick="return confirm('{{ $payRun->status === 'processed' ? 'Re-process this pay run? Existing calculations will be overwritten.' : 'Process this pay run? This will calculate payroll for all active employees.' }}');"><i class="fa fa-cogs"></i> {{ $payRun->status === 'processed' ? 'Re-Process' : 'Process' }}</button>
            </form>
            @endif
            @if($payRun->status === 'processed')
            <form method="POST" action="{{ route('cimspayroll.pay-runs.approve', $payRun->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn button_master_add" onclick="return confirm('Approve this pay run? This will lock it.');"><i class="fa fa-check-circle"></i> Approve</button>
            </form>
            @endif
            <a href="{{ route('cimspayroll.pay-runs.index') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Status & Summary -->
    <div class="row" style="margin-bottom:16px;">
        <div class="col-md-2">
            <div class="summary-card">
                @php $sc = ['draft' => 'secondary', 'processed' => 'warning', 'approved' => 'success', 'cancelled' => 'danger']; @endphp
                <span class="badge bg-{{ $sc[$payRun->status] ?? 'secondary' }}" style="font-size:14px;padding:6px 16px;">{{ ucfirst($payRun->status) }}</span>
                <br><small>Status</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="summary-card">
                <h3>{{ $payRun->employee_count ?? $payRun->lines->count() }}</h3>
                <small>Employees</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="summary-card">
                <h3 style="color:#28a745;">R {{ number_format($payRun->total_gross, 2) }}</h3>
                <small>Total Gross</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="summary-card">
                <h3 style="color:#dc3545;">R {{ number_format($payRun->total_deductions, 2) }}</h3>
                <small>Total Deductions</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="summary-card">
                <h3 style="color:#007bff;">R {{ number_format($payRun->total_employer_cost, 2) }}</h3>
                <small>Employer Cost</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="summary-card" style="background:#e8f5e9;">
                <h3 style="color:#155724;font-weight:800;">R {{ number_format($payRun->total_net_pay, 2) }}</h3>
                <small>Total Net Pay</small>
            </div>
        </div>
    </div>

    @if($payRun->description)
    <div class="alert alert-info" style="font-size:13px;"><i class="fa fa-info-circle me-2"></i>{{ $payRun->description }}</div>
    @endif

    <!-- Employee Lines -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-users"></i> EMPLOYEE PAYROLL LINES</h4></div>
            <div class="card-body" style="padding:0;">
                @if($payRun->lines->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Employee</th>
                                <th style="text-align:right;">Basic</th>
                                <th style="text-align:right;">OT</th>
                                <th style="text-align:right;">Gross</th>
                                <th style="text-align:right;">PAYE</th>
                                <th style="text-align:right;">UIF</th>
                                <th style="text-align:right;">Other Ded.</th>
                                <th style="text-align:right;">Loans</th>
                                <th style="text-align:right;">Total Ded.</th>
                                <th style="text-align:right;font-weight:700;">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payRun->lines as $line)
                            @php
                                $otPay = $line->gross_pay - $line->basic_salary;
                                $loanDeds = $line->items->where('item_type', 'deduction')->filter(fn($i) => str_starts_with($i->name, 'Loan:'))->sum('amount');
                                $otherDeds = $line->total_deductions - $line->paye_tax - $line->uif_employee - $loanDeds;
                            @endphp
                            <tr style="cursor:pointer;" onclick="toggleDetail({{ $line->id }})">
                                <td>
                                    <strong>{{ $line->employee->first_name ?? '' }} {{ $line->employee->last_name ?? '' }}</strong>
                                    <br><small class="text-muted">#{{ $line->employee->employee_number ?? '' }}</small>
                                </td>
                                <td style="text-align:right;">R {{ number_format($line->basic_salary, 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($otPay, 2) }}</td>
                                <td style="text-align:right;font-weight:600;">R {{ number_format($line->gross_pay, 2) }}</td>
                                <td style="text-align:right;color:#dc3545;">R {{ number_format($line->paye_tax, 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($line->uif_employee, 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($otherDeds, 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($loanDeds, 2) }}</td>
                                <td style="text-align:right;color:#dc3545;font-weight:600;">R {{ number_format($line->total_deductions, 2) }}</td>
                                <td style="text-align:right;font-weight:800;color:#155724;">R {{ number_format($line->net_pay, 2) }}</td>
                            </tr>
                            <!-- Detail row (hidden by default) -->
                            <tr id="detail-{{ $line->id }}" style="display:none;">
                                <td colspan="10" style="background:#fafafa;padding:12px 20px;">
                                    <div class="row">
                                        <!-- Income Items -->
                                        <div class="col-md-4">
                                            <h6 style="color:#28a745;margin-bottom:8px;"><i class="fa fa-plus-circle"></i> Income</h6>
                                            <table class="table table-sm line-items-table mb-0">
                                                @foreach($line->items->where('item_type', 'income') as $item)
                                                <tr class="income-row">
                                                    <td>{{ $item->name }}</td>
                                                    <td style="text-align:right;font-weight:600;">R {{ number_format($item->amount, 2) }}</td>
                                                </tr>
                                                @endforeach
                                                <tr><td><strong>Total Gross</strong></td><td style="text-align:right;font-weight:700;">R {{ number_format($line->gross_pay, 2) }}</td></tr>
                                            </table>
                                        </div>
                                        <!-- Deduction Items -->
                                        <div class="col-md-4">
                                            <h6 style="color:#dc3545;margin-bottom:8px;"><i class="fa fa-minus-circle"></i> Deductions</h6>
                                            <table class="table table-sm line-items-table mb-0">
                                                @foreach($line->items->where('item_type', 'deduction') as $item)
                                                <tr class="deduction-row">
                                                    <td>{{ $item->name }}</td>
                                                    <td style="text-align:right;font-weight:600;">R {{ number_format($item->amount, 2) }}</td>
                                                </tr>
                                                @endforeach
                                                <tr><td><strong>Total Deductions</strong></td><td style="text-align:right;font-weight:700;">R {{ number_format($line->total_deductions, 2) }}</td></tr>
                                            </table>
                                        </div>
                                        <!-- Employer Contributions -->
                                        <div class="col-md-4">
                                            <h6 style="color:#007bff;margin-bottom:8px;"><i class="fa fa-building"></i> Employer Contributions</h6>
                                            <table class="table table-sm line-items-table mb-0">
                                                @foreach($line->items->where('item_type', 'employer_contribution') as $item)
                                                <tr class="employer-row">
                                                    <td>{{ $item->name }}</td>
                                                    <td style="text-align:right;font-weight:600;">R {{ number_format($item->amount, 2) }}</td>
                                                </tr>
                                                @endforeach
                                                <tr><td><strong>Total Employer</strong></td><td style="text-align:right;font-weight:700;">R {{ number_format($line->total_employer_contributions, 2) }}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div style="margin-top:8px;padding-top:8px;border-top:2px solid #28a745;text-align:right;">
                                        <strong style="font-size:16px;color:#155724;">Net Pay: R {{ number_format($line->net_pay, 2) }}</strong>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background:#f0f0f0;font-weight:700;">
                            <tr>
                                <td>TOTALS ({{ $payRun->lines->count() }} employees)</td>
                                <td style="text-align:right;">R {{ number_format($payRun->lines->sum('basic_salary'), 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($payRun->total_gross - $payRun->lines->sum('basic_salary'), 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($payRun->total_gross, 2) }}</td>
                                <td style="text-align:right;color:#dc3545;">R {{ number_format($payRun->lines->sum('paye_tax'), 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($payRun->lines->sum('uif_employee'), 2) }}</td>
                                <td style="text-align:right;">—</td>
                                <td style="text-align:right;">—</td>
                                <td style="text-align:right;color:#dc3545;">R {{ number_format($payRun->total_deductions, 2) }}</td>
                                <td style="text-align:right;color:#155724;">R {{ number_format($payRun->total_net_pay, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-cogs" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                    <p>No payroll lines yet. Click <strong>Process</strong> to calculate payroll for all active employees.</p>
                </div>
                @endif
            </div>
        </div>
    </div></div>

    @if($payRun->approved_at)
    <div class="alert alert-success" style="font-size:13px;margin-top:8px;">
        <i class="fa fa-check-circle me-2"></i>Approved on {{ $payRun->approved_at->format('d M Y H:i') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleDetail(id) {
    var row = document.getElementById('detail-' + id);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
    } else {
        row.style.display = 'none';
    }
}
</script>
@endpush
