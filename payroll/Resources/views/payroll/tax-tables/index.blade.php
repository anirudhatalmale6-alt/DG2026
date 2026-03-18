@extends('layouts.default')

@section('title', 'Tax Tables')

@push('styles')
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.edit-row { display: none; }
.edit-row.active { display: table-row; }
.view-row.hidden { display: none; }
.edit-row input, .edit-row select { height: 34px; border: 2px solid #17A2B8; border-radius: 6px; padding: 0 8px; font-size: 13px; }
.text-right { text-align: right; }
</style>
@endpush

@section('content')
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-calculator"></i></div>
            <div><h1>SARS Tax Tables</h1><p>Manage tax brackets, rebates & thresholds</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimspayroll.dashboard') }}">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Tax Tables</span>
        </div>
        <a href="{{ route('cimspayroll.dashboard') }}" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Tax Year Selector -->
    <div class="row mb-3">
        <div class="col-md-3">
            <form method="GET" action="{{ route('cimspayroll.tax-tables.index') }}" style="display:flex;gap:8px;align-items:center;">
                <label class="form-label" style="white-space:nowrap;margin:0;font-weight:700;">Tax Year:</label>
                <select name="tax_year" class="form-control" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $taxYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Tax Brackets -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-layer-group"></i> TAX BRACKETS — {{ $taxYear }}</h4></div>
            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;"><tr><th>Bracket</th><th class="text-right">Min (R)</th><th class="text-right">Max (R)</th><th class="text-right">Rate (%)</th><th class="text-right">Base Tax (R)</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($brackets as $i => $b)
                            <tr class="view-row" id="bv-{{ $b->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td class="text-right">R {{ number_format($b->min_amount, 0) }}</td>
                                <td class="text-right">R {{ number_format($b->max_amount, 0) }}</td>
                                <td class="text-right">{{ $b->rate }}%</td>
                                <td class="text-right">R {{ number_format($b->base_tax, 0) }}</td>
                                <td>
                                    <button type="button" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;" onclick="toggleEdit('b',{{ $b->id }})"><i class="fa fa-edit"></i></button>
                                    <form method="POST" action="{{ route('cimspayroll.tax-tables.bracket.destroy', $b->id) }}" onsubmit="return confirm('Delete?');" style="display:inline;">@csrf @method('DELETE')
                                        <button type="submit" class="btn button_master_delete" style="padding:4px 12px;font-size:12px;"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="edit-row" id="be-{{ $b->id }}">
                                <td>{{ $i + 1 }}</td>
                                <td colspan="5">
                                    <form method="POST" action="{{ route('cimspayroll.tax-tables.bracket.update', $b->id) }}" style="display:flex;gap:8px;align-items:center;">
                                        @csrf @method('PUT')
                                        <input type="number" step="0.01" name="min_amount" value="{{ $b->min_amount }}" style="width:120px;" placeholder="Min">
                                        <input type="number" step="0.01" name="max_amount" value="{{ $b->max_amount }}" style="width:120px;" placeholder="Max">
                                        <input type="number" step="0.01" name="rate" value="{{ $b->rate }}" style="width:80px;" placeholder="Rate%">
                                        <input type="number" step="0.01" name="base_tax" value="{{ $b->base_tax }}" style="width:120px;" placeholder="Base Tax">
                                        <button type="submit" class="btn button_master_save" style="padding:4px 14px;font-size:12px;"><i class="fa fa-check"></i></button>
                                        <button type="button" class="btn button_master_cancel" style="padding:4px 14px;font-size:12px;" onclick="toggleEdit('b',{{ $b->id }})"><i class="fa fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Add bracket -->
                <div style="padding:16px;">
                    <form method="POST" action="{{ route('cimspayroll.tax-tables.bracket.store') }}">
                        @csrf
                        <input type="hidden" name="tax_year" value="{{ $taxYear }}">
                        <div class="row">
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">Min (R)</label><input type="number" step="0.01" name="min_amount" class="form-control" required></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">Max (R)</label><input type="number" step="0.01" name="max_amount" class="form-control" required></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">Rate (%)</label><input type="number" step="0.01" name="rate" class="form-control" required></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">Base Tax (R)</label><input type="number" step="0.01" name="base_tax" class="form-control" value="0" required></div></div>
                            <div class="col-md-2"><div class="mb-3"><label class="form-label">&nbsp;</label><button type="submit" class="btn button_master_add" style="padding:6px 16px;font-size:13px;"><i class="fa fa-plus"></i> Add Bracket</button></div></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div></div>

    <div class="row">
        <!-- Rebates -->
        <div class="col-md-6">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-percentage"></i> TAX REBATES — {{ $taxYear }}</h4></div>
                <div class="card-body" style="padding:0;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background:#f8f9fa;"><tr><th>Type</th><th class="text-right">Amount (R)</th><th>Age</th><th>Actions</th></tr></thead>
                            <tbody>
                                @foreach($rebates as $r)
                                <tr class="view-row" id="rv-{{ $r->id }}">
                                    <td><strong>{{ ucfirst($r->rebate_type) }}</strong></td>
                                    <td class="text-right">R {{ number_format($r->amount, 0) }}</td>
                                    <td>{{ $r->age_threshold ? $r->age_threshold . '+' : '—' }}</td>
                                    <td><button type="button" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;" onclick="toggleEdit('r',{{ $r->id }})"><i class="fa fa-edit"></i></button></td>
                                </tr>
                                <tr class="edit-row" id="re-{{ $r->id }}">
                                    <td>{{ ucfirst($r->rebate_type) }}</td>
                                    <td colspan="3">
                                        <form method="POST" action="{{ route('cimspayroll.tax-tables.rebate.update', $r->id) }}" style="display:flex;gap:8px;align-items:center;">
                                            @csrf @method('PUT')
                                            <input type="number" step="0.01" name="amount" value="{{ $r->amount }}" style="width:120px;">
                                            <input type="number" name="age_threshold" value="{{ $r->age_threshold }}" style="width:70px;" placeholder="Age">
                                            <button type="submit" class="btn button_master_save" style="padding:4px 14px;font-size:12px;"><i class="fa fa-check"></i></button>
                                            <button type="button" class="btn button_master_cancel" style="padding:4px 14px;font-size:12px;" onclick="toggleEdit('r',{{ $r->id }})"><i class="fa fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="padding:16px;">
                        <form method="POST" action="{{ route('cimspayroll.tax-tables.rebate.store') }}">
                            @csrf
                            <input type="hidden" name="tax_year" value="{{ $taxYear }}">
                            <div class="row">
                                <div class="col-md-4"><select name="rebate_type" class="form-control" required><option value="primary">Primary</option><option value="secondary">Secondary</option><option value="tertiary">Tertiary</option></select></div>
                                <div class="col-md-3"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required></div>
                                <div class="col-md-2"><input type="number" name="age_threshold" class="form-control" placeholder="Age"></div>
                                <div class="col-md-3"><button type="submit" class="btn button_master_add" style="padding:6px 14px;font-size:13px;"><i class="fa fa-plus"></i></button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thresholds -->
        <div class="col-md-6">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-shield-alt"></i> TAX THRESHOLDS — {{ $taxYear }}</h4></div>
                <div class="card-body" style="padding:0;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background:#f8f9fa;"><tr><th>Age Group</th><th class="text-right">Threshold (R)</th><th>Actions</th></tr></thead>
                            <tbody>
                                @foreach($thresholds as $t)
                                <tr class="view-row" id="tv-{{ $t->id }}">
                                    <td><strong>{{ str_replace('_', ' ', ucfirst($t->age_group)) }}</strong></td>
                                    <td class="text-right">R {{ number_format($t->threshold_amount, 0) }}</td>
                                    <td><button type="button" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;" onclick="toggleEdit('t',{{ $t->id }})"><i class="fa fa-edit"></i></button></td>
                                </tr>
                                <tr class="edit-row" id="te-{{ $t->id }}">
                                    <td>{{ str_replace('_', ' ', ucfirst($t->age_group)) }}</td>
                                    <td colspan="2">
                                        <form method="POST" action="{{ route('cimspayroll.tax-tables.threshold.update', $t->id) }}" style="display:flex;gap:8px;align-items:center;">
                                            @csrf @method('PUT')
                                            <input type="number" step="0.01" name="threshold_amount" value="{{ $t->threshold_amount }}" style="width:140px;">
                                            <button type="submit" class="btn button_master_save" style="padding:4px 14px;font-size:12px;"><i class="fa fa-check"></i></button>
                                            <button type="button" class="btn button_master_cancel" style="padding:4px 14px;font-size:12px;" onclick="toggleEdit('t',{{ $t->id }})"><i class="fa fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="padding:16px;">
                        <form method="POST" action="{{ route('cimspayroll.tax-tables.threshold.store') }}">
                            @csrf
                            <input type="hidden" name="tax_year" value="{{ $taxYear }}">
                            <div class="row">
                                <div class="col-md-5"><select name="age_group" class="form-control" required><option value="below_65">Below 65</option><option value="65_to_74">65 to 74</option><option value="75_and_over">75 and Over</option></select></div>
                                <div class="col-md-4"><input type="number" step="0.01" name="threshold_amount" class="form-control" placeholder="Amount" required></div>
                                <div class="col-md-3"><button type="submit" class="btn button_master_add" style="padding:6px 14px;font-size:13px;"><i class="fa fa-plus"></i></button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleEdit(prefix, id) {
    document.getElementById(prefix + 'v-' + id).classList.toggle('hidden');
    document.getElementById(prefix + 'e-' + id).classList.toggle('active');
}
</script>
@endpush
