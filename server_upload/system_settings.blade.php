@extends('layouts.default')

@section('title', 'System Settings')

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
            </div>

            <hr class="my-4">

            {{-- Row 5: Compliance Badges --}}
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

        </div>
    </div>

</div>
@endsection
