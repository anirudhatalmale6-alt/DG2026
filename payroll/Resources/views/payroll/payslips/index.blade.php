@extends('layouts.default')

@section('title', 'Payslips')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div><h1>Payslips</h1><p>Generate and download employee payslips</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Payslips</span>
        </div>
        <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Filter -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
            <div class="card-body">
                <form method="GET" action="{{ route('cimspayroll.payslips.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="company_id" class="form-control">
                                <option value="">-- All Companies --</option>
                                @foreach($companies as $c)
                                <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>
                                @endforeach
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

    <!-- Pay Runs with Payslips -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-file-invoice-dollar"></i> PROCESSED PAY RUNS</h4></div>
            <div class="card-body" style="padding:0;">
                @if($payRuns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Period</th>
                                <th>Company</th>
                                <th>Employees</th>
                                <th style="text-align:right;">Total Net Pay</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payRuns as $pr)
                            <tr>
                                <td><strong>{{ $pr->pay_period }}</strong><br><small class="text-muted">{{ $pr->period_start->format('d M') }} — {{ $pr->period_end->format('d M Y') }}</small></td>
                                <td>{{ $pr->company->company_name ?? '—' }}</td>
                                <td>{{ $pr->employee_count }}</td>
                                <td style="text-align:right;font-weight:700;">R {{ number_format($pr->total_net_pay, 2) }}</td>
                                <td>
                                    @php $sc = ['processed' => 'warning', 'approved' => 'success']; @endphp
                                    <span class="badge bg-{{ $sc[$pr->status] ?? 'secondary' }}">{{ ucfirst($pr->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('cimspayroll.payslips.preview', $pr->id) }}" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;" title="Preview & Download"><i class="fa fa-eye"></i> View</a>
                                    <a href="{{ route('cimspayroll.payslips.download-bulk', $pr->id) }}" class="btn button_master_save" style="padding:4px 12px;font-size:12px;" title="Download All Payslips (PDF)"><i class="fa fa-download"></i> All PDF</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $payRuns->withQueryString()->links() }}</div>
                @else
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-file-invoice-dollar" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                    <p>No processed pay runs yet. Process a pay run first to generate payslips.</p>
                    <a href="{{ route('cimspayroll.pay-runs.index') }}" class="btn button_master_add"><i class="fa fa-play-circle"></i> Go to Pay Runs</a>
                </div>
                @endif
            </div>
        </div>
    </div></div>
</div>
@endsection
