@extends('layouts.default')

@section('title', 'Bank Statement Conversions')

@push('styles')
<style>
    .bankconv-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
    .bank-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .bank-fnb { background: #009b4d; color: #fff; }
    .bank-standard { background: #003b73; color: #fff; }
    .bank-absa { background: #af1e2d; color: #fff; }
    .bank-nedbank { background: #006e51; color: #fff; }
    .bank-capitec { background: #003e82; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid bankconv-wrapper">

    {{-- Page Header --}}
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-exchange-alt"></i></div>
            <div>
                <h1>Bank Statement Conversions</h1>
                <p>Convert bank statements to QuickBooks format</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <span class="current">Conversions</span>
        </div>
    </div>

    {{-- Breadcrumb White --}}
    <div class="breadcrumb_white">
        <div class="bw_title_area">
            <div class="bw_icon"><i class="fas fa-university"></i></div>
            <div>
                <div class="bw_title">Bank Statement Converter</div>
                <div class="bw_subtitle">PDF to QuickBooks CSV — Conversion History</div>
            </div>
        </div>
    </div>

    {{-- Quick Launch --}}
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-4 col-lg-3 mb-3">
            <a href="{{ route('cimsbankconv.convert') }}" class="card smartdash-form-card text-center" style="text-decoration:none; cursor:pointer;">
                <div class="card-body" style="padding: 20px 10px;">
                    <div style="font-size: 28px; color: #17A2B8; margin-bottom: 8px;"><i class="fas fa-exchange-alt"></i></div>
                    <div style="font-weight: 700; color: #333; font-size: 13px;">New Conversion</div>
                    <div style="font-size: 11px; color: #888;">Bank to QuickBooks</div>
                </div>
            </a>
        </div>
    </div>

    {{-- Conversion History --}}
    <div class="card smartdash-form-card">
        <div class="card-header">
            <h4><i class="fa fa-history"></i> CONVERSION HISTORY</h4>
        </div>
        <div class="card-body">
            @if($conversions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" style="font-size: 13px;">
                        <thead style="background: #0d3d56; color: #fff;">
                            <tr>
                                <th>Date</th>
                                <th>Bank</th>
                                <th>Client</th>
                                <th>Account</th>
                                <th>Period</th>
                                <th class="text-right">Credits</th>
                                <th class="text-right">Debits</th>
                                <th class="text-center">Txns</th>
                                <th>Converted By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conversions as $conv)
                                <tr>
                                    <td>{{ $conv->created_at->format('d M Y H:i') }}</td>
                                    <td><span class="bank-badge bank-{{ $conv->bank_type }}">{{ strtoupper($conv->bank_type) }}</span></td>
                                    <td>{{ $conv->company_name ?: $conv->client_code ?: '-' }}</td>
                                    <td>{{ $conv->account_number ?: '-' }}</td>
                                    <td style="font-size: 12px;">{{ $conv->statement_period ?: '-' }}</td>
                                    <td class="text-right" style="color: #059669; font-weight: 600;">{{ number_format($conv->total_credits, 2) }}</td>
                                    <td class="text-right" style="color: #dc2626; font-weight: 600;">{{ number_format($conv->total_debits, 2) }}</td>
                                    <td class="text-center">{{ $conv->transaction_count }}</td>
                                    <td>{{ $conv->converted_by ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $conversions->links() }}
            @else
                <div class="text-center" style="padding: 40px;">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 12px;"></i>
                    <p style="color: #888; font-size: 15px;">No conversions yet. Select a bank above to get started.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    @if(session('info'))
        Swal.fire({
            icon: 'info',
            title: 'Coming Soon',
            text: '{{ session('info') }}',
            confirmButtonColor: '#17A2B8'
        });
    @endif
</script>
@endpush
