@extends('layouts.default')

@section('title', 'Payroll Processing')

@push('styles')
<style>
/* ============================================================
   PAYROLL PROCESSING - Unified Employee Processing Screen
   SmartWeigh / SmartDash Theme
   ============================================================ */

/* === BREADCRUMB MASTER (embedded from cims_master_css) === */
.breadcrumb_master {
    background: linear-gradient(135deg, #ffffff 0%, #f0fafb 100%);
    border-radius: 12px; margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-left: 5px solid #009688;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    transition: box-shadow 0.3s ease, transform 0.3s ease; overflow: hidden;
}
.breadcrumb_master:hover { box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12); transform: translateY(-1px); }
.bm_header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 15px; padding: 22px 28px 18px 28px;
}
.bm_header .bm_title_area { display: flex; align-items: center; gap: 16px; }
.bm_header .bm_icon {
    width: 46px; height: 46px;
    background: linear-gradient(135deg, #009688 0%, #4DB6AC 100%);
    border-radius: 12px; display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fff; box-shadow: 0 3px 10px rgba(0, 150, 136, 0.3); flex-shrink: 0;
}
.bm_header .bm_title { font-size: 20px; font-weight: 700; color: #0d3d56; margin: 0; letter-spacing: 0.3px; line-height: 1.3; }
.bm_header .bm_subtitle { font-size: 13px; color: #888; margin: 3px 0 0 0; font-weight: 400; }
.bm_header .bm_badge { font-size: 26px; font-weight: 800; color: #0d3d56; letter-spacing: 2px; text-transform: uppercase; text-align: right; }
.bm_controls { display: flex; align-items: center; padding: 16px 28px; border-top: 1px solid rgba(0, 150, 136, 0.1); }

/* === SMARTDASH BUTTON (embedded from cims_master_css) === */
[class*="button_master_"] {
    padding: 8px 30px !important; font-size: 18px !important; margin: 10px 10px !important;
    display: inline-flex !important; align-items: center; gap: 8px;
    border: none !important; color: #fff !important; border-radius: 50px !important;
    transition: all 0.3s ease !important; cursor: pointer; letter-spacing: 0.5px; font-weight: 600;
    text-decoration: none !important;
}
[class*="button_master_"]:hover { transform: translateY(-3px); color: #fff !important; }
[class*="button_master_"] i { transition: transform 0.3s ease; }
[class*="button_master_"]:active { transform: translateY(-1px); }
.button_master_refresh {
    background: linear-gradient(to right, #0d3d56 0%, #17A2B8 100%) !important;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.35);
}
.button_master_refresh:hover { background: linear-gradient(to right, #104A68 0%, #1FC8E3 100%) !important; box-shadow: 0 8px 25px rgba(23, 162, 184, 0.5); }
.button_master_refresh:disabled { opacity: 0.5 !important; cursor: not-allowed !important; transform: none !important; }
.button_master_refresh:disabled:hover { transform: none !important; box-shadow: 0 4px 15px rgba(23, 162, 184, 0.35) !important; }

/* === RESPONSIVE === */
@media (max-width: 992px) {
    .bm_header { flex-direction: column; text-align: center; padding: 18px 20px 14px 20px; }
    .bm_header .bm_title_area { flex-direction: column; }
    .bm_header .bm_title { font-size: 18px; }
    .bm_header .bm_badge { font-size: 20px; text-align: center; }
    .bm_controls { flex-direction: column; gap: 14px; padding: 14px 20px; }
}

/* Full-width with 50px padding all sides */
.payroll-wrapper { max-width: none; margin: 0; padding: 50px; }

/* Layout - Full width with sidebar */
.pp-container { display: flex; height: calc(100vh - 340px); overflow: hidden; background: #f4f6f9; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

/* Controls row inside breadcrumb_master */
.pp-controls-row {
    display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap;
}
.pp-control-group { display: flex; flex-direction: column; gap: 4px; }
.pp-control-group label {
    font-size: 11px; font-weight: 700; color: #009688;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.pp-control-group select {
    padding: 8px 14px; border: 1px solid #B2DFDB; border-radius: 8px;
    font-size: 13px; font-family: 'Poppins', sans-serif; color: #333;
    background: #fff; min-width: 220px; transition: all 0.2s;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.pp-control-group select:focus {
    border-color: #009688; box-shadow: 0 0 0 3px rgba(0,150,136,0.12); outline: none;
}
.pp-control-group select:disabled {
    background: #f0f0f0; color: #aaa; cursor: not-allowed;
}

/* Employee Sidebar */
.pp-sidebar {
    width: 260px; min-width: 260px; background: #fff;
    border-right: 1px solid #e0e0e0; display: flex; flex-direction: column;
    overflow: hidden;
}
.pp-sidebar-header {
    padding: 12px; background: #E0F2F1; border-bottom: 1px solid #B2DFDB;
}
.pp-sidebar-header h3 { margin: 0 0 8px 0; font-size: 14px; color: #004D40; font-weight: 700; }
.pp-sidebar-search {
    width: 100%; padding: 7px 10px; border: 1px solid #B2DFDB; border-radius: 6px;
    font-size: 12px; outline: none;
}
.pp-sidebar-search:focus { border-color: #009688; box-shadow: 0 0 0 2px rgba(0,150,136,0.15); }
.pp-sidebar-filters {
    padding: 6px 12px; background: #f8f9fa; border-bottom: 1px solid #e0e0e0;
    display: flex; gap: 4px;
}
.pp-filter-btn {
    padding: 3px 10px; font-size: 11px; border: 1px solid #B2DFDB; border-radius: 12px;
    background: #fff; color: #00796B; cursor: pointer; transition: all 0.2s;
}
.pp-filter-btn.active { background: #009688; color: #fff; border-color: #009688; }

.pp-employee-list { flex: 1; overflow-y: auto; }
.pp-emp-item {
    padding: 10px 12px; border-bottom: 1px solid #f0f0f0; cursor: pointer;
    transition: all 0.15s; display: flex; align-items: center; gap: 10px;
}
.pp-emp-item:hover { background: #E0F2F1; }
.pp-emp-item.active { background: #009688; color: #fff; }
.pp-emp-item.active .pp-emp-code { color: rgba(255,255,255,0.8); }
.pp-emp-item.active .pp-emp-title { color: rgba(255,255,255,0.7); }
.pp-emp-avatar {
    width: 36px; height: 36px; border-radius: 50%; background: #B2DFDB;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: #004D40; flex-shrink: 0;
}
.pp-emp-item.active .pp-emp-avatar { background: rgba(255,255,255,0.25); color: #fff; }
.pp-emp-info { flex: 1; min-width: 0; }
.pp-emp-name { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pp-emp-code { font-size: 11px; color: #888; }
.pp-emp-title { font-size: 10px; color: #aaa; }
.pp-emp-count {
    padding: 8px 12px; background: #f8f9fa; border-top: 1px solid #e0e0e0;
    font-size: 11px; color: #888; text-align: center;
}

/* Main Content Area */
.pp-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

/* Employee Header */
.pp-emp-header {
    padding: 12px 20px; background: #fff; border-bottom: 1px solid #e0e0e0;
    display: flex; align-items: center; gap: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.pp-emp-header-avatar {
    width: 48px; height: 48px; border-radius: 50%; background: #B2DFDB;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 18px; color: #004D40; border: 2px solid #009688;
}
.pp-emp-header-info h2 { margin: 0; font-size: 18px; color: #004D40; }
.pp-emp-header-info .pp-emp-meta { font-size: 12px; color: #888; margin-top: 2px; }
.pp-emp-header-actions { margin-left: auto; display: flex; gap: 8px; }

/* Tab Navigation */
.pp-tabs {
    display: flex; background: #fff; border-bottom: 2px solid #e0e0e0;
    padding: 0 10px; overflow-x: auto; flex-shrink: 0;
}
.pp-tab {
    padding: 10px 14px; font-size: 12px; font-weight: 600; color: #666;
    cursor: pointer; border-bottom: 3px solid transparent; white-space: nowrap;
    transition: all 0.2s; margin-bottom: -2px;
}
.pp-tab:hover { color: #009688; background: #f8fffe; }
.pp-tab.active { color: #009688; border-bottom-color: #009688; }

/* Tab Content */
.pp-tab-content { flex: 1; overflow-y: auto; padding: 20px; }
.pp-tab-pane { display: none; }
.pp-tab-pane.active { display: block; }

/* Form Styles */
.pp-card {
    background: #fff; border-radius: 8px; padding: 16px 20px; margin-bottom: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06); border: 1px solid #eee;
}
.pp-card-title {
    font-size: 14px; font-weight: 700; color: #004D40; margin: 0 0 12px 0;
    padding-bottom: 8px; border-bottom: 2px solid #E0F2F1;
}
.pp-form-row { display: flex; gap: 16px; margin-bottom: 10px; flex-wrap: wrap; }
.pp-form-group { flex: 1; min-width: 180px; }
.pp-form-group label { display: block; font-size: 11px; font-weight: 600; color: #00796B; margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.3px; }
.pp-form-group input, .pp-form-group select, .pp-form-group textarea {
    width: 100%; padding: 7px 10px; border: 1px solid #ddd; border-radius: 5px;
    font-size: 13px; transition: border-color 0.2s; background: #fff;
}
.pp-form-group input:focus, .pp-form-group select:focus {
    border-color: #009688; box-shadow: 0 0 0 2px rgba(0,150,136,0.12); outline: none;
}
.pp-form-group input[readonly] { background: #f5f5f5; color: #888; }
.pp-form-group .pp-computed { background: #E0F2F1; font-weight: 600; color: #004D40; }

/* Payslip Tab - 2x2 Grid Layout */
.pp-payslip-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.pp-payslip-section { background: #fff; border-radius: 8px; border: 1px solid #eee; overflow: hidden; }
.pp-payslip-section-header {
    padding: 8px 12px; font-weight: 700; font-size: 12px; text-transform: uppercase;
    display: flex; justify-content: space-between; align-items: center;
}
.pp-payslip-section.earnings .pp-payslip-section-header { background: #E8F5E9; color: #2E7D32; border-bottom: 2px solid #4CAF50; }
.pp-payslip-section.deductions .pp-payslip-section-header { background: #FFF3E0; color: #E65100; border-bottom: 2px solid #FF9800; }
.pp-payslip-section.contributions .pp-payslip-section-header { background: #E3F2FD; color: #1565C0; border-bottom: 2px solid #2196F3; }
.pp-payslip-section.fringe .pp-payslip-section-header { background: #F3E5F5; color: #7B1FA2; border-bottom: 2px solid #9C27B0; }

/* Payslip Items - Table-like layout with columns */
.pp-payslip-items { padding: 0; }
.pp-payslip-col-headers {
    display: grid; grid-template-columns: 1fr 70px 90px 100px 24px; gap: 4px;
    padding: 4px 10px; background: #f8f9fa; font-size: 10px; font-weight: 600;
    color: #888; text-transform: uppercase; border-bottom: 1px solid #eee;
}
.pp-payslip-col-headers span:nth-child(2),
.pp-payslip-col-headers span:nth-child(3),
.pp-payslip-col-headers span:nth-child(4) { text-align: right; }
.pp-payslip-item {
    display: grid; grid-template-columns: 1fr 70px 90px 100px 24px; gap: 4px;
    padding: 5px 10px; align-items: center; border-bottom: 1px solid #f5f5f5; font-size: 12px;
}
.pp-payslip-item:last-child { border-bottom: none; }
.pp-payslip-item .pp-item-name { font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pp-payslip-item .pp-field-input {
    width: 100%; text-align: right; border: 1px solid #ddd; border-radius: 3px;
    padding: 3px 5px; font-size: 12px; font-family: 'Courier New', monospace;
    background: #fafafa;
}
.pp-payslip-item .pp-field-input:focus { background: #fff; border-color: #009688; outline: none; }
.pp-payslip-item .pp-field-computed {
    width: 100%; text-align: right; padding: 3px 5px; font-size: 12px;
    font-family: 'Courier New', monospace; font-weight: 600; color: #004D40;
    background: #E0F2F1; border: 1px solid #B2DFDB; border-radius: 3px;
}
.pp-remove-item-btn { border: none; background: transparent; cursor: pointer; padding: 0; text-align: center; opacity: 0.3; transition: opacity 0.2s; }
.pp-remove-item-btn:hover { opacity: 1; }
.pp-remove-item-btn:hover i { color: #E91E63 !important; }
.pp-payslip-total {
    display: grid; grid-template-columns: 1fr 70px 90px 100px 24px; gap: 4px;
    padding: 8px 10px; font-weight: 700; font-size: 12px;
    border-top: 2px solid #eee; background: #fafafa;
}
.pp-payslip-total .pp-total-amount { text-align: right; font-family: 'Courier New', monospace; }
.pp-add-item-btn {
    width: 100%; padding: 5px; border: 1px dashed #B2DFDB; background: transparent;
    color: #009688; font-size: 11px; cursor: pointer; transition: all 0.2s;
}
.pp-add-item-btn:hover { background: #E0F2F1; }

/* Net Pay Box */
.pp-nett-pay {
    background: linear-gradient(135deg, #004D40 0%, #00796B 100%);
    border-radius: 8px; padding: 14px 20px; margin-top: 16px;
    display: flex; justify-content: space-between; align-items: center; color: #fff;
}
.pp-nett-pay-label { font-size: 13px; opacity: 0.85; text-transform: uppercase; letter-spacing: 1px; }
.pp-nett-pay-amount { font-size: 28px; font-weight: 700; font-family: 'Courier New', monospace; }

/* Medical Aid Grid */
.pp-med-grid { overflow-x: auto; }
.pp-med-grid table { min-width: 800px; }
.pp-med-grid th { background: #E0F2F1; color: #004D40; font-size: 11px; padding: 6px 8px; text-align: center; white-space: nowrap; }
.pp-med-grid td { padding: 4px 6px; text-align: center; }
.pp-med-grid td input { width: 80px; text-align: right; padding: 4px 6px; font-size: 12px; border: 1px solid #ddd; border-radius: 3px; }
.pp-med-grid .pp-med-label { text-align: left; font-size: 12px; color: #333; white-space: nowrap; padding-left: 12px; }
.pp-med-grid .pp-med-section { background: #f8f9fa; font-weight: 700; font-size: 11px; color: #00796B; text-transform: uppercase; }
.pp-med-grid .pp-med-total td { font-weight: 700; background: #f8f9fa; }

/* YTD Grid */
.pp-ytd-grid { overflow-x: auto; }
.pp-ytd-grid table { min-width: 1000px; }
.pp-ytd-grid th { background: #E0F2F1; color: #004D40; font-size: 10px; padding: 6px 4px; text-align: right; white-space: nowrap; }
.pp-ytd-grid th:first-child { text-align: left; }
.pp-ytd-grid td { padding: 4px 6px; font-size: 12px; font-family: 'Courier New', monospace; text-align: right; }
.pp-ytd-grid td:first-child { text-align: left; font-family: inherit; }
.pp-ytd-grid .pp-ytd-section td { background: #f8f9fa; font-weight: 700; font-family: inherit; font-size: 11px; color: #00796B; text-transform: uppercase; }
.pp-ytd-grid .pp-ytd-total td { font-weight: 700; border-top: 2px solid #009688; }

/* Leave Accordion */
.pp-leave-type { margin-bottom: 8px; border: 1px solid #eee; border-radius: 6px; overflow: hidden; }
.pp-leave-header {
    padding: 10px 14px; background: #f8f9fa; cursor: pointer;
    display: flex; justify-content: space-between; align-items: center;
    font-weight: 600; font-size: 13px; color: #333; transition: background 0.2s;
}
.pp-leave-header:hover { background: #E0F2F1; }
.pp-leave-header .pp-chevron { transition: transform 0.3s; font-size: 16px; color: #888; }
.pp-leave-header.open .pp-chevron { transform: rotate(90deg); }
.pp-leave-body { display: none; padding: 12px; border-top: 1px solid #eee; }
.pp-leave-body.open { display: block; }
.pp-leave-table { width: 100%; }
.pp-leave-table th { background: #E0F2F1; color: #004D40; font-size: 10px; padding: 5px 8px; text-align: right; }
.pp-leave-table th:first-child { text-align: left; }
.pp-leave-table td { padding: 5px 8px; font-size: 12px; text-align: right; border-bottom: 1px solid #f5f5f5; }
.pp-leave-table td:first-child { text-align: left; }
.pp-leave-table input { width: 80px; text-align: right; padding: 3px 6px; font-size: 12px; border: 1px solid #ddd; border-radius: 3px; }

/* Transactions Table */
.pp-trans-table { width: 100%; }
.pp-trans-table th { background: #E0F2F1; color: #004D40; font-size: 11px; padding: 6px 10px; text-align: left; }
.pp-trans-table th:nth-child(n+2) { text-align: right; }
.pp-trans-table td { padding: 6px 10px; font-size: 12px; border-bottom: 1px solid #f5f5f5; }
.pp-trans-table td:nth-child(n+2) { text-align: right; font-family: 'Courier New', monospace; }
.pp-trans-section td { background: #f8f9fa; font-weight: 700; font-size: 11px; color: #00796B; }

/* Action Buttons */
.pp-btn {
    padding: 7px 16px; border: none; border-radius: 5px; font-size: 12px;
    font-weight: 600; cursor: pointer; transition: all 0.2s; display: inline-flex;
    align-items: center; gap: 6px;
}
.pp-btn-save { background: #009688; color: #fff; }
.pp-btn-save:hover { background: #00796B; }
.pp-btn-undo { background: #f5f5f5; color: #666; border: 1px solid #ddd; }
.pp-btn-undo:hover { background: #eee; }
.pp-btn-generate { background: #004D40; color: #fff; }
.pp-btn-generate:hover { background: #00332e; }
.pp-btn-danger { background: #E91E63; color: #fff; }
.pp-btn-danger:hover { background: #C2185B; }
.pp-btn-sm { padding: 4px 10px; font-size: 11px; }

.pp-actions-bar {
    padding: 10px 20px; background: #fff; border-top: 1px solid #e0e0e0;
    display: flex; justify-content: flex-end; gap: 8px;
    box-shadow: 0 -1px 3px rgba(0,0,0,0.05);
}

/* Empty State */
.pp-empty { text-align: center; padding: 60px 20px; color: #aaa; }
.pp-empty i { font-size: 48px; margin-bottom: 16px; display: block; color: #B2DFDB; }
.pp-empty h3 { color: #888; margin: 0 0 8px 0; }
.pp-empty p { font-size: 13px; }

/* Loading Spinner */
.pp-loading { text-align: center; padding: 40px; color: #009688; }
.pp-loading i { font-size: 24px; animation: pp-spin 1s linear infinite; }
@keyframes pp-spin { to { transform: rotate(360deg); } }

/* Toast Notifications */
.pp-toast {
    position: fixed; bottom: 20px; right: 20px; padding: 12px 20px;
    border-radius: 8px; color: #fff; font-size: 13px; font-weight: 600;
    z-index: 9999; transform: translateY(100px); opacity: 0; transition: all 0.3s;
}
.pp-toast.show { transform: translateY(0); opacity: 1; }
.pp-toast.success { background: #009688; }
.pp-toast.error { background: #E91E63; }

/* Responsive */
@media (max-width: 900px) {
    .pp-container { flex-direction: column; }
    .pp-sidebar { width: 100%; min-width: 100%; max-height: 200px; }
    .pp-payslip-row { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<div class="container-fluid payroll-wrapper">

<!-- Breadcrumb Master: Header + Controls -->
<div class="breadcrumb_master">
    <!-- Row 1: Header with Icon & Badge -->
    <div class="bm_header">
        <div class="bm_title_area">
            <div class="bm_icon"><i class="fas fa-money-check-alt"></i></div>
            <div>
                <div class="bm_title">Payroll Processing</div>
                <div class="bm_subtitle">Process employee payroll, generate payslips</div>
            </div>
        </div>
        <div class="bm_badge">Payroll Processing</div>
    </div>

    <!-- Row 2: Controls - Company, Tax Year, Period, Load -->
    <div class="bm_controls">
        <div class="pp-controls-row">
            <div class="pp-control-group">
                <label><i class="fas fa-building"></i> Company</label>
                <select id="ppCompanySelect" onchange="ppOnCompanyChange()">
                    <option value="">-- Select Company --</option>
                    @foreach($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pp-control-group">
                <label><i class="fas fa-calendar-alt"></i> Tax Year</label>
                <select id="ppTaxYearSelect" onchange="ppOnTaxYearChange()" disabled>
                    <option value="">-- Select Tax Year --</option>
                </select>
            </div>
            <div class="pp-control-group">
                <label><i class="fas fa-clock"></i> Pay Period</label>
                <select id="ppPeriodSelect" disabled>
                    <option value="">-- Select Period --</option>
                </select>
            </div>
            <div class="pp-control-group">
                <label>&nbsp;</label>
                <button type="button" class="button_master_refresh" id="ppLoadBtn" onclick="ppLoadEmployees()" disabled style="opacity:0.5;">
                    <i class="fas fa-users"></i> Load Employees
                </button>
            </div>
        </div>
    </div>
</div>

<div class="pp-container">
    <!-- Employee Sidebar -->
    <div class="pp-sidebar">
        <div class="pp-sidebar-header">
            <h3><i class="fas fa-users"></i> Employees</h3>
            <input type="text" class="pp-sidebar-search" id="ppEmpSearch" placeholder="Search name or code..." oninput="ppFilterEmployees()">
        </div>
        <div class="pp-sidebar-filters">
            <button class="pp-filter-btn active" data-filter="active" onclick="ppSetFilter(this)">Active</button>
            <button class="pp-filter-btn" data-filter="all" onclick="ppSetFilter(this)">All</button>
            <button class="pp-filter-btn" data-filter="terminated" onclick="ppSetFilter(this)">Terminated</button>
        </div>
        <div class="pp-employee-list" id="ppEmployeeList">
            <div class="pp-empty" style="padding:30px 10px;">
                <i class="fas fa-building"></i>
                <p>Select company, tax year, and period<br>then click Load Employees</p>
            </div>
        </div>
        <div class="pp-emp-count" id="ppEmpCount">0 employees</div>
    </div>

    <!-- Main Content -->
    <div class="pp-main">
        <!-- Empty state when no employee selected -->
        <div id="ppEmptyState" class="pp-empty" style="margin-top:100px;">
            <i class="fas fa-user-tie"></i>
            <h3>Select an Employee</h3>
            <p>Choose a company and click on an employee to start processing</p>
        </div>

        <!-- Employee content (hidden until employee selected) -->
        <div id="ppEmployeeContent" style="display:none; flex:1; display:none; flex-direction:column; overflow:hidden;">
            <!-- Employee Header -->
            <div class="pp-emp-header">
                <div class="pp-emp-header-avatar" id="ppHeaderAvatar">SN</div>
                <div class="pp-emp-header-info">
                    <h2 id="ppHeaderName">Employee Name</h2>
                    <div class="pp-emp-meta" id="ppHeaderMeta">EMP001 | Manager | Salaried</div>
                </div>
                <div class="pp-emp-header-actions">
                    <button class="pp-btn pp-btn-generate" onclick="ppGeneratePayslip()"><i class="fas fa-file-pdf"></i> Generate Payslip</button>
                    <button class="pp-btn pp-btn-save" onclick="ppViewTaxCalc()"><i class="fas fa-calculator"></i> Tax Calc</button>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="pp-tabs">
                <div class="pp-tab active" data-tab="details" onclick="ppSwitchTab(this)">Details</div>
                <div class="pp-tab" data-tab="hours" onclick="ppSwitchTab(this)">Hours & Rates</div>
                <div class="pp-tab" data-tab="eti" onclick="ppSwitchTab(this)">ETI</div>
                <div class="pp-tab" data-tab="payslip" onclick="ppSwitchTab(this)">Payslip</div>
                <div class="pp-tab" data-tab="private-ra" onclick="ppSwitchTab(this)">Private RA</div>
                <div class="pp-tab" data-tab="medical-aid" onclick="ppSwitchTab(this)">Medical Aid</div>
                <div class="pp-tab" data-tab="transactions" onclick="ppSwitchTab(this)">Transactions</div>
                <div class="pp-tab" data-tab="ytd" onclick="ppSwitchTab(this)">YTD</div>
                <div class="pp-tab" data-tab="leave" onclick="ppSwitchTab(this)">Leave</div>
                <div class="pp-tab" data-tab="leave-history" onclick="ppSwitchTab(this)">Leave History</div>
            </div>

            <!-- Tab Content -->
            <div class="pp-tab-content">

                <!-- ============ TAB 1: DETAILS ============ -->
                <div class="pp-tab-pane active" id="tab-details">
                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-user"></i> Personal Details</h4>
                        <div class="pp-form-row">
                            <div class="pp-form-group" style="max-width:200px;">
                                <label>Employee Type</label>
                                <select id="det_employee_type" name="employee_type">
                                    <option value="person">Person</option>
                                    <option value="psp">Personal Service Provider</option>
                                </select>
                            </div>
                            <div class="pp-form-group" style="max-width:140px;">
                                <label>Employee Code</label>
                                <input type="text" id="det_employee_number" name="employee_number" readonly class="pp-computed">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group" style="max-width:80px;">
                                <label>Title</label>
                                <select id="det_title" name="title">
                                    <option value="">—</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Dr">Dr</option>
                                    <option value="Prof">Prof</option>
                                </select>
                            </div>
                            <div class="pp-form-group">
                                <label>First Name</label>
                                <input type="text" id="det_first_name" name="first_name">
                            </div>
                            <div class="pp-form-group">
                                <label>Second Name</label>
                                <input type="text" id="det_second_name" name="second_name">
                            </div>
                            <div class="pp-form-group" style="max-width:80px;">
                                <label>Initials</label>
                                <input type="text" id="det_initials" name="initials">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Surname</label>
                                <input type="text" id="det_last_name" name="last_name">
                            </div>
                            <div class="pp-form-group">
                                <label>Known As Name</label>
                                <input type="text" id="det_known_as" name="known_as">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>RSA ID Number</label>
                                <input type="text" id="det_id_number" name="id_number" maxlength="13">
                            </div>
                            <div class="pp-form-group">
                                <label>Date of Birth</label>
                                <input type="date" id="det_date_of_birth" name="date_of_birth" onchange="ppCalcAge()">
                            </div>
                            <div class="pp-form-group" style="max-width:140px;">
                                <label>Age</label>
                                <input type="text" id="det_age" readonly class="pp-computed">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Passport Number</label>
                                <input type="text" id="det_passport_number" name="passport_number">
                            </div>
                            <div class="pp-form-group">
                                <label>Passport Country</label>
                                <select id="det_passport_country" name="passport_country">
                                    <option value="">—</option>
                                    <option value="South Africa">South Africa</option>
                                    <option value="Zimbabwe">Zimbabwe</option>
                                    <option value="Mozambique">Mozambique</option>
                                    <option value="Malawi">Malawi</option>
                                    <option value="Lesotho">Lesotho</option>
                                    <option value="Eswatini">Eswatini</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group" style="max-width:120px;">
                                <label>Gender</label>
                                <select id="det_gender" name="gender">
                                    <option value="">—</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="pp-form-group">
                                <label>Phone</label>
                                <input type="text" id="det_phone" name="phone">
                            </div>
                            <div class="pp-form-group">
                                <label>Email</label>
                                <input type="email" id="det_email" name="email">
                            </div>
                        </div>
                    </div>

                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-map-marker-alt"></i> Address</h4>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Address Line 1</label>
                                <input type="text" id="det_address_line1" name="address_line1">
                            </div>
                            <div class="pp-form-group">
                                <label>Address Line 2</label>
                                <input type="text" id="det_address_line2" name="address_line2">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>City</label>
                                <input type="text" id="det_city" name="city">
                            </div>
                            <div class="pp-form-group">
                                <label>Province</label>
                                <select id="det_province" name="province">
                                    <option value="">—</option>
                                    <option value="Gauteng">Gauteng</option>
                                    <option value="KwaZulu-Natal">KwaZulu-Natal</option>
                                    <option value="Western Cape">Western Cape</option>
                                    <option value="Eastern Cape">Eastern Cape</option>
                                    <option value="Free State">Free State</option>
                                    <option value="Limpopo">Limpopo</option>
                                    <option value="Mpumalanga">Mpumalanga</option>
                                    <option value="North West">North West</option>
                                    <option value="Northern Cape">Northern Cape</option>
                                </select>
                            </div>
                            <div class="pp-form-group" style="max-width:120px;">
                                <label>Postal Code</label>
                                <input type="text" id="det_postal_code" name="postal_code" maxlength="5">
                            </div>
                        </div>
                    </div>

                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-briefcase"></i> Employment Details</h4>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Job Title</label>
                                <input type="text" id="det_job_title" name="job_title">
                            </div>
                            <div class="pp-form-group">
                                <label>Department</label>
                                <input type="text" id="det_department" name="department">
                            </div>
                            <div class="pp-form-group">
                                <label>Start Date</label>
                                <input type="date" id="det_start_date" name="start_date">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Status</label>
                                <select id="det_status" name="status">
                                    <option value="active">Active</option>
                                    <option value="terminated">Terminated</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <div class="pp-form-group">
                                <label>Tax Number</label>
                                <input type="text" id="det_tax_number" name="tax_number" placeholder="SARS tax number">
                            </div>
                            <div class="pp-form-group">
                                <label>Tax Status</label>
                                <select id="det_tax_status" name="tax_status">
                                    <option value="normal">Normal</option>
                                    <option value="directive">Directive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-university"></i> Banking Details</h4>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Bank Name</label>
                                <select id="det_bank_name" name="bank_name">
                                    <option value="">—</option>
                                    <option value="ABSA">ABSA</option>
                                    <option value="Capitec">Capitec</option>
                                    <option value="FNB">FNB</option>
                                    <option value="Nedbank">Nedbank</option>
                                    <option value="Standard Bank">Standard Bank</option>
                                    <option value="African Bank">African Bank</option>
                                    <option value="Discovery Bank">Discovery Bank</option>
                                    <option value="Investec">Investec</option>
                                    <option value="TymeBank">TymeBank</option>
                                </select>
                            </div>
                            <div class="pp-form-group">
                                <label>Branch Code</label>
                                <input type="text" id="det_bank_branch_code" name="bank_branch_code">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Account Number</label>
                                <input type="text" id="det_bank_account_number" name="bank_account_number">
                            </div>
                            <div class="pp-form-group" style="max-width:180px;">
                                <label>Account Type</label>
                                <select id="det_bank_account_type" name="bank_account_type">
                                    <option value="">—</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="savings">Savings</option>
                                    <option value="transmission">Transmission</option>
                                </select>
                            </div>
                            <div class="pp-form-group" style="max-width:180px;">
                                <label>Pay Method</label>
                                <select id="det_pay_method" name="pay_method">
                                    <option value="electronic">Electronic Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ TAB 2: HOURS & RATES ============ -->
                <div class="pp-tab-pane" id="tab-hours">
                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-clock"></i> Hours</h4>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Working Hours per Day</label>
                                <input type="number" id="hr_hours_per_day" name="working_hours_per_day" step="0.01" value="9.00" onchange="ppCalcRates()">
                            </div>
                            <div class="pp-form-group">
                                <label>Working Days per Week</label>
                                <input type="number" id="hr_days_per_week" name="working_days_per_week" step="0.01" value="5.00" onchange="ppCalcRates()">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Avg Working Hours per Month</label>
                                <input type="text" id="hr_hours_per_month" readonly class="pp-computed" value="195.00">
                            </div>
                            <div class="pp-form-group">
                                <label>Avg Working Days per Month</label>
                                <input type="text" id="hr_days_per_month" readonly class="pp-computed" value="21.67">
                            </div>
                        </div>
                    </div>

                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-money-bill-wave"></i> Rates</h4>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Pay Type</label>
                                <select id="hr_pay_type" name="pay_type" onchange="ppTogglePayType()">
                                    <option value="salaried">Salaried</option>
                                    <option value="hourly">Hourly</option>
                                </select>
                            </div>
                        </div>
                        <div class="pp-form-row" id="hrSalariedFields">
                            <div class="pp-form-group">
                                <label>Annual Salary</label>
                                <input type="number" id="hr_annual_salary" step="0.01" onchange="ppCalcFromAnnual()">
                            </div>
                            <div class="pp-form-group">
                                <label>Fixed Salary (Monthly)</label>
                                <input type="number" id="hr_basic_salary" name="basic_salary" step="0.01" onchange="ppCalcFromMonthly()">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label>Rate per Day</label>
                                <input type="text" id="hr_rate_per_day" readonly class="pp-computed">
                            </div>
                            <div class="pp-form-group">
                                <label>Rate per Hour</label>
                                <input type="number" id="hr_hourly_rate" name="hourly_rate" step="0.0001" onchange="ppCalcFromHourly()">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label style="font-size:11px; color:#666; text-transform:none;">
                                    <input type="checkbox" id="hr_must_capture_hours" name="must_capture_hours">
                                    This employee must not be paid unless hours or days worked are advised
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ TAB 3: ETI ============ -->
                <div class="pp-tab-pane" id="tab-eti">
                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-hand-holding-usd"></i> ETI Values</h4>
                        <p style="font-size:12px;color:#888;font-style:italic;margin-bottom:12px;">These values are used for the employment tax incentive calculation</p>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label style="font-size:11px; color:#666; text-transform:none;">
                                    <input type="checkbox" id="eti_prescribed_min_wage" name="eti_prescribed_min_wage">
                                    This employee is paid a prescribed minimum wage
                                </label>
                            </div>
                            <div class="pp-form-group">
                                <label style="font-size:11px; color:#666; text-transform:none;">
                                    <input type="checkbox" id="eti_national_min_wage" name="eti_national_min_wage">
                                    or is paid a national minimum wage
                                </label>
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group" style="max-width:280px;">
                                <label>Minimum Rate (R per hour)</label>
                                <input type="number" id="eti_min_rate" name="eti_min_rate" step="0.0001" value="0.0000">
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label style="font-size:11px; color:#666; text-transform:none;">
                                    <input type="checkbox" id="eti_fixed_hours" name="eti_fixed_hours">
                                    Average Working Hours per Month are fixed hours agreed to in a contract
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-info-circle"></i> Current Month Summary</h4>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label style="font-size:11px; color:#666; text-transform:none;">
                                    <input type="checkbox" id="eti_sez" name="eti_sez">
                                    Employed in a <strong>Special Economic Zone (SEZ)</strong>
                                </label>
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label style="font-size:11px; color:#666; text-transform:none;">
                                    <input type="checkbox" id="eti_connected" name="eti_connected">
                                    This employee is a <strong>connected person</strong>
                                </label>
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label style="font-size:11px; color:#666; text-transform:none;">
                                    <input type="checkbox" id="eti_domestic" name="eti_domestic">
                                    This employee is a <strong>domestic worker</strong>
                                </label>
                            </div>
                        </div>
                        <div class="pp-form-row">
                            <div class="pp-form-group">
                                <label style="font-size:11px; color:#666; text-transform:none;">
                                    <input type="checkbox" id="eti_labour_broker" name="eti_labour_broker">
                                    This employee is a <strong>labour broker</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ TAB 4: PAYSLIP ============ -->
                <div class="pp-tab-pane" id="tab-payslip">
                    <!-- Row 1: Earnings + Deductions -->
                    <div class="pp-payslip-row">
                        <div class="pp-payslip-section earnings">
                            <div class="pp-payslip-section-header">
                                <span>Earnings</span>
                                <button class="pp-btn pp-btn-sm pp-btn-save" onclick="ppAddPayslipItem('earnings')"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="pp-payslip-col-headers"><span>Description</span><span>Hours</span><span>Rate</span><span>Amount</span><span></span></div>
                            <div class="pp-payslip-items" id="ppEarningsItems"></div>
                            <div class="pp-payslip-total">
                                <span>Total</span><span></span><span></span>
                                <span class="pp-total-amount" id="ppEarningsTotal">0.00</span><span></span>
                            </div>
                        </div>

                        <div class="pp-payslip-section deductions">
                            <div class="pp-payslip-section-header">
                                <span>Deductions</span>
                                <button class="pp-btn pp-btn-sm" style="background:#FF9800;color:#fff;" onclick="ppAddPayslipItem('deductions')"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="pp-payslip-col-headers"><span>Description</span><span>Qty</span><span>Rate</span><span>Amount</span><span></span></div>
                            <div class="pp-payslip-items" id="ppDeductionsItems"></div>
                            <div class="pp-payslip-total">
                                <span>Total</span><span></span><span></span>
                                <span class="pp-total-amount" id="ppDeductionsTotal">0.00</span><span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Fringe Benefits + Company Contributions -->
                    <div class="pp-payslip-row">
                        <div class="pp-payslip-section fringe">
                            <div class="pp-payslip-section-header">
                                <span>Fringe Benefits</span>
                                <button class="pp-btn pp-btn-sm" style="background:#9C27B0;color:#fff;" onclick="ppAddPayslipItem('fringe')"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="pp-payslip-col-headers"><span>Description</span><span>Qty</span><span>Rate</span><span>Amount</span><span></span></div>
                            <div class="pp-payslip-items" id="ppFringeItems"></div>
                            <div class="pp-payslip-total">
                                <span>Total</span><span></span><span></span>
                                <span class="pp-total-amount" id="ppFringeTotal">0.00</span><span></span>
                            </div>
                        </div>

                        <div class="pp-payslip-section contributions">
                            <div class="pp-payslip-section-header">
                                <span>Company Contributions</span>
                                <button class="pp-btn pp-btn-sm" style="background:#2196F3;color:#fff;" onclick="ppAddPayslipItem('contributions')"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="pp-payslip-col-headers"><span>Description</span><span>Qty</span><span>Rate</span><span>Amount</span><span></span></div>
                            <div class="pp-payslip-items" id="ppContributionsItems"></div>
                            <div class="pp-payslip-total">
                                <span>Total</span><span></span><span></span>
                                <span class="pp-total-amount" id="ppContributionsTotal">0.00</span><span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Nett Pay -->
                    <div class="pp-nett-pay">
                        <span class="pp-nett-pay-label">Nett Pay</span>
                        <span class="pp-nett-pay-amount" id="ppNettPay">R 0.00</span>
                    </div>

                    <!-- Save Defaults -->
                    <div style="margin-top:16px; display:flex; justify-content:space-between; align-items:center;">
                        <span id="ppDefaultsStatus" style="font-size:12px; color:#888;"></span>
                        <button class="pp-btn pp-btn-save" onclick="ppSaveDefaults()" style="padding:8px 20px;">
                            <i class="fas fa-save"></i> Save as Employee Default
                        </button>
                    </div>
                </div>

                <!-- ============ TAB 5: PRIVATE RA ============ -->
                <div class="pp-tab-pane" id="tab-private-ra">
                    <div class="pp-card">
                        <h4 class="pp-card-title">
                            <i class="fas fa-piggy-bank"></i> Private Retirement Annuity
                            <button class="pp-btn pp-btn-sm pp-btn-save" style="float:right;" onclick="ppAddRA()"><i class="fas fa-plus"></i> Add</button>
                        </h4>
                        <p style="font-size:12px;color:#888;font-style:italic;">Private retirement annuities are paid directly by the employee to the insurance company and the employee will receive a tax benefit on the amount.</p>
                        <div id="ppRAItems">
                            <div class="pp-empty" style="padding:20px;">
                                <p>No private retirement annuities recorded</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ TAB 6: MEDICAL AID ============ -->
                <div class="pp-tab-pane" id="tab-medical-aid">
                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-medkit"></i> Medical Aid</h4>
                        <div class="pp-med-grid">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="text-align:left;min-width:180px;"></th>
                                        <th>Mar</th><th>Apr</th><th>May</th><th>Jun</th><th>Jul</th><th>Aug</th>
                                        <th>Sep</th><th>Oct</th><th>Nov</th><th>Dec</th><th>Jan</th><th>Feb</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="pp-med-label">Type of medical aid</td>
                                        @for($m=0; $m<12; $m++)
                                        <td><select style="width:80px;font-size:10px;padding:2px;" id="med_type_{{$m}}"><option value="">None</option><option value="medical_scheme">Medical Scheme</option></select></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td class="pp-med-label">Beneficiaries</td>
                                        @for($m=0; $m<12; $m++)
                                        <td><input type="number" id="med_beneficiaries_{{$m}}" value="0" min="0"></td>
                                        @endfor
                                    </tr>
                                    <tr class="pp-med-section"><td colspan="13">Private Medical Aid</td></tr>
                                    <tr>
                                        <td class="pp-med-label">Private contribution</td>
                                        @for($m=0; $m<12; $m++)
                                        <td><input type="number" id="med_priv_contrib_{{$m}}" value="0.00" step="0.01"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td class="pp-med-label">Private contribution adjustment</td>
                                        @for($m=0; $m<12; $m++)
                                        <td><input type="number" id="med_priv_adj_{{$m}}" value="0.00" step="0.01"></td>
                                        @endfor
                                    </tr>
                                    <tr class="pp-med-total">
                                        <td class="pp-med-label"><strong>Total private contribution</strong></td>
                                        @for($m=0; $m<12; $m++)
                                        <td id="med_priv_total_{{$m}}">0.00</td>
                                        @endfor
                                    </tr>
                                    <tr class="pp-med-section"><td colspan="13">Company Medical Aid</td></tr>
                                    <tr>
                                        <td class="pp-med-label">Employee contribution</td>
                                        @for($m=0; $m<12; $m++)
                                        <td><input type="number" id="med_emp_contrib_{{$m}}" value="0.00" step="0.01"></td>
                                        @endfor
                                    </tr>
                                    <tr>
                                        <td class="pp-med-label">Company contribution</td>
                                        @for($m=0; $m<12; $m++)
                                        <td><input type="number" id="med_comp_contrib_{{$m}}" value="0.00" step="0.01"></td>
                                        @endfor
                                    </tr>
                                    <tr class="pp-med-section"><td colspan="13">Medical Aid Tax Credits</td></tr>
                                    <tr>
                                        <td class="pp-med-label">Medical aid tax credit</td>
                                        @for($m=0; $m<12; $m++)
                                        <td id="med_tax_credit_{{$m}}">0.00</td>
                                        @endfor
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ============ TAB 7: TRANSACTIONS ============ -->
                <div class="pp-tab-pane" id="tab-transactions">
                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-exchange-alt"></i> Current Period Transactions</h4>
                        <table class="pp-trans-table">
                            <thead>
                                <tr><th>Description</th><th>Input Units</th><th>Input Amount</th><th>Final Amount</th></tr>
                            </thead>
                            <tbody id="ppTransBody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ============ TAB 8: YTD ============ -->
                <div class="pp-tab-pane" id="tab-ytd">
                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-calendar-check"></i> Year to Date figures up to {{ now()->format('F') }}</h4>
                        <div class="pp-ytd-grid">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="text-align:left;min-width:160px;">Description</th>
                                        <th>Mar</th><th>Apr</th><th>May</th><th>Jun</th><th>Jul</th><th>Aug</th>
                                        <th>Sep</th><th>Oct</th><th>Nov</th><th>Dec</th><th>Jan</th><th>Feb</th>
                                        <th style="background:#004D40;color:#fff;">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="ppYtdBody">
                                    <!-- Populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ============ TAB 9: LEAVE ============ -->
                <div class="pp-tab-pane" id="tab-leave">
                    <div class="pp-card">
                        <h4 class="pp-card-title">
                            <i class="fas fa-umbrella-beach"></i> Leave
                            <button class="pp-btn pp-btn-sm pp-btn-save" style="float:right;" onclick="ppAddLeaveTransaction()"><i class="fas fa-plus"></i> Add New Transaction</button>
                        </h4>
                        <div class="pp-form-row" style="margin-bottom:16px;">
                            <div class="pp-form-group" style="max-width:200px;">
                                <label>Leave Start Date</label>
                                <input type="month" id="lv_start_date">
                            </div>
                            <div class="pp-form-group" style="max-width:250px;">
                                <label>Leave Based On</label>
                                <div style="padding-top:6px;">
                                    <label style="text-transform:none;font-weight:normal;"><input type="radio" name="lv_basis" value="working" checked> Working Days</label>&nbsp;&nbsp;
                                    <label style="text-transform:none;font-weight:normal;"><input type="radio" name="lv_basis" value="calendar"> Calendar Days</label>
                                </div>
                            </div>
                        </div>
                        <div class="pp-form-row" style="margin-bottom:16px;">
                            <div class="pp-form-group">
                                <label>This Employee Works On</label>
                                <div style="padding-top:4px; display:flex; gap:12px; flex-wrap:wrap;">
                                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                    <label style="text-transform:none;font-weight:normal;font-size:12px;">
                                        <input type="checkbox" name="lv_workdays[]" value="{{ strtolower($day) }}" {{ in_array(strtolower($day), ['monday','tuesday','wednesday','thursday','friday']) ? 'checked' : '' }}>
                                        {{ $day }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="ppLeaveTypes">
                        @foreach(['Annual Leave','Sick Leave','Family Leave','Maternity Leave','Parental Leave','Adoption Leave','Commissioning Parental Leave','Study Leave'] as $lt)
                        <div class="pp-leave-type">
                            <div class="pp-leave-header" onclick="ppToggleLeave(this)">
                                <strong>{{ $lt }}</strong>
                                <span class="pp-chevron"><i class="fas fa-chevron-right"></i></span>
                            </div>
                            <div class="pp-leave-body">
                                <table class="pp-leave-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left;">Leave Rule</th>
                                            <th>Entitlement/Cycle</th>
                                            <th>Cycle Start</th>
                                            <th>Next Cycle</th>
                                            <th>Opening Bal</th>
                                            <th>Accrual</th>
                                            <th>Taken</th>
                                            <th>Closing Bal</th>
                                            <th>Planned</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ppLeave_{{ \Illuminate\Support\Str::slug($lt) }}">
                                        <tr><td colspan="9" style="text-align:center;color:#aaa;font-size:12px;">No leave rules configured</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- ============ TAB 10: LEAVE HISTORY ============ -->
                <div class="pp-tab-pane" id="tab-leave-history">
                    <div class="pp-card">
                        <h4 class="pp-card-title"><i class="fas fa-history"></i> Leave Transaction History</h4>
                        <table class="pp-trans-table">
                            <thead>
                                <tr>
                                    <th>Transaction Description</th>
                                    <th>Period Captured</th>
                                    <th>Leave Taken From</th>
                                    <th>Leave Taken To</th>
                                    <th>Units Calculated</th>
                                    <th>Units Applied</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ppLeaveHistoryBody">
                                <tr><td colspan="7" style="text-align:center;color:#aaa;padding:20px;">No leave transactions recorded</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Bottom Actions Bar -->
            <div class="pp-actions-bar">
                <button class="pp-btn pp-btn-undo" onclick="ppUndoChanges()"><i class="fas fa-undo"></i> Undo</button>
                <button class="pp-btn pp-btn-save" onclick="ppSaveCurrentTab()"><i class="fas fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="pp-toast" id="ppToast"></div>

</div><!-- /.container-fluid payroll-wrapper -->

@endsection

@push('scripts')
<script>
/* ============================================================
   PAYROLL PROCESSING - JavaScript
   ============================================================ */

let ppCurrentEmployee = null;
let ppEmployees = [];
let ppCurrentFilter = 'active';
let ppOriginalData = {};
let ppSelectedTaxYear = '';
let ppSelectedPeriod = '';

// ─── MONTHS for financial year (March to February) ───
const ppMonths = [
    { value: 3, label: 'March' }, { value: 4, label: 'April' }, { value: 5, label: 'May' },
    { value: 6, label: 'June' }, { value: 7, label: 'July' }, { value: 8, label: 'August' },
    { value: 9, label: 'September' }, { value: 10, label: 'October' }, { value: 11, label: 'November' },
    { value: 12, label: 'December' }, { value: 1, label: 'January' }, { value: 2, label: 'February' }
];

// ─── CASCADING CONTROLS ───
function ppOnCompanyChange() {
    const companyId = document.getElementById('ppCompanySelect').value;
    const taxYearSelect = document.getElementById('ppTaxYearSelect');
    const periodSelect = document.getElementById('ppPeriodSelect');
    const loadBtn = document.getElementById('ppLoadBtn');

    // Reset downstream
    periodSelect.innerHTML = '<option value="">-- Select Period --</option>';
    periodSelect.disabled = true;
    loadBtn.disabled = true;
    loadBtn.style.opacity = '0.5';

    // Reset employee sidebar
    document.getElementById('ppEmployeeList').innerHTML = '<div class="pp-empty" style="padding:30px 10px;"><i class="fas fa-building"></i><p>Select company, tax year, and period</p></div>';
    document.getElementById('ppEmpCount').textContent = '0 employees';
    document.getElementById('ppEmptyState').style.display = '';
    document.getElementById('ppEmployeeContent').style.display = 'none';
    ppCurrentEmployee = null;
    ppEmployees = [];

    if (!companyId) {
        taxYearSelect.innerHTML = '<option value="">-- Select Tax Year --</option>';
        taxYearSelect.disabled = true;
        return;
    }

    // Populate tax years (current + 1 previous + 1 next)
    const now = new Date();
    const currentTaxYear = now.getMonth() >= 2 ? now.getFullYear() + 1 : now.getFullYear(); // month is 0-based, so >=2 means March+
    taxYearSelect.innerHTML = '<option value="">-- Select Tax Year --</option>';
    for (let y = currentTaxYear + 1; y >= currentTaxYear - 2; y--) {
        const label = (y - 1) + ' / ' + y;
        const selected = y === currentTaxYear ? ' selected' : '';
        taxYearSelect.innerHTML += '<option value="' + y + '"' + selected + '>' + label + '</option>';
    }
    taxYearSelect.disabled = false;

    // Auto-trigger tax year change since we pre-selected current year
    ppOnTaxYearChange();
}

function ppOnTaxYearChange() {
    const taxYearSelect = document.getElementById('ppTaxYearSelect');
    const periodSelect = document.getElementById('ppPeriodSelect');
    const loadBtn = document.getElementById('ppLoadBtn');

    ppSelectedTaxYear = taxYearSelect.value;

    // Reset downstream
    loadBtn.disabled = true;
    loadBtn.style.opacity = '0.5';

    if (!ppSelectedTaxYear) {
        periodSelect.innerHTML = '<option value="">-- Select Period --</option>';
        periodSelect.disabled = true;
        return;
    }

    // Populate periods for this tax year (March YYYY-1 to February YYYY)
    const yr = parseInt(ppSelectedTaxYear);
    const startYear = yr - 1; // e.g. tax year 2027 starts March 2026
    periodSelect.innerHTML = '<option value="">-- Select Period --</option>';

    const now = new Date();
    let preselect = '';

    ppMonths.forEach(m => {
        const calYear = m.value >= 3 ? startYear : yr;
        const val = calYear + '-' + String(m.value).padStart(2, '0');
        const label = m.label + ' ' + calYear;

        // Pre-select current month if it matches
        if (m.value === (now.getMonth() + 1) && calYear === now.getFullYear()) {
            preselect = val;
        }

        periodSelect.innerHTML += '<option value="' + val + '">' + label + '</option>';
    });

    periodSelect.disabled = false;

    // Pre-select current period if available
    if (preselect) {
        periodSelect.value = preselect;
    }

    ppOnPeriodChange();
    periodSelect.onchange = ppOnPeriodChange;
}

function ppOnPeriodChange() {
    const periodSelect = document.getElementById('ppPeriodSelect');
    const loadBtn = document.getElementById('ppLoadBtn');
    ppSelectedPeriod = periodSelect.value;

    if (ppSelectedPeriod && document.getElementById('ppCompanySelect').value && document.getElementById('ppTaxYearSelect').value) {
        loadBtn.disabled = false;
        loadBtn.style.opacity = '1';
    } else {
        loadBtn.disabled = true;
        loadBtn.style.opacity = '0.5';
    }
}

// ─── LOAD EMPLOYEES ───
function ppLoadEmployees() {
    const companyId = document.getElementById('ppCompanySelect').value;
    if (!companyId || !ppSelectedPeriod) {
        ppShowToast('Please select Company, Tax Year, and Period first', 'error');
        return;
    }

    document.getElementById('ppEmployeeList').innerHTML = '<div class="pp-loading"><i class="fas fa-spinner"></i></div>';

    fetch('{{ url("cims/payroll/processing/employees") }}?company_id=' + companyId + '&period=' + ppSelectedPeriod + '&tax_year=' + ppSelectedTaxYear)
        .then(r => r.json())
        .then(data => {
            ppEmployees = data.employees || [];
            ppRenderEmployees();
            if (ppEmployees.length > 0) {
                ppShowToast('Loaded ' + ppEmployees.length + ' employee' + (ppEmployees.length !== 1 ? 's' : ''), 'success');
            }
        })
        .catch(() => {
            document.getElementById('ppEmployeeList').innerHTML = '<div class="pp-empty" style="padding:20px;"><p style="color:#E91E63;">Error loading employees</p></div>';
        });
}

function ppRenderEmployees() {
    const search = (document.getElementById('ppEmpSearch').value || '').toLowerCase();
    let filtered = ppEmployees.filter(e => {
        if (ppCurrentFilter === 'active' && e.status !== 'active') return false;
        if (ppCurrentFilter === 'terminated' && e.status !== 'terminated') return false;
        if (search && !e.full_name.toLowerCase().includes(search) && !e.employee_number.toLowerCase().includes(search)) return false;
        return true;
    });

    const list = document.getElementById('ppEmployeeList');
    if (filtered.length === 0) {
        list.innerHTML = '<div class="pp-empty" style="padding:20px;"><p>No employees found</p></div>';
    } else {
        list.innerHTML = filtered.map(e => {
            const initials = (e.first_name[0] || '') + (e.last_name[0] || '');
            const active = ppCurrentEmployee && ppCurrentEmployee.id === e.id ? ' active' : '';
            return `<div class="pp-emp-item${active}" onclick="ppSelectEmployee(${e.id})" data-id="${e.id}">
                <div class="pp-emp-avatar">${initials.toUpperCase()}</div>
                <div class="pp-emp-info">
                    <div class="pp-emp-name">${e.full_name}</div>
                    <div class="pp-emp-code">${e.employee_number}</div>
                    <div class="pp-emp-title">${e.job_title || ''}</div>
                </div>
            </div>`;
        }).join('');
    }
    document.getElementById('ppEmpCount').textContent = filtered.length + ' employee' + (filtered.length !== 1 ? 's' : '');
}

function ppFilterEmployees() { ppRenderEmployees(); }
function ppSetFilter(btn) {
    document.querySelectorAll('.pp-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    ppCurrentFilter = btn.dataset.filter;
    ppRenderEmployees();
}

// ─── SELECT EMPLOYEE ───
function ppSelectEmployee(id) {
    document.getElementById('ppEmployeeList').querySelectorAll('.pp-emp-item').forEach(el => {
        el.classList.toggle('active', parseInt(el.dataset.id) === id);
    });

    fetch('{{ url("cims/payroll/processing/employee") }}/' + id)
        .then(r => r.json())
        .then(data => {
            ppCurrentEmployee = data.employee;
            ppPopulateAllTabs(data);
            document.getElementById('ppEmptyState').style.display = 'none';
            const content = document.getElementById('ppEmployeeContent');
            content.style.display = 'flex';
            content.style.flexDirection = 'column';

            // Update header
            const initials = (ppCurrentEmployee.first_name[0] || '') + (ppCurrentEmployee.last_name[0] || '');
            document.getElementById('ppHeaderAvatar').textContent = initials.toUpperCase();
            document.getElementById('ppHeaderName').textContent = ppCurrentEmployee.first_name + ' ' + ppCurrentEmployee.last_name;
            document.getElementById('ppHeaderMeta').textContent = ppCurrentEmployee.employee_number + ' | ' + (ppCurrentEmployee.job_title || 'No title') + ' | ' + (ppCurrentEmployee.pay_type === 'salaried' ? 'Salaried' : 'Hourly');
        })
        .catch(err => {
            ppShowToast('Error loading employee data', 'error');
            console.error(err);
        });
}

// ─── POPULATE TABS ───
function ppPopulateAllTabs(data) {
    const e = data.employee;

    // Details tab
    ppSetVal('det_employee_type', e.employee_type || 'person');
    ppSetVal('det_employee_number', e.employee_number);
    ppSetVal('det_title', e.title || '');
    ppSetVal('det_first_name', e.first_name);
    ppSetVal('det_second_name', e.second_name || '');
    ppSetVal('det_initials', e.initials || '');
    ppSetVal('det_last_name', e.last_name);
    ppSetVal('det_known_as', e.known_as || '');
    ppSetVal('det_id_number', e.id_number || '');
    ppSetVal('det_date_of_birth', e.date_of_birth || '');
    ppCalcAge();
    ppSetVal('det_passport_number', e.passport_number || '');
    ppSetVal('det_passport_country', e.passport_country || '');
    ppSetVal('det_gender', e.gender || '');
    ppSetVal('det_phone', e.phone || '');
    ppSetVal('det_email', e.email || '');
    ppSetVal('det_address_line1', e.address_line1 || e.address || '');
    ppSetVal('det_address_line2', e.address_line2 || '');
    ppSetVal('det_city', e.city || '');
    ppSetVal('det_province', e.province || '');
    ppSetVal('det_postal_code', e.postal_code || '');
    ppSetVal('det_job_title', e.job_title || '');
    ppSetVal('det_department', e.department || '');
    ppSetVal('det_start_date', e.start_date || '');
    ppSetVal('det_status', e.status);
    ppSetVal('det_tax_number', e.tax_number || '');
    ppSetVal('det_tax_status', e.tax_status || 'normal');
    ppSetVal('det_bank_name', e.bank_name || '');
    ppSetVal('det_bank_branch_code', e.bank_branch_code || '');
    ppSetVal('det_bank_account_number', e.bank_account_number || '');
    ppSetVal('det_bank_account_type', e.bank_account_type || '');
    ppSetVal('det_pay_method', e.pay_method || 'electronic');

    // Hours & Rates tab
    ppSetVal('hr_hours_per_day', e.working_hours_per_day || 9);
    ppSetVal('hr_days_per_week', e.working_days_per_week || 5);
    ppSetVal('hr_pay_type', e.pay_type || 'salaried');
    ppSetVal('hr_basic_salary', e.basic_salary || 0);
    ppSetVal('hr_hourly_rate', e.hourly_rate || 0);
    ppSetVal('hr_annual_salary', (parseFloat(e.basic_salary) || 0) * 12);
    ppCalcRates();
    ppTogglePayType();
    if (e.must_capture_hours) document.getElementById('hr_must_capture_hours').checked = true;

    // ETI tab
    if (e.eti_prescribed_min_wage) document.getElementById('eti_prescribed_min_wage').checked = true;
    if (e.eti_national_min_wage) document.getElementById('eti_national_min_wage').checked = true;
    ppSetVal('eti_min_rate', e.eti_min_rate || 0);
    if (e.eti_fixed_hours) document.getElementById('eti_fixed_hours').checked = true;
    if (e.eti_sez) document.getElementById('eti_sez').checked = true;
    if (e.eti_connected) document.getElementById('eti_connected').checked = true;
    if (e.eti_domestic) document.getElementById('eti_domestic').checked = true;
    if (e.eti_labour_broker) document.getElementById('eti_labour_broker').checked = true;

    // Payslip tab
    ppPopulatePayslip(data.payslip || {});

    // Transactions tab
    ppPopulateTransactions(data.transactions || []);

    // YTD tab
    ppPopulateYTD(data.ytd || {});

    // Leave tab
    ppPopulateLeave(data.leave || {});

    // Leave History tab
    ppPopulateLeaveHistory(data.leave_history || []);
}

// ─── PAYSLIP ───
let ppPayslipData = { earnings: [], deductions: [], contributions: [], fringe: [] };
const ppContainerMap = { earnings: 'ppEarningsItems', deductions: 'ppDeductionsItems', contributions: 'ppContributionsItems', fringe: 'ppFringeItems' };

function ppPopulatePayslip(ps) {
    ppPayslipData.earnings = (ps.earnings || []).map(e => ({
        name: e.name, hours: parseFloat(e.hours) || 0, rate: parseFloat(e.rate) || parseFloat(e.amount) || 0,
        amount: parseFloat(e.amount) || 0
    }));
    ppPayslipData.deductions = (ps.deductions || []).map(e => ({
        name: e.name, hours: parseFloat(e.hours) || 1, rate: parseFloat(e.rate) || parseFloat(e.amount) || 0,
        amount: parseFloat(e.amount) || 0
    }));
    ppPayslipData.contributions = (ps.contributions || []).map(e => ({
        name: e.name, hours: parseFloat(e.hours) || 1, rate: parseFloat(e.rate) || parseFloat(e.amount) || 0,
        amount: parseFloat(e.amount) || 0
    }));
    ppPayslipData.fringe = (ps.fringe || []).map(e => ({
        name: e.name, hours: parseFloat(e.hours) || 1, rate: parseFloat(e.rate) || parseFloat(e.amount) || 0,
        amount: parseFloat(e.amount) || 0
    }));

    Object.keys(ppContainerMap).forEach(s => ppRenderPayslipSection(ppContainerMap[s], ppPayslipData[s], s));
    ppCalcPayslipTotals();

    // Show defaults status
    const statusEl = document.getElementById('ppDefaultsStatus');
    if (statusEl) {
        if (ps.has_saved_defaults) {
            statusEl.innerHTML = '<i class="fas fa-check-circle" style="color:#4CAF50;"></i> Loaded from saved employee defaults';
        } else {
            statusEl.innerHTML = '<i class="fas fa-info-circle" style="color:#FF9800;"></i> Using global defaults — click "Save as Employee Default" to remember these settings';
        }
    }
}

function ppRenderPayslipSection(containerId, items, section) {
    const container = document.getElementById(containerId);
    if (!items || items.length === 0) {
        container.innerHTML = '<div style="padding:12px;color:#aaa;font-size:12px;text-align:center;">No items — click + to add</div>';
        return;
    }
    container.innerHTML = items.map((item, i) => {
        const hrs = parseFloat(item.hours) || 0;
        const rate = parseFloat(item.rate) || 0;
        const amt = hrs > 0 && rate > 0 ? (hrs * rate) : parseFloat(item.amount) || 0;
        return `
        <div class="pp-payslip-item">
            <span class="pp-item-name" title="${item.name}">${item.name}</span>
            <input type="number" step="0.01" min="0" class="pp-field-input" value="${hrs.toFixed(2)}"
                onchange="ppUpdateItem('${section}',${i},'hours',this.value)">
            <input type="number" step="0.01" min="0" class="pp-field-input" value="${rate.toFixed(2)}"
                onchange="ppUpdateItem('${section}',${i},'rate',this.value)">
            <div class="pp-field-computed">${amt.toFixed(2)}</div>
            <button class="pp-remove-item-btn" onclick="ppRemovePayslipItem('${section}',${i})" title="Remove">
                <i class="fas fa-times" style="color:#ccc;font-size:10px;"></i>
            </button>
        </div>`;
    }).join('');
}

function ppUpdateItem(section, index, field, value) {
    const val = parseFloat(value) || 0;
    ppPayslipData[section][index][field] = val;
    // Recalculate amount = hours × rate
    const item = ppPayslipData[section][index];
    const hrs = parseFloat(item.hours) || 0;
    const rate = parseFloat(item.rate) || 0;
    item.amount = hrs > 0 && rate > 0 ? parseFloat((hrs * rate).toFixed(2)) : (field === 'rate' ? val : item.amount);
    // Re-render to update computed field
    ppRenderPayslipSection(ppContainerMap[section], ppPayslipData[section], section);
    ppCalcPayslipTotals();
}

function ppRemovePayslipItem(section, index) {
    ppPayslipData[section].splice(index, 1);
    ppRenderPayslipSection(ppContainerMap[section], ppPayslipData[section], section);
    ppCalcPayslipTotals();
}

function ppCalcPayslipTotals() {
    let earnings = 0, deductions = 0, contributions = 0, fringe = 0;
    ppPayslipData.earnings.forEach(i => { const h=parseFloat(i.hours)||0, r=parseFloat(i.rate)||0; earnings += (h>0&&r>0) ? h*r : parseFloat(i.amount)||0; });
    ppPayslipData.deductions.forEach(i => { const h=parseFloat(i.hours)||0, r=parseFloat(i.rate)||0; deductions += (h>0&&r>0) ? h*r : parseFloat(i.amount)||0; });
    ppPayslipData.contributions.forEach(i => { const h=parseFloat(i.hours)||0, r=parseFloat(i.rate)||0; contributions += (h>0&&r>0) ? h*r : parseFloat(i.amount)||0; });
    ppPayslipData.fringe.forEach(i => { const h=parseFloat(i.hours)||0, r=parseFloat(i.rate)||0; fringe += (h>0&&r>0) ? h*r : parseFloat(i.amount)||0; });

    document.getElementById('ppEarningsTotal').textContent = formatR(earnings);
    document.getElementById('ppDeductionsTotal').textContent = formatR(deductions);
    document.getElementById('ppContributionsTotal').textContent = formatR(contributions);
    document.getElementById('ppFringeTotal').textContent = formatR(fringe);
    document.getElementById('ppNettPay').textContent = 'R ' + formatR(earnings - deductions);
}

// ─── TRANSACTIONS ───
function ppPopulateTransactions(trans) {
    const body = document.getElementById('ppTransBody');
    if (!trans || trans.length === 0) {
        body.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#aaa;padding:20px;">No transactions for current period</td></tr>';
        return;
    }
    let html = '';
    let currentSection = '';
    trans.forEach(t => {
        if (t.section && t.section !== currentSection) {
            currentSection = t.section;
            html += `<tr class="pp-trans-section"><td colspan="4">${t.section}</td></tr>`;
        }
        html += `<tr><td>${t.description || t.name}</td><td>${t.input_units || '0.00'}</td><td>${t.input_amount || '0.00'}</td><td>${formatR(t.final_amount || 0)}</td></tr>`;
    });
    body.innerHTML = html;
}

// ─── YTD ───
function ppPopulateYTD(ytd) {
    const body = document.getElementById('ppYtdBody');
    if (!ytd || !ytd.sections) {
        body.innerHTML = '<tr><td colspan="14" style="text-align:center;color:#aaa;padding:20px;">No YTD data available</td></tr>';
        return;
    }
    let html = '';
    ytd.sections.forEach(section => {
        html += `<tr class="pp-ytd-section"><td colspan="14">${section.name}</td></tr>`;
        (section.items || []).forEach(item => {
            html += '<tr><td>' + item.name + '</td>';
            let total = 0;
            (item.months || []).forEach(val => {
                total += parseFloat(val) || 0;
                html += '<td>' + formatR(val) + '</td>';
            });
            html += '<td style="font-weight:700;background:#E0F2F1;">' + formatR(total) + '</td></tr>';
        });
        if (section.total) {
            html += '<tr class="pp-ytd-total"><td><strong>' + (section.total_label || 'TOTAL') + '</strong></td>';
            let grandTotal = 0;
            (section.total || []).forEach(val => {
                grandTotal += parseFloat(val) || 0;
                html += '<td><strong>' + formatR(val) + '</strong></td>';
            });
            html += '<td style="font-weight:700;background:#004D40;color:#fff;">' + formatR(grandTotal) + '</td></tr>';
        }
    });
    body.innerHTML = html;
}

// ─── LEAVE ───
function ppPopulateLeave(leave) {
    if (!leave || !leave.balances) return;
    leave.balances.forEach(bal => {
        const slug = bal.slug;
        const tbody = document.getElementById('ppLeave_' + slug);
        if (!tbody) return;
        if (!bal.rules || bal.rules.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;color:#aaa;font-size:12px;">No leave rules configured</td></tr>';
            return;
        }
        tbody.innerHTML = bal.rules.map(r => `
            <tr>
                <td style="text-align:left;">${r.description}</td>
                <td><input type="number" value="${r.entitlement || 0}" step="0.0001"></td>
                <td>${r.cycle_start || ''}</td>
                <td>${r.next_cycle || ''}</td>
                <td><input type="number" value="${r.opening_balance || 0}" step="0.00001"></td>
                <td>${formatR4(r.accrual || 0)}</td>
                <td>${formatR4(r.taken || 0)}</td>
                <td>${formatR4(r.closing_balance || 0)}</td>
                <td>${formatR4(r.planned || 0)}</td>
            </tr>
        `).join('');
    });
    if (leave.start_date) ppSetVal('lv_start_date', leave.start_date);
}

function ppPopulateLeaveHistory(history) {
    const body = document.getElementById('ppLeaveHistoryBody');
    if (!history || history.length === 0) {
        body.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#aaa;padding:20px;">No leave transactions recorded</td></tr>';
        return;
    }
    body.innerHTML = history.map(h => `
        <tr>
            <td>${h.description}</td>
            <td>${h.period_captured || ''}</td>
            <td>${h.leave_from || ''}</td>
            <td>${h.leave_to || ''}</td>
            <td style="text-align:right;">${h.units_calculated || '0'}</td>
            <td style="text-align:right;">${h.units_applied || '0'}</td>
            <td style="text-align:center;"><button class="pp-btn pp-btn-sm pp-btn-danger" onclick="ppDeleteLeaveTransaction(${h.id})"><i class="fas fa-trash"></i></button></td>
        </tr>
    `).join('');
}

// ─── TAB SWITCHING ───
function ppSwitchTab(el) {
    document.querySelectorAll('.pp-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.pp-tab-pane').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tab-' + el.dataset.tab).classList.add('active');
}

// ─── LEAVE ACCORDION ───
function ppToggleLeave(header) {
    header.classList.toggle('open');
    header.nextElementSibling.classList.toggle('open');
}

// ─── CALCULATIONS ───
function ppCalcRates() {
    const hpd = parseFloat(document.getElementById('hr_hours_per_day').value) || 9;
    const dpw = parseFloat(document.getElementById('hr_days_per_week').value) || 5;
    const hpm = (hpd * dpw * 52 / 12).toFixed(2);
    const dpm = (dpw * 52 / 12).toFixed(2);
    document.getElementById('hr_hours_per_month').value = hpm;
    document.getElementById('hr_days_per_month').value = dpm;

    const salary = parseFloat(document.getElementById('hr_basic_salary').value) || 0;
    if (salary > 0 && parseFloat(dpm) > 0) {
        document.getElementById('hr_rate_per_day').value = (salary / parseFloat(dpm)).toFixed(4);
    }
    if (salary > 0 && parseFloat(hpm) > 0) {
        document.getElementById('hr_hourly_rate').value = (salary / parseFloat(hpm)).toFixed(4);
    }
}

function ppCalcFromAnnual() {
    const annual = parseFloat(document.getElementById('hr_annual_salary').value) || 0;
    document.getElementById('hr_basic_salary').value = (annual / 12).toFixed(2);
    ppCalcRates();
}

function ppCalcFromMonthly() {
    const monthly = parseFloat(document.getElementById('hr_basic_salary').value) || 0;
    document.getElementById('hr_annual_salary').value = (monthly * 12).toFixed(2);
    ppCalcRates();
}

function ppCalcFromHourly() {
    const hourly = parseFloat(document.getElementById('hr_hourly_rate').value) || 0;
    const hpm = parseFloat(document.getElementById('hr_hours_per_month').value) || 195;
    document.getElementById('hr_basic_salary').value = (hourly * hpm).toFixed(2);
    document.getElementById('hr_annual_salary').value = (hourly * hpm * 12).toFixed(2);
    ppCalcRates();
}

function ppCalcAge() {
    const dob = document.getElementById('det_date_of_birth').value;
    if (!dob) { document.getElementById('det_age').value = ''; return; }
    const birth = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
    document.getElementById('det_age').value = 'The employee is now ' + age;
}

function ppTogglePayType() {
    const type = document.getElementById('hr_pay_type').value;
    document.getElementById('hrSalariedFields').style.display = type === 'salaried' ? 'flex' : 'none';
}

// ─── SAVE ───
function ppSaveCurrentTab() {
    if (!ppCurrentEmployee) return;
    const activeTab = document.querySelector('.pp-tab.active').dataset.tab;
    const formData = ppCollectTabData(activeTab);

    fetch('{{ url("cims/payroll/processing/save") }}/' + ppCurrentEmployee.id, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ tab: activeTab, data: formData })
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            ppShowToast('Saved successfully', 'success');
            // Refresh employee data in sidebar
            const emp = ppEmployees.find(e => e.id === ppCurrentEmployee.id);
            if (emp && formData.first_name) {
                emp.first_name = formData.first_name;
                emp.last_name = formData.last_name;
                emp.full_name = formData.first_name + ' ' + formData.last_name;
                ppRenderEmployees();
            }
        } else {
            ppShowToast(result.message || 'Save failed', 'error');
        }
    })
    .catch(() => ppShowToast('Network error', 'error'));
}

function ppCollectTabData(tab) {
    const data = {};
    const pane = document.getElementById('tab-' + tab);
    if (!pane) return data;
    pane.querySelectorAll('input, select, textarea').forEach(el => {
        if (el.name) {
            if (el.type === 'checkbox') data[el.name] = el.checked ? 1 : 0;
            else if (el.type === 'radio') { if (el.checked) data[el.name] = el.value; }
            else data[el.name] = el.value;
        }
    });
    return data;
}

function ppUndoChanges() {
    if (ppCurrentEmployee) ppSelectEmployee(ppCurrentEmployee.id);
}

// ─── GENERATE PAYSLIP ───
function ppGeneratePayslip() {
    if (!ppCurrentEmployee) return;
    window.open('{{ url("cims/payroll/processing/generate-payslip") }}/' + ppCurrentEmployee.id, '_blank');
}

function ppViewTaxCalc() {
    if (!ppCurrentEmployee) return;
    window.open('{{ url("cims/payroll/processing/tax-calculation") }}/' + ppCurrentEmployee.id, '_blank');
}

// ─── ADD ITEMS ───
function ppAddPayslipItem(section) {
    const name = prompt('Enter item name:');
    if (!name || !name.trim()) return;
    ppPayslipData[section].push({ name: name.trim(), hours: section === 'earnings' ? 0 : 1, rate: 0, amount: 0 });
    ppRenderPayslipSection(ppContainerMap[section], ppPayslipData[section], section);
    ppCalcPayslipTotals();
}
function ppSaveDefaults() {
    if (!ppCurrentEmployee) { ppShowToast('Select an employee first', 'error'); return; }
    const sections = {};
    ['earnings', 'deductions', 'contributions', 'fringe'].forEach(s => {
        sections[s] = ppPayslipData[s].map(item => ({
            name: item.name,
            hours: parseFloat(item.hours) || 0,
            rate: parseFloat(item.rate) || 0
        }));
    });

    fetch(`/cims/payroll/processing/save-defaults/${ppCurrentEmployee.id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ sections })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            ppShowToast(data.message, 'success');
            document.getElementById('ppDefaultsStatus').innerHTML = '<i class="fas fa-check-circle" style="color:#4CAF50;"></i> Saved defaults (' + data.count + ' items)';
        } else {
            ppShowToast(data.message || 'Error saving defaults', 'error');
        }
    })
    .catch(err => ppShowToast('Error: ' + err.message, 'error'));
}

function ppAddRA() { ppShowToast('Add RA — coming soon', 'success'); }
function ppAddLeaveTransaction() { ppShowToast('Add leave transaction — coming soon', 'success'); }
function ppDeleteLeaveTransaction(id) { ppShowToast('Delete — coming soon', 'success'); }

// ─── HELPERS ───
function ppSetVal(id, val) { const el = document.getElementById(id); if (el) el.value = val; }
function parseAmount(str) { return parseFloat((str || '0').replace(/[^0-9.-]/g, '')) || 0; }
function formatR(val) { return parseFloat(val || 0).toLocaleString('en-ZA', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
function formatR4(val) { return parseFloat(val || 0).toFixed(4); }

function ppShowToast(msg, type) {
    const toast = document.getElementById('ppToast');
    toast.textContent = msg;
    toast.className = 'pp-toast ' + type + ' show';
    setTimeout(() => toast.classList.remove('show'), 3000);
}
</script>
@endpush
