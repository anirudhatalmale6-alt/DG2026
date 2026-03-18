@extends('layouts.default')

@section('title', 'View EMP201 - ' . $declaration->client_code)

@push('styles')
<style>
.smartdash-page-header {
    background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
    border-radius: 12px;
    padding: 20px 28px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.25);
}
.smartdash-page-header .page-title { display: flex; align-items: center; gap: 15px; }
.smartdash-page-header .page-icon { width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
.smartdash-page-header .page-title h1 { font-size: 22px; font-weight: 800; margin: 0; }
.smartdash-page-header .page-title p { font-size: 13px; margin: 4px 0 0 0; opacity: 0.9; }
.detail-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
.detail-card .card-header-bar { background: linear-gradient(135deg, #17A2B8 0%, #138496 100%); color: #fff; padding: 14px 24px; font-size: 16px; font-weight: 700; letter-spacing: 0.5px; }
.detail-card .card-body-content { padding: 24px; }
.detail-row { display: flex; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
.detail-row:last-child { border-bottom: none; }
.detail-label { width: 200px; font-weight: 600; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 0.3px; }
.detail-value { flex: 1; font-size: 15px; color: #1e293b; font-weight: 500; }
.detail-value.currency { font-family: 'Courier New', monospace; font-size: 16px; text-align: right; }
.detail-value.highlight { color: #17A2B8; font-weight: 700; font-size: 18px; }
.status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
.status-badge.active { background: #dcfce7; color: #16a34a; }
.status-badge.inactive { background: #fef3c7; color: #d97706; }
.totals-section { background: linear-gradient(135deg, #f0fdfa 0%, #e0f7fa 100%); border: 2px solid #17A2B8; border-radius: 12px; padding: 20px; }
.btn-action { padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
</style>
@endpush

@section('content')
<div class="container-fluid" style="padding: 20px;">

    <div class="smartdash-page-header mb-4">
        <div class="page-title">
            <div class="page-icon"><i class="fa fa-file-invoice-dollar"></i></div>
            <div>
                <h1>EMP201 - {{ $declaration->client_code }}</h1>
                <p>{{ $declaration->company_name }} | {{ $declaration->pay_period }}</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('cimsemp201.index') }}" class="btn-action" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('cimsemp201.edit', $declaration->id) }}" class="btn-action" style="background:#fff;color:#17A2B8;">
                <i class="fa fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            {{-- Company Details --}}
            <div class="detail-card">
                <div class="card-header-bar"><i class="fa fa-building me-2"></i> Company Details</div>
                <div class="card-body-content">
                    <div class="detail-row"><div class="detail-label">Company Name</div><div class="detail-value">{{ $declaration->company_name }}</div></div>
                    <div class="detail-row"><div class="detail-label">Client Code</div><div class="detail-value">{{ $declaration->client_code }}</div></div>
                    <div class="detail-row"><div class="detail-label">Company Number</div><div class="detail-value">{{ $declaration->company_number }}</div></div>
                    <div class="detail-row"><div class="detail-label">VAT Number</div><div class="detail-value">{{ $declaration->vat_number }}</div></div>
                    <div class="detail-row"><div class="detail-label">Income Tax Number</div><div class="detail-value">{{ $declaration->income_tax_number }}</div></div>
                    <div class="detail-row"><div class="detail-label">PAYE No</div><div class="detail-value">{{ $declaration->paye_number }}</div></div>
                    <div class="detail-row"><div class="detail-label">SDL No</div><div class="detail-value">{{ $declaration->sdl_number }}</div></div>
                    <div class="detail-row"><div class="detail-label">UIF No</div><div class="detail-value">{{ $declaration->uif_number }}</div></div>
                </div>
            </div>

            {{-- Public Officer --}}
            <div class="detail-card">
                <div class="card-header-bar"><i class="fa fa-user-tie me-2"></i> Public Officer / Contact</div>
                <div class="card-body-content">
                    <div class="detail-row"><div class="detail-label">Name</div><div class="detail-value">{{ $declaration->title }} {{ $declaration->initial }} {{ $declaration->first_name }} {{ $declaration->surname }}</div></div>
                    <div class="detail-row"><div class="detail-label">Position</div><div class="detail-value">{{ $declaration->position }}</div></div>
                    <div class="detail-row"><div class="detail-label">Office Number</div><div class="detail-value">{{ $declaration->telephone_number }}</div></div>
                    <div class="detail-row"><div class="detail-label">Mobile</div><div class="detail-value">{{ $declaration->mobile_number }}</div></div>
                    <div class="detail-row"><div class="detail-label">WhatsApp</div><div class="detail-value">{{ $declaration->whatsapp_number }}</div></div>
                    <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value">{{ $declaration->email }}</div></div>
                </div>
            </div>

            {{-- Documents --}}
            @if($declaration->file_emp201_return || $declaration->file_emp201_statement || $declaration->file_working_papers || $declaration->file_emp201_pack)
            <div class="detail-card">
                <div class="card-header-bar"><i class="fa fa-paperclip me-2"></i> Uploaded Documents</div>
                <div class="card-body-content">
                    @if($declaration->file_emp201_return)
                    <div class="detail-row"><div class="detail-label">SARS EMP201 Return</div><div class="detail-value"><a href="/uploads/emp201/{{ $declaration->file_emp201_return }}" target="_blank"><i class="fa fa-download me-1"></i> {{ $declaration->file_emp201_return }}</a></div></div>
                    @endif
                    @if($declaration->file_emp201_statement)
                    <div class="detail-row"><div class="detail-label">SARS PAYE Statement</div><div class="detail-value"><a href="/uploads/emp201/{{ $declaration->file_emp201_statement }}" target="_blank"><i class="fa fa-download me-1"></i> {{ $declaration->file_emp201_statement }}</a></div></div>
                    @endif
                    @if($declaration->file_working_papers)
                    <div class="detail-row"><div class="detail-label">Working Papers</div><div class="detail-value"><a href="/uploads/emp201/{{ $declaration->file_working_papers }}" target="_blank"><i class="fa fa-download me-1"></i> {{ $declaration->file_working_papers }}</a></div></div>
                    @endif
                    @if($declaration->file_emp201_pack)
                    <div class="detail-row"><div class="detail-label">EMP201 Pack</div><div class="detail-value"><a href="/uploads/emp201/{{ $declaration->file_emp201_pack }}" target="_blank"><i class="fa fa-download me-1"></i> {{ $declaration->file_emp201_pack }}</a></div></div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            {{-- Period & Status --}}
            <div class="detail-card">
                <div class="card-header-bar"><i class="fa fa-calendar me-2"></i> Period & Status</div>
                <div class="card-body-content">
                    <div class="detail-row"><div class="detail-label">Tax Period</div><div class="detail-value" style="font-size:18px;font-weight:700;color:#17A2B8">{{ $declaration->pay_period }}</div></div>
                    <div class="detail-row"><div class="detail-label">Financial Year</div><div class="detail-value" style="font-weight:700">{{ $declaration->financial_year }}</div></div>
                    <div class="detail-row"><div class="detail-label">Payment Ref</div><div class="detail-value" style="font-family:monospace">{{ $declaration->payment_reference }}</div></div>
                    <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value"><span class="status-badge {{ $declaration->status == 1 ? 'active' : 'inactive' }}"><i class="fa fa-circle" style="font-size:8px"></i> {{ $declaration->status == 1 ? 'Active' : 'Inactive' }}</span></div></div>
                    <div class="detail-row"><div class="detail-label">Created</div><div class="detail-value">{{ $declaration->created_at ? $declaration->created_at->format('d M Y H:i') : '-' }}</div></div>
                </div>
            </div>

            {{-- Payroll Tax Summary --}}
            <div class="detail-card">
                <div class="card-header-bar"><i class="fa fa-calculator me-2"></i> Payroll Tax</div>
                <div class="card-body-content">
                    <div class="detail-row"><div class="detail-label">PAYE Liability</div><div class="detail-value currency">R {{ number_format($declaration->paye_liability, 2) }}</div></div>
                    <div class="detail-row"><div class="detail-label">SDL Liability</div><div class="detail-value currency">R {{ number_format($declaration->sdl_liability, 2) }}</div></div>
                    <div class="detail-row"><div class="detail-label">UIF Liability</div><div class="detail-value currency">R {{ number_format($declaration->uif_liability, 2) }}</div></div>
                    <div class="detail-row" style="border-top:2px solid #e2e8f0;padding-top:12px"><div class="detail-label">Payroll Liability</div><div class="detail-value currency" style="font-weight:700">R {{ number_format($declaration->paye_liability + $declaration->sdl_liability + $declaration->uif_liability, 2) }}</div></div>
                </div>
            </div>

            {{-- Penalties --}}
            <div class="detail-card">
                <div class="card-header-bar"><i class="fa fa-exclamation-triangle me-2"></i> Penalties & Interest</div>
                <div class="card-body-content">
                    <div class="detail-row"><div class="detail-label">Penalty</div><div class="detail-value currency">R {{ number_format($declaration->penalty, 2) }}</div></div>
                    <div class="detail-row"><div class="detail-label">Interest</div><div class="detail-value currency">R {{ number_format($declaration->interest, 2) }}</div></div>
                    <div class="detail-row"><div class="detail-label">Other</div><div class="detail-value currency">R {{ number_format($declaration->other, 2) }}</div></div>
                </div>
            </div>

            {{-- Total Due --}}
            <div class="totals-section mb-3">
                <div class="detail-row" style="border:none"><div class="detail-label" style="font-size:15px">TAX PAYABLE</div><div class="detail-value currency highlight" style="font-size:22px">R {{ number_format($declaration->tax_payable, 2) }}</div></div>
            </div>

            {{-- Payment --}}
            <div class="detail-card">
                <div class="card-header-bar"><i class="fa fa-credit-card me-2"></i> Payment</div>
                <div class="card-body-content">
                    <div class="detail-row"><div class="detail-label">Payment Date</div><div class="detail-value">{{ $declaration->payment_date ? $declaration->payment_date->format('d M Y') : '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Payment Type</div><div class="detail-value">{{ ucwords(str_replace('_', ' ', $declaration->payment_type ?? '-')) }}</div></div>
                    <div class="detail-row"><div class="detail-label">Amount Paid</div><div class="detail-value currency">R {{ number_format($declaration->payment_amount, 2) }}</div></div>
                    <div class="detail-row" style="border-top:2px solid #e2e8f0;padding-top:12px"><div class="detail-label">Balance Outstanding</div><div class="detail-value currency" style="font-weight:700;color:{{ $declaration->balance_outstanding > 0 ? '#dc2626' : '#16a34a' }}">R {{ number_format($declaration->balance_outstanding, 2) }}</div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
