@extends('layouts.default')

@section('title', 'System Settings')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
<div class="container-fluid">

    <x-primary-breadcrumb
        title="System Settings"
        subtitle="Manage system-wide settings"
        icon="fa-solid fa-cog"
        :breadcrumbs="[
            ['label' => '<i class=\'fa fa-home\'></i> CIMS', 'url' => url('/')],
            ['label' => 'System Settings'],
        ]"
    >
        <x-slot:actions>
            <x-close-button href="{{ url()->previous() }}"></x-close-button>
        </x-slot:actions>
    </x-primary-breadcrumb>

    {{-- Breadcrumb Card --}}
    <div class="card mb-4">
        <div class="card-header sd_background_pink">
            <h4 class="mb-0 text-white"><i class="fa-solid fa-bars me-2"></i>Breadcrumb</h4>
        </div>
        <div class="card-body">
            <div class="smartdash-page-header">
                <div class="page-title">
                    <div class="page-icon">
                        <i class="fa-solid fa-cog"></i>
                    </div>
                    <div>
                        <h1>Sample Page Title</h1>
                        <p>Sample subtitle text</p>
                    </div>
                </div>
                <div class="page-breadcrumb">
                    <a href="#">CIMS</a>
                    <span class="separator">/</span>
                    <span class="current">Sample Page</span>
                </div>
                <div class="page-actions">
                    <x-close-button href="#"></x-close-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Breadcrumb White Card --}}
    <div class="card mb-4">
        <div class="card-header sd_background_pink">
            <h4 class="mb-0 text-white"><i class="fa-solid fa-window-maximize me-2"></i>Breadcrumb — White</h4>
        </div>
        <div class="card-body" style="background:#f5f7fa; border-radius:0 0 12px 12px;">

            {{-- Demo 1: With icon and badge --}}
            <h6 class="text-muted mb-3 mt-2"><strong>With Icon & Badge</strong></h6>
            <div class="breadcrumb_white">
                <div class="bw_title_area">
                    <div class="bw_icon"><i class="fa-solid fa-chart-line"></i></div>
                    <div>
                        <div class="bw_title">Customer Aged Analysis</div>
                        <div class="bw_subtitle">Outstanding balances aged by due date</div>
                    </div>
                </div>
                <div class="bw_badge">Aged Analysis</div>
            </div>

            {{-- Demo 2: Without icon --}}
            <h6 class="text-muted mb-3 mt-4"><strong>Without Icon</strong></h6>
            <div class="breadcrumb_white">
                <div class="bw_title_area">
                    <div>
                        <div class="bw_title">Persons Directory</div>
                        <div class="bw_subtitle">Manage all persons and contacts</div>
                    </div>
                </div>
                <div class="bw_badge">People</div>
            </div>

            {{-- Demo 3: With action buttons --}}
            <h6 class="text-muted mb-3 mt-4"><strong>With Action Buttons</strong></h6>
            <div class="breadcrumb_white">
                <div class="bw_title_area">
                    <div class="bw_icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div>
                        <div class="bw_title">Invoice Manager</div>
                        <div class="bw_subtitle">Create and manage client invoices</div>
                    </div>
                </div>
                <div class="bw_actions">
                    <button type="button" class="button_master_add"><i class="fa fa-plus"></i> New</button>
                    <a href="#" class="button_master_close"><i class="fa-solid fa-circle-left"></i> Close</a>
                </div>
            </div>

            <div class="mt-3"><span style="font-size:18px; color:#000;">Class: <strong>breadcrumb_white</strong></span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Inner elements: .bw_title_area, .bw_icon, .bw_title, .bw_subtitle, .bw_badge, .bw_actions</span></div>

        </div>
    </div>

    {{-- Breadcrumb Master Card --}}
    <div class="card mb-4">
        <div class="card-header sd_background_pink">
            <h4 class="mb-0 text-white"><i class="fa-solid fa-layer-group me-2"></i>Breadcrumb — Master (Unified)</h4>
        </div>
        <div class="card-body" style="background:#f5f7fa; border-radius:0 0 12px 12px;">

            {{-- Demo 1: Full breadcrumb with all 3 rows --}}
            <h6 class="text-muted mb-3 mt-2"><strong>Full — All Rows Visible</strong></h6>
            <div class="breadcrumb_master">
                <div class="bm_header">
                    <div class="bm_title_area">
                        <div class="bm_icon"><i class="fa-solid fa-chart-line"></i></div>
                        <div>
                            <div class="bm_title">Customer Aged Analysis</div>
                            <div class="bm_subtitle">Outstanding balances aged by due date</div>
                        </div>
                    </div>
                    <div class="bm_badge">AGED ANALYSIS</div>
                </div>
                <div class="bm_controls">
                    <div class="bm_control_left">
                        <label class="date_master_picker_label">As of Date</label>
                        <div class="date_master_picker">
                            <input type="text" placeholder="Thursday, 19 March 2026" readonly>
                            <i class="fa-regular fa-calendar-days dm_icon"></i>
                        </div>
                    </div>
                    <div class="bm_control_center">
                        <div class="toggle_master_switch_sd">
                            <span class="toggle_master_label active" data-side="left">Summary</span>
                            <label class="toggle_master_track">
                                <input type="checkbox">
                                <span class="toggle_master_knob"></span>
                            </label>
                            <span class="toggle_master_label" data-side="right">Detailed</span>
                        </div>
                    </div>
                    <div class="bm_control_right">
                        <button class="button_master_generate"><i class="fa fa-sync-alt"></i> Generate</button>
                    </div>
                </div>
                <div class="bm_actions">
                    <button class="button_master_excel"><i class="fa fa-file-excel"></i> Excel</button>
                    <button class="button_master_pdf"><i class="fa fa-file-pdf"></i> PDF</button>
                    <button class="button_master_email"><i class="fa fa-envelope"></i> Email</button>
                    <button class="button_master_print"><i class="fa fa-print"></i> Print</button>
                    <a href="#" class="button_master_close"><i class="fa-solid fa-circle-left"></i> Close</a>
                </div>
            </div>

            {{-- Demo 2: Header + Actions only (no controls row) --}}
            <h6 class="text-muted mb-3 mt-4"><strong>Header + Actions Only (Controls Hidden)</strong></h6>
            <div class="breadcrumb_master">
                <div class="bm_header">
                    <div class="bm_title_area">
                        <div class="bm_icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                        <div>
                            <div class="bm_title">Invoice Manager</div>
                            <div class="bm_subtitle">Create and manage client invoices</div>
                        </div>
                    </div>
                    <div class="bm_badge">INVOICES</div>
                </div>
                <div class="bm_controls bm_hide"></div>
                <div class="bm_actions">
                    <button class="button_master_add"><i class="fa fa-plus"></i> New Invoice</button>
                    <button class="button_master_print"><i class="fa fa-print"></i> Print</button>
                    <a href="#" class="button_master_close"><i class="fa-solid fa-circle-left"></i> Close</a>
                </div>
            </div>

            {{-- Demo 3: Header only (controls + actions hidden) --}}
            <h6 class="text-muted mb-3 mt-4"><strong>Header Only (Minimal)</strong></h6>
            <div class="breadcrumb_master">
                <div class="bm_header">
                    <div class="bm_title_area">
                        <div class="bm_icon"><i class="fa-solid fa-users"></i></div>
                        <div>
                            <div class="bm_title">Persons Directory</div>
                            <div class="bm_subtitle">Manage all persons and contacts</div>
                        </div>
                    </div>
                    <div class="bm_badge">PEOPLE</div>
                </div>
            </div>

            <div class="mt-3"><span style="font-size:18px; color:#000;">Class: <strong>breadcrumb_master</strong></span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Rows: .bm_header, .bm_controls, .bm_actions</span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Header: .bm_title_area, .bm_icon, .bm_title, .bm_subtitle, .bm_badge</span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Controls: .bm_control_left, .bm_control_center, .bm_control_right</span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Hide anything: add <strong>.bm_hide</strong> class to any row or element</span></div>

        </div>
    </div>

    {{-- Pills Master Card --}}
    <div class="card mb-4">
        <div class="card-header sd_background_pink">
            <h4 class="mb-0 text-white"><i class="fa-solid fa-tablets me-2"></i>Pills — Quick Select Master</h4>
        </div>
        <div class="card-body" style="background:#f5f7fa; border-radius:0 0 12px 12px;">

            {{-- Demo: Full Quick Select Pills --}}
            <h6 class="text-muted mb-3 mt-2"><strong>Quick Select — Date Range Pills</strong></h6>
            <div class="breadcrumb_master">
                <div class="bm_pills">
                    <label class="bm_pills_label">Quick Select:</label>
                    <div class="bm_pills_content">
                        <div class="bm_pills_row">
                            <button type="button" class="bm_pill">Today</button>
                            <button type="button" class="bm_pill">This Week</button>
                            <button type="button" class="bm_pill">This Fortnight</button>
                            <button type="button" class="bm_pill">This Month</button>
                            <button type="button" class="bm_pill">Last Month</button>
                            <button type="button" class="bm_pill">This Quarter</button>
                            <button type="button" class="bm_pill">Last Quarter</button>
                            <button type="button" class="bm_pill">Last 3 Months</button>
                            <button type="button" class="bm_pill">Last 6 Months</button>
                        </div>
                        <div class="bm_pills_row">
                            <button type="button" class="bm_pill">This Calendar Year</button>
                            <button type="button" class="bm_pill">Last Calendar Year</button>
                            <button type="button" class="bm_pill">This Financial Year</button>
                            <button type="button" class="bm_pill">Last Financial Year</button>
                            <button type="button" class="bm_pill active">All Transactions</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Demo: Minimal Pills (fewer options) --}}
            <h6 class="text-muted mb-3 mt-4"><strong>Minimal — Single Row</strong></h6>
            <div class="breadcrumb_master">
                <div class="bm_pills">
                    <label class="bm_pills_label">Filter:</label>
                    <div class="bm_pills_content">
                        <div class="bm_pills_row">
                            <button type="button" class="bm_pill active">All</button>
                            <button type="button" class="bm_pill">Active</button>
                            <button type="button" class="bm_pill">Pending</button>
                            <button type="button" class="bm_pill">Overdue</button>
                            <button type="button" class="bm_pill">Closed</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3"><span style="font-size:18px; color:#000;">Container: <strong>bm_pills</strong></span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Label: .bm_pills_label (left-aligned, teal, uppercase)</span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Content wrapper: .bm_pills_content (holds one or more rows)</span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Rows: .bm_pills_row (flex row, auto-sized pills)</span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Individual pill: .bm_pill (teal gradient, flex:1 auto-size)</span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">Active state: add <strong>.active</strong> class to highlight selected pill</span></div>
            <div class="mt-2"><span style="font-size:14px; color:#666;">Usage: Place <strong>.bm_pills</strong> inside <strong>.breadcrumb_master</strong> as Row 3 (between controls and actions)</span></div>
            <div class="mt-1"><span style="font-size:14px; color:#666;">JS: Attach click handler to <strong>.bm_pill</strong> elements with <strong>data-period</strong> attribute</span></div>

        </div>
    </div>

    {{-- Buttons Card --}}
    <div class="card">
        <div class="card-header sd_background_pink">
            <h4 class="mb-0 text-white"><i class="fa-solid fa-hand-pointer me-2"></i>Buttons — Master Library</h4>
        </div>
        <div class="card-body">

            {{-- Row 1: Core Action Buttons --}}
            <h6 class="text-muted mb-3 mt-2"><strong>Core Actions</strong></h6>
            <div class="row mb-3">
                <div class="col-3 text-center mb-4">
                    <a href="#" class="button_master_close"><i class="fa-solid fa-circle-left"></i> Close</a>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_close</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_close_blue"><i class="fa fa-times"></i> Close</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_close_blue</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_save"><i class="fa fa-save"></i> Save</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_save</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_update"><i class="fa fa-save"></i> Update</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_update</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <a href="#" class="button_master_edit"><i class="fa fa-edit"></i> Edit</a>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_edit</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_delete"><i class="fa fa-trash"></i> Delete</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_delete</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_cancel"><i class="fa fa-times"></i> Cancel</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_cancel</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 2: Form & Navigation Buttons --}}
            <h6 class="text-muted mb-3"><strong>Form & Navigation</strong></h6>
            <div class="row mb-3">
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_submit"><i class="fa fa-paper-plane"></i> Submit</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_submit</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_add"><i class="fa fa-plus"></i> Add</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_add</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_search"><i class="fa fa-search"></i> Search</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_search</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <a href="#" class="button_master_back"><i class="fa fa-arrow-left"></i> Back</a>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_back</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_next">Next <i class="fa fa-arrow-right"></i></button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_next</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_reset"><i class="fa fa-undo"></i> Reset</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_reset</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 3: Document & Data Buttons --}}
            <h6 class="text-muted mb-3"><strong>Documents & Data</strong></h6>
            <div class="row mb-3">
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_print"><i class="fa fa-print"></i> Print</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_print</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <a href="#" class="button_master_download"><i class="fa fa-download"></i> Download</a>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_download</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <a href="#" class="button_master_email"><i class="fa fa-envelope"></i> Email</a>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_email</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_export"><i class="fa fa-file-export"></i> Export</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_export</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_import"><i class="fa fa-file-import"></i> Import</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_import</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <a href="#" class="button_master_view"><i class="fa fa-eye"></i> View</a>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_view</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 4: Status & Workflow Buttons --}}
            <h6 class="text-muted mb-3"><strong>Status & Workflow</strong></h6>
            <div class="row mb-3">
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_approve"><i class="fa fa-check"></i> Approve</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_approve</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_reject"><i class="fa fa-ban"></i> Reject</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_reject</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_warning"><i class="fa fa-exclamation-triangle"></i> Warning</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_warning</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_notify"><i class="fa fa-bell"></i> Notify</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_notify</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_refresh"><i class="fa fa-sync"></i> Refresh</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_refresh</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_archive"><i class="fa fa-box-archive"></i> Archive</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_archive</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_yes"><i class="fa fa-check"></i> Yes</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_yes</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_no"><i class="fa fa-ban"></i> No</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_no</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_ok"><i class="fa fa-check"></i> OK</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_ok</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 5: Generate, Load & File Export Buttons --}}
            <h6 class="text-muted mb-3"><strong>Generate, Load & File Export</strong></h6>
            <div class="row mb-3">
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_generate"><i class="fa fa-cogs"></i> Generate</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_generate</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_load"><i class="fa fa-spinner"></i> Load</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_load</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_excel"><i class="fa fa-file-excel"></i> Excel</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_excel</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_word"><i class="fa fa-file-word"></i> Word</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_word</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_csv"><i class="fa fa-file-csv"></i> CSV</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_csv</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_pdf"><i class="fa fa-file-pdf"></i> PDF</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_pdf</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 6: Entity & Status Buttons --}}
            <h6 class="text-muted mb-3"><strong>Entity & Status</strong></h6>
            <div class="row mb-3">
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_address"><i class="fa fa-map-marker-alt"></i> Address</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_address</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_directors"><i class="fa fa-user-tie"></i> Directors</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_directors</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_people"><i class="fa fa-users"></i> People</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_people</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_banks"><i class="fa fa-university"></i> Banks</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_banks</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_active"><i class="fa fa-check-circle"></i> Active</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_active</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_inactive"><i class="fa fa-times-circle"></i> Inactive</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_inactive</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <button type="button" class="button_master_list_all"><i class="fa fa-list"></i> List All</button>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">button_master_list_all</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 7: Compliance Badges --}}
            <h6 class="text-muted mb-3"><strong>Compliance Badges</strong></h6>
            <div class="row mb-3">
                <div class="col-3 text-center mb-4">
                    <img src="{{ asset('assets/cims_core/Colour_Indicator_GREEN.png') }}" alt="Compliant" style="height:40px;">
                    <div class="mt-2"><span style="font-size:18px; color:#000;">badge_master_compliant</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <img src="{{ asset('assets/cims_core/Colour_Indicator_RED.png') }}" alt="Non Compliant" style="height:40px;">
                    <div class="mt-2"><span style="font-size:18px; color:#000;">badge_master_non_compliant</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <span class="badge_master_compliant_pill"><i class="fa fa-check"></i> Compliant</span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">badge_master_compliant_pill</span></div>
                </div>
                <div class="col-3 text-center mb-4">
                    <span class="badge_master_non_compliant_pill"><i class="fa fa-times"></i> Non Compliant</span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">badge_master_non_compliant_pill</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 8: Smart Toggle --}}
            <h6 class="text-muted mb-3"><strong>Smart Toggle</strong></h6>
            <div class="row mb-3">
                <div class="col-4 text-center mb-4">
                    <div class="toggle_master_switch_sd">
                        <span class="toggle_master_label active" data-side="left">Summary</span>
                        <label class="toggle_master_track">
                            <input type="checkbox" id="demo_toggle_1">
                            <span class="toggle_master_knob"></span>
                        </label>
                        <span class="toggle_master_label" data-side="right">Detailed</span>
                    </div>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">toggle_master_switch_sd</span></div>
                </div>
                <div class="col-4 text-center mb-4">
                    <div class="toggle_master_switch_oo">
                        <span class="toggle_master_label active" data-side="left">Off</span>
                        <label class="toggle_master_track">
                            <input type="checkbox" id="demo_toggle_2">
                            <span class="toggle_master_knob"></span>
                        </label>
                        <span class="toggle_master_label" data-side="right">On</span>
                    </div>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">toggle_master_switch_oo</span></div>
                </div>
                <div class="col-4 text-center mb-4">
                    <div class="toggle_master_switch_ai">
                        <span class="toggle_master_label active" data-side="left">Active</span>
                        <label class="toggle_master_track">
                            <input type="checkbox" id="demo_toggle_3">
                            <span class="toggle_master_knob"></span>
                        </label>
                        <span class="toggle_master_label" data-side="right">Inactive</span>
                    </div>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">toggle_master_switch_ai</span></div>
                </div>
            </div>

        </div>
    </div>

    {{-- Date Master Card --}}
    <div class="card mt-4">
        <div class="card-header sd_background_pink">
            <h4 class="mb-0 text-white"><i class="fa-solid fa-calendar-days me-2"></i>Date Master</h4>
        </div>
        <div class="card-body">

            {{-- Date Format Displays --}}
            <h6 class="text-muted mb-3 mt-2"><strong>Date Format Displays</strong></h6>
            <div class="row mb-3">
                <div class="col-4 mb-4">
                    <span class="date_master_long"><i class="fa-regular fa-calendar"></i> <span id="dm_long"></span></span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_long</span></div>
                </div>
                <div class="col-4 mb-4">
                    <span class="date_master_short"><i class="fa-regular fa-calendar"></i> <span id="dm_short"></span></span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_short</span></div>
                </div>
                <div class="col-4 mb-4">
                    <span class="date_master_month_year"><i class="fa-regular fa-calendar"></i> <span id="dm_month_year"></span></span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_month_year</span></div>
                </div>
                <div class="col-4 mb-4">
                    <span class="date_master_year"><i class="fa-regular fa-calendar"></i> <span id="dm_year"></span></span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_year</span></div>
                </div>
                <div class="col-4 mb-4">
                    <span class="date_master_day_month"><i class="fa-regular fa-calendar"></i> <span id="dm_day_month"></span></span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_day_month</span></div>
                </div>
                <div class="col-4 mb-4">
                    <span class="date_master_day_short"><i class="fa-regular fa-calendar-check"></i> <span id="dm_day_short"></span></span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_day_short</span></div>
                </div>
                <div class="col-6 mb-4">
                    <span class="date_master_day_long"><i class="fa-regular fa-calendar-check"></i> <span id="dm_day_long"></span></span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_day_long</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Date Picker Input --}}
            <h6 class="text-muted mb-3"><strong>Date Picker Input</strong></h6>
            <div class="row mb-3">
                <div class="col-4 mb-4">
                    <label class="date_master_picker_label">Select Date</label>
                    <div class="date_master_picker">
                        <input type="text" id="dm_picker_demo" placeholder="dd/mm/yyyy" readonly>
                        <i class="fa-regular fa-calendar-days dm_icon"></i>
                    </div>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_picker</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Date Range Picker --}}
            <h6 class="text-muted mb-3"><strong>Date Range Picker</strong></h6>
            <div class="row mb-3">
                <div class="col-8 mb-4">
                    <label class="date_master_picker_label">Date Range</label>
                    <div class="date_master_range">
                        <div class="dm_range_from">
                            <i class="fa-regular fa-calendar dm_icon"></i>
                            <input type="text" id="dm_range_from" placeholder="From date" readonly>
                        </div>
                        <div class="dm_range_sep">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                        <div class="dm_range_to">
                            <i class="fa-regular fa-calendar dm_icon"></i>
                            <input type="text" id="dm_range_to" placeholder="To date" readonly>
                        </div>
                    </div>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_range</span></div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Date Badge --}}
            <h6 class="text-muted mb-3"><strong>Date Badge</strong></h6>
            <div class="row mb-3">
                <div class="col-3 mb-4">
                    <span class="date_master_badge"><i class="fa-regular fa-clock"></i> <span id="dm_badge"></span></span>
                    <div class="mt-2"><span style="font-size:18px; color:#000;">date_master_badge</span></div>
                </div>
                <div class="col-3 mb-4">
                    <span class="date_master_badge"><i class="fa-regular fa-clock"></i> Today</span>
                    <div class="mt-2"><span style="font-size:14px; color:#666;">Example: relative date</span></div>
                </div>
            </div>

        </div>
    </div>

</div>

    {{-- ================================================================
        EXCEL MASTER — Export to Excel Component
        ================================================================ --}}
    <div class="card mb-4">
        <div class="card-header sd_background_pink">
            <h4 class="mb-0 text-white"><i class="fa fa-file-excel me-2"></i>Excel Master — Export to Excel</h4>
        </div>
        <div class="card-body">

            {{-- Function Reference --}}
            <h5 style="font-weight:700; color:#0d3d56; margin-bottom:4px;"><i class="fa-solid fa-code me-2" style="color:#17A2B8;"></i>Master Function</h5>
            <div class="excel_master_ref">
                cimsExcelExport({<span class="em_param"> title, company, date, headers, rows, totals, filename </span>})
                <br><span class="em_comment">// Generates formatted .xlsx with Save &amp; Open options</span>
            </div>
            <div class="excel_master_info">
                <strong>Required CDN scripts on page:</strong><br>
                <code>&lt;script src="exceljs@4.4.0"&gt;</code>
                <code>&lt;script src="file-saver@2.0.5"&gt;</code>
                <code>&lt;script src="cims_excel_master.js"&gt;</code><br><br>
                <strong>Parameters:</strong><br>
                <code>title</code> — Report title (e.g. "CUSTOMER AGED ANALYSIS")<br>
                <code>company</code> — Company name for header<br>
                <code>date</code> — Date string to display<br>
                <code>headers</code> — Array of column header names<br>
                <code>headerAlign</code> — Array of alignments ('left' or 'right')<br>
                <code>colWidths</code> — Array of column widths<br>
                <code>rows</code> — Array of { cells: [...], styles: {...} } objects<br>
                <code>totals</code> — { label: 'GRAND TOTAL', values: [n1, n2, ...] }<br>
                <code>agingCols</code> — { amber: colIdx, red: colIdx, darkRed: colIdx }<br>
                <code>filename</code> — Filename without .xlsx extension
            </div>

            <hr style="margin:20px 0; border-color:#e2e8f0;">

            {{-- Number Format Demo --}}
            <h5 style="font-weight:700; color:#0d3d56; margin-bottom:8px;"><i class="fa-solid fa-hashtag me-2" style="color:#17A2B8;"></i>Number Format</h5>
            <div class="excel_master_num_demo">
                <div><span class="em_label">Format</span><br><span class="em_value">1 000.00</span></div>
                <div style="color:#ccc; font-size:20px;">|</div>
                <div><span class="em_label">Thousands</span><br><span class="em_value" style="color:#17A2B8;">SPACE</span></div>
                <div style="color:#ccc; font-size:20px;">|</div>
                <div><span class="em_label">Decimal</span><br><span class="em_value" style="color:#17A2B8;">PERIOD (.)</span></div>
                <div style="color:#ccc; font-size:20px;">|</div>
                <div><span class="em_label">Places</span><br><span class="em_value" style="color:#17A2B8;">2</span></div>
            </div>

            <hr style="margin:20px 0; border-color:#e2e8f0;">

            {{-- Excel Preview --}}
            <h5 style="font-weight:700; color:#0d3d56; margin-bottom:8px;"><i class="fa-solid fa-table me-2" style="color:#17A2B8;"></i>Output Preview</h5>
            <div class="excel_master_preview">
                <div class="excel_master_preview_header">
                    <div style="font-size:14px;">Company Name (Pty) Ltd</div>
                </div>
                <div style="background:#17A2B8; color:#fff; padding:6px 16px; font-weight:700; font-size:12px; text-align:center; letter-spacing:1px;">
                    CUSTOMER AGED ANALYSIS
                </div>
                <div style="background:#f0fafb; padding:6px 16px; font-size:11px; color:#0d3d56; text-align:center; font-style:italic;">
                    As of Date: Wednesday, 12 March 2026
                </div>
                {{-- Header row --}}
                <div class="excel_master_preview_row" style="background:#0d3d56; color:#fff; font-weight:700; font-size:11px; text-transform:uppercase;">
                    <div class="excel_master_preview_cell">Client Code</div>
                    <div class="excel_master_preview_cell">Client Name</div>
                    <div class="excel_master_preview_cell em_right">Current</div>
                    <div class="excel_master_preview_cell em_right">30 Days</div>
                    <div class="excel_master_preview_cell em_right">60 Days</div>
                    <div class="excel_master_preview_cell em_right">90+ Days</div>
                    <div class="excel_master_preview_cell em_right">Total</div>
                </div>
                {{-- Sample rows --}}
                <div class="excel_master_preview_row">
                    <div class="excel_master_preview_cell em_teal">ACC100</div>
                    <div class="excel_master_preview_cell" style="font-weight:600;">Accounting Corp</div>
                    <div class="excel_master_preview_cell em_right">9 783.00</div>
                    <div class="excel_master_preview_cell em_right em_amber">1 200.00</div>
                    <div class="excel_master_preview_cell em_right">0.00</div>
                    <div class="excel_master_preview_cell em_right">0.00</div>
                    <div class="excel_master_preview_cell em_right" style="font-weight:700; color:#0d3d56;">10 983.00</div>
                </div>
                <div class="excel_master_preview_row">
                    <div class="excel_master_preview_cell em_teal">FRG100</div>
                    <div class="excel_master_preview_cell" style="font-weight:600;">Fringe Media</div>
                    <div class="excel_master_preview_cell em_right">0.00</div>
                    <div class="excel_master_preview_cell em_right">0.00</div>
                    <div class="excel_master_preview_cell em_right em_red">5 450.00</div>
                    <div class="excel_master_preview_cell em_right em_dark_red">12 300.00</div>
                    <div class="excel_master_preview_cell em_right" style="font-weight:700; color:#0d3d56;">17 750.00</div>
                </div>
                {{-- Total row --}}
                <div class="excel_master_preview_row em_total">
                    <div class="excel_master_preview_cell"></div>
                    <div class="excel_master_preview_cell" style="text-align:right;">GRAND TOTAL</div>
                    <div class="excel_master_preview_cell em_right">9 783.00</div>
                    <div class="excel_master_preview_cell em_right">1 200.00</div>
                    <div class="excel_master_preview_cell em_right">5 450.00</div>
                    <div class="excel_master_preview_cell em_right">12 300.00</div>
                    <div class="excel_master_preview_cell em_right">28 733.00</div>
                </div>
            </div>

            <hr style="margin:20px 0; border-color:#e2e8f0;">

            {{-- Demo button --}}
            <h5 style="font-weight:700; color:#0d3d56; margin-bottom:8px;"><i class="fa-solid fa-play me-2" style="color:#17A2B8;"></i>Live Demo</h5>
            <button class="excel_master_btn_demo" id="btnExcelDemo">
                <i class="fa fa-file-excel"></i> Export to Excel
            </button>
            <div style="margin-top:8px; font-size:12px; color:#999;">Click to generate a sample .xlsx file with full formatting</div>

        </div>
    </div>

</div>

{{-- Flatpickr CDN --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Smart Toggle demo JS --}}
<script>
document.querySelectorAll('[class*="toggle_master_switch"] input[type="checkbox"]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var wrapper = this.closest('[class*="toggle_master_switch"]');
        var labels = wrapper.querySelectorAll('.toggle_master_label');
        labels.forEach(function(lbl) { lbl.classList.remove('active'); });
        if (this.checked) {
            wrapper.querySelector('[data-side="right"]').classList.add('active');
        } else {
            wrapper.querySelector('[data-side="left"]').classList.add('active');
        }
    });
});

/* ---- Date Master: populate live date displays ---- */
(function() {
    var now = new Date();
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var monthsShort = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var daysShort = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    var d = now.getDate();
    var m = now.getMonth();
    var y = now.getFullYear();
    var dow = now.getDay();
    var dd = d < 10 ? '0' + d : d;
    var mm = (m + 1) < 10 ? '0' + (m + 1) : (m + 1);

    function setText(id, val) {
        var el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    setText('dm_long', d + ' ' + months[m] + ' ' + y);
    setText('dm_short', dd + '/' + mm + '/' + y);
    setText('dm_month_year', months[m] + ' ' + y);
    setText('dm_year', y);
    setText('dm_day_month', d + ' ' + months[m]);
    setText('dm_day_short', daysShort[dow] + ', ' + d + ' ' + monthsShort[m]);
    setText('dm_day_long', days[dow] + ', ' + d + ' ' + months[m] + ' ' + y);
    setText('dm_badge', dd + '/' + mm + '/' + y);
})();

/* ---- Date Master: initialise flatpickr pickers ---- */
if (typeof flatpickr !== 'undefined') {
    flatpickr('#dm_picker_demo', {
        dateFormat: 'd/m/Y',
        defaultDate: new Date(),
        animate: true
    });
    flatpickr('#dm_range_from', {
        dateFormat: 'd/m/Y',
        defaultDate: new Date(new Date().setDate(new Date().getDate() - 7)),
        animate: true
    });
    flatpickr('#dm_range_to', {
        dateFormat: 'd/m/Y',
        defaultDate: new Date(),
        animate: true
    });
}

/* ---- Excel Master: demo button handler ---- */
document.getElementById('btnExcelDemo').addEventListener('click', function() {
    cimsExcelExport({
        title:      'CUSTOMER AGED ANALYSIS',
        subtitle:   'Outstanding Balances by Aging Bucket',
        company:    'SmartWeigh (Pty) Ltd',
        date:       'Wednesday, 12 March 2026',
        filename:   'Demo_Aged_Analysis_2026-03-12',
        headers:    ['Client Code','Client Name','Current','30 Days','60 Days','90+ Days','Total'],
        headerAlign:['left','left','right','right','right','right','right'],
        colWidths:  [16, 32, 16, 16, 16, 16, 18],
        rows: [
            { cells: ['ACC100','Accounting Corp',9783.00,1200.00,0,0,10983.00], styles: { 0:{font:'teal'}, 1:{font:'boldText'}, 6:{font:'bold'} } },
            { cells: ['FRG100','Fringe Media',0,0,5450.00,12300.00,17750.00], styles: { 0:{font:'teal'}, 1:{font:'boldText'}, 6:{font:'bold'} } },
            { cells: ['HVY100','Heavy Trucks',28150.00,0,0,0,28150.00], styles: { 0:{font:'teal'}, 1:{font:'boldText'}, 6:{font:'bold'} } },
            { cells: ['HEL100','Hello Laser',3475.00,0,0,0,3475.00], styles: { 0:{font:'teal'}, 1:{font:'boldText'}, 6:{font:'bold'} } },
            { cells: ['NXS100','Nexus Solutions',13845.00,0,977.00,0,14822.00], styles: { 0:{font:'teal'}, 1:{font:'boldText'}, 6:{font:'bold'} } }
        ],
        totals: {
            label: 'GRAND TOTAL (5 clients)',
            values: [55253.00, 1200.00, 6427.00, 12300.00, 75180.00]
        },
        agingCols: { amber: 3, red: 4, darkRed: 5 }
    });
});
</script>

{{-- Excel Master CDN --}}
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
<script src="/public/modules/cimscore/js/cims_excel_master.js"></script>
@endsection
