@extends('layouts.default')

@section('title', 'Pay Runs')

@push('styles')
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-play-circle"></i></div>
            <div><h1>Pay Runs</h1><p>Process monthly payroll</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Pay Runs</span>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('cimspayroll.pay-runs.create') }}" class="btn button_master_add"><i class="fa fa-plus"></i> New Pay Run</a>
            <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
            <div class="card-body">
                <form method="GET" action="{{ route('cimspayroll.pay-runs.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="company_id" class="form-control"><option value="">-- All Companies --</option>
                                @foreach($companies as $c)<option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->company_name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control"><option value="">-- All Status --</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Processed</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>
                        <div class="col-md-2"><button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Filter</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div></div>

    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-list"></i> PAY RUNS</h4></div>
            <div class="card-body" style="padding:0;">
                @if($payRuns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;"><tr><th>Period</th><th>Company</th><th>Employees</th><th style="text-align:right;">Gross</th><th style="text-align:right;">Deductions</th><th style="text-align:right;">Net Pay</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($payRuns as $pr)
                            <tr>
                                <td><strong>{{ $pr->pay_period }}</strong><br><small class="text-muted">{{ $pr->period_start->format('d M') }} — {{ $pr->period_end->format('d M Y') }}</small></td>
                                <td>{{ $pr->company->company_name ?? '—' }}</td>
                                <td>{{ $pr->employee_count }}</td>
                                <td style="text-align:right;">R {{ number_format($pr->total_gross, 2) }}</td>
                                <td style="text-align:right;">R {{ number_format($pr->total_deductions, 2) }}</td>
                                <td style="text-align:right;font-weight:700;">R {{ number_format($pr->total_net_pay, 2) }}</td>
                                <td>
                                    @php $sc = ['draft' => 'secondary', 'processed' => 'warning', 'approved' => 'success', 'cancelled' => 'danger']; @endphp
                                    <span class="badge bg-{{ $sc[$pr->status] ?? 'secondary' }}">{{ ucfirst($pr->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('cimspayroll.pay-runs.show', $pr->id) }}" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;"><i class="fa fa-eye"></i> View</a>
                                    @if($pr->status !== 'approved')
                                    <form method="POST" action="{{ route('cimspayroll.pay-runs.destroy', $pr->id) }}" onsubmit="return confirm('Delete this pay run?');" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn button_master_delete" style="padding:4px 12px;font-size:12px;"><i class="fa fa-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $payRuns->withQueryString()->links() }}</div>
                @else
                <div style="text-align:center;padding:40px;color:#999;"><i class="fas fa-play-circle" style="font-size:48px;margin-bottom:12px;display:block;"></i><p>No pay runs found. Click <strong>New Pay Run</strong> to get started.</p></div>
                @endif
            </div>
        </div>
    </div></div>
</div>
@endsection
