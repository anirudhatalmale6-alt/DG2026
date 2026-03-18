@extends('layouts.default')

@section('title', 'Tax Tables')

@push('styles')
<style>
.payroll-wrapper { max-width: none; margin: 0; padding: 30px; }
.edit-row { display: none; }
.edit-row.active { display: table-row; }
.view-row.hidden { display: none; }
.edit-row input, .edit-row select { height: 34px; border: 2px solid #17A2B8; border-radius: 6px; padding: 0 8px; font-size: 13px; }

/* Tax table */
.tax-table thead th {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #5b676d;
    padding: 10px 8px;
    border-bottom: 2px solid #dee2e6;
    background: #f8f9fa;
    white-space: nowrap;
}
.tax-table tbody td {
    vertical-align: middle;
    padding: 10px 8px;
    border-bottom: 1px solid #eee;
}
.tax-table tbody tr.view-row:hover {
    background: #f8fbfd;
}
.tax-field {
    font-size: 14px;
    font-weight: 600;
    color: #5b676d;
}
.tax-actions {
    white-space: nowrap;
    text-align: center;
}
.tax-actions .btn {
    margin-left: 6px;
}

/* Flex edit form rows */
.edit-flex-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 10px;
    margin-bottom: 6px;
}
.edit-flex-row .edit-field {
    flex: 1 1 0;
    min-width: 0;
}
.edit-flex-row .edit-field label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #5b676d;
    margin-bottom: 3px;
}
.edit-flex-row .edit-field input,
.edit-flex-row .edit-field select {
    width: 100%;
    box-sizing: border-box;
}
.edit-flex-row .edit-field-btn {
    flex: 0 0 auto;
    min-width: 90px;
    display: flex;
    align-items: flex-end;
}
.edit-flex-row .edit-field-btn .btn {
    width: 100%;
}
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

    <!-- Tax Year Selector -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-calendar-alt"></i> TAX YEAR</h4></div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cimspayroll.tax-tables.index') }}">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <div class="mb-3"><label class="form-label">Select Tax Year</label><select name="tax_year" class="sd_drop_class" onchange="this.form.submit()">@foreach($availableYears as $y)<option value="{{ $y }}" {{ $taxYear == $y ? 'selected' : '' }}>{{ $y }}</option>@endforeach</select></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Brackets -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-layer-group"></i> TAX BRACKETS — {{ $taxYear }}</h4></div>
                <div class="card-body" style="padding:0;">
                    <div class="table-responsive">
                        <table class="table tax-table mb-0">
                            <thead>
                                <tr>
                                    <th>Bracket</th>
                                    <th class="cims_money_format">Min (R)</th>
                                    <th class="cims_money_format">Max (R)</th>
                                    <th class="cims_money_format">Rate (%)</th>
                                    <th class="cims_money_format">Base Tax (R)</th>
                                    <th style="text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brackets as $i => $b)
                                <tr class="view-row" id="bv-{{ $b->id }}">
                                    <td><span class="tax-field">{{ $i + 1 }}</span></td>
                                    <td class="cims_money_format"><span class="tax-field">{{ cims_money_format($b->min_amount) }}</span></td>
                                    <td class="cims_money_format"><span class="tax-field">{{ cims_money_format($b->max_amount) }}</span></td>
                                    <td class="cims_money_format"><span class="tax-field">{{ number_format($b->rate, 2) }}%</span></td>
                                    <td class="cims_money_format"><span class="tax-field">{{ cims_money_format($b->base_tax) }}</span></td>
                                    <td class="tax-actions">
                                        <button type="button" class="btn button_master_edit" onclick="toggleEdit('b',{{ $b->id }})"><i class="fa fa-edit"></i> Edit</button>
                                        <form method="POST" action="{{ route('cimspayroll.tax-tables.bracket.destroy', $b->id) }}" id="delete-bracket-{{ $b->id }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn button_master_delete" onclick="confirmDelete('delete-bracket-{{ $b->id }}', 'Bracket {{ $i + 1 }}')"><i class="fa fa-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="edit-row" id="be-{{ $b->id }}">
                                    <td colspan="6" style="padding:12px;background:#f0faff;">
                                        <form method="POST" action="{{ route('cimspayroll.tax-tables.bracket.update', $b->id) }}">
                                            @csrf @method('PUT')
                                            <div class="edit-flex-row" style="align-items:flex-end;">
                                                <div class="edit-field"><label>Min (R)</label><input type="number" step="0.01" name="min_amount" value="{{ $b->min_amount }}" class="form-control" required></div>
                                                <div class="edit-field"><label>Max (R)</label><input type="number" step="0.01" name="max_amount" value="{{ $b->max_amount }}" class="form-control" required></div>
                                                <div class="edit-field"><label>Rate (%)</label><input type="number" step="0.01" name="rate" value="{{ $b->rate }}" class="form-control" required></div>
                                                <div class="edit-field"><label>Base Tax (R)</label><input type="number" step="0.01" name="base_tax" value="{{ $b->base_tax }}" class="form-control" required></div>
                                                <div class="edit-field-btn"><button type="submit" class="btn button_master_save"><i class="fa fa-check"></i> Save</button></div>
                                                <div class="edit-field-btn"><button type="button" class="btn button_master_cancel" onclick="toggleEdit('b',{{ $b->id }})"><i class="fa fa-times"></i> Cancel</button></div>
                                            </div>
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
                            <div class="row align-items-end">
                                <div class="col-md-2"><div class="mb-3"><label class="form-label">Min (R)</label><input type="number" step="0.01" name="min_amount" class="form-control" required></div></div>
                                <div class="col-md-2"><div class="mb-3"><label class="form-label">Max (R)</label><input type="number" step="0.01" name="max_amount" class="form-control" required></div></div>
                                <div class="col-md-2"><div class="mb-3"><label class="form-label">Rate (%)</label><input type="number" step="0.01" name="rate" class="form-control" required></div></div>
                                <div class="col-md-2"><div class="mb-3"><label class="form-label">Base Tax (R)</label><input type="number" step="0.01" name="base_tax" class="form-control" value="0" required></div></div>
                                <div class="col-md-2"><div class="mb-3"><button type="submit" class="btn button_master_add w-100"><i class="fa fa-plus"></i> Add Bracket</button></div></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Rebates -->
        <div class="col-md-6">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-percentage"></i> TAX REBATES — {{ $taxYear }}</h4></div>
                <div class="card-body" style="padding:0;">
                    <div class="table-responsive">
                        <table class="table tax-table mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th class="cims_money_format">Amount (R)</th>
                                    <th>Age</th>
                                    <th style="text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rebates as $r)
                                <tr class="view-row" id="rv-{{ $r->id }}">
                                    <td><span class="tax-field" style="font-weight:700;">{{ ucfirst($r->rebate_type) }}</span></td>
                                    <td class="cims_money_format"><span class="tax-field">{{ cims_money_format($r->amount) }}</span></td>
                                    <td><span class="tax-field">{{ $r->age_threshold ? $r->age_threshold . '+' : '—' }}</span></td>
                                    <td class="tax-actions"><button type="button" class="btn button_master_edit" onclick="toggleEdit('r',{{ $r->id }})"><i class="fa fa-edit"></i> Edit</button></td>
                                </tr>
                                <tr class="edit-row" id="re-{{ $r->id }}">
                                    <td colspan="4" style="padding:12px;background:#f0faff;">
                                        <form method="POST" action="{{ route('cimspayroll.tax-tables.rebate.update', $r->id) }}">
                                            @csrf @method('PUT')
                                            <div class="edit-flex-row" style="align-items:flex-end;">
                                                <div class="edit-field"><label>Amount (R)</label><input type="number" step="0.01" name="amount" value="{{ $r->amount }}" class="form-control" required></div>
                                                <div class="edit-field"><label>Age Threshold</label><input type="number" name="age_threshold" value="{{ $r->age_threshold }}" class="form-control" placeholder="e.g. 65"></div>
                                                <div class="edit-field-btn"><button type="submit" class="btn button_master_save"><i class="fa fa-check"></i> Save</button></div>
                                                <div class="edit-field-btn"><button type="button" class="btn button_master_cancel" onclick="toggleEdit('r',{{ $r->id }})"><i class="fa fa-times"></i> Cancel</button></div>
                                            </div>
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
                            <div class="row align-items-end">
                                <div class="col-md-4"><div class="mb-3"><label class="form-label">Type</label><select name="rebate_type" class="sd_drop_class" required><option value="primary">Primary</option><option value="secondary">Secondary</option><option value="tertiary">Tertiary</option></select></div></div>
                                <div class="col-md-3"><div class="mb-3"><label class="form-label">Amount (R)</label><input type="number" step="0.01" name="amount" class="form-control" required></div></div>
                                <div class="col-md-2"><div class="mb-3"><label class="form-label">Age</label><input type="number" name="age_threshold" class="form-control" placeholder="e.g. 65"></div></div>
                                <div class="col-md-3"><div class="mb-3"><button type="submit" class="btn button_master_add w-100"><i class="fa fa-plus"></i> Add Rebate</button></div></div>
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
                        <table class="table tax-table mb-0">
                            <thead>
                                <tr>
                                    <th>Age Group</th>
                                    <th class="cims_money_format">Threshold (R)</th>
                                    <th style="text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($thresholds as $t)
                                <tr class="view-row" id="tv-{{ $t->id }}">
                                    <td><span class="tax-field" style="font-weight:700;">{{ str_replace('_', ' ', ucfirst($t->age_group)) }}</span></td>
                                    <td class="cims_money_format"><span class="tax-field">{{ cims_money_format($t->threshold_amount) }}</span></td>
                                    <td class="tax-actions"><button type="button" class="btn button_master_edit" onclick="toggleEdit('t',{{ $t->id }})"><i class="fa fa-edit"></i> Edit</button></td>
                                </tr>
                                <tr class="edit-row" id="te-{{ $t->id }}">
                                    <td colspan="3" style="padding:12px;background:#f0faff;">
                                        <form method="POST" action="{{ route('cimspayroll.tax-tables.threshold.update', $t->id) }}">
                                            @csrf @method('PUT')
                                            <div class="edit-flex-row" style="align-items:flex-end;">
                                                <div class="edit-field"><label>Threshold (R)</label><input type="number" step="0.01" name="threshold_amount" value="{{ $t->threshold_amount }}" class="form-control" required></div>
                                                <div class="edit-field-btn"><button type="submit" class="btn button_master_save"><i class="fa fa-check"></i> Save</button></div>
                                                <div class="edit-field-btn"><button type="button" class="btn button_master_cancel" onclick="toggleEdit('t',{{ $t->id }})"><i class="fa fa-times"></i> Cancel</button></div>
                                            </div>
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
                            <div class="row align-items-end">
                                <div class="col-md-5"><div class="mb-3"><label class="form-label">Age Group</label><select name="age_group" class="sd_drop_class" required><option value="below_65">Below 65</option><option value="65_to_74">65 to 74</option><option value="75_and_over">75 and Over</option></select></div></div>
                                <div class="col-md-4"><div class="mb-3"><label class="form-label">Threshold (R)</label><input type="number" step="0.01" name="threshold_amount" class="form-control" required></div></div>
                                <div class="col-md-3"><div class="mb-3"><button type="submit" class="btn button_master_add w-100"><i class="fa fa-plus"></i> Add</button></div></div>
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
$(document).ready(function() { $('.sd_drop_class').selectpicker(); });

function toggleEdit(prefix, id) {
    document.getElementById(prefix + 'v-' + id).classList.toggle('hidden');
    document.getElementById(prefix + 'e-' + id).classList.toggle('active');
}

function confirmDelete(formId, name) {
    CIMSAlert.confirmDelete(formId, name);
}

document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
    CIMSAlert.success('{{ session("swal_action", "saved") }}', '{{ session("swal_name", "") }}');
    @endif

    @if(session('error'))
    CIMSAlert.error('{!! session("error") !!}');
    @endif
});
</script>
@endpush
