<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 


?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
  integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<meta name="csrf-token" content="<?= $this->security->get_csrf_hash(); ?>" />
<?php
$paymentTypes = [
    'cash' => 'Cash',
    'master_card' => 'Master Card',
    'bank_transfer' => 'Bank Transfer',
    'mobile_payment' => 'Mobile Payment'
];

$statusOptions = [
    '1' => 'Active',
    '0' => 'Inactive'
]; ?>
<style>
    .preview { 
        width: 292px;
        height: 239px;
    }
    .period_label {
        background: #0f2590;
        height: 100px;
        color: #fff;
    }
    .period_label .header_bar { 
        font-size: 44px;
        text-align: right;
        font-weight: 600;
        padding-top: 29px;
        padding-right: 27px;
    }
    .phone_icon_text {
        padding: 14.5px !important;
        height: auto;
        position: absolute;
        right: 0px !important;
        margin-top: 1px !important;
        border-left: none !important;
        border-bottom: none !important;
        border-left: none !important;
        background-color: #f3f0f9 !important;
        color: #9da1a5 !important;
        font-size: 18px !important;
        margin-right: 1px !important;
        border-radius: 0px !important;
        border-top-right-radius: 10px !important;
        border-bottom-right-radius: 10px !important;
   }
    #document_wrapper { margin-left: 20px;
        margin-right: 15px}
    div#wrapper { background: #fff;}
    .clearfix_row {
            border: 1px solid #ccc;
        }
        .pad_10 { padding-top:10px; }
        .pad_30 { padding-top:30px; }
        
        .section-header h3 { color: #2f3f4c;
    font-weight: 700;
    font-size: 60px; }
        .file_adjust { 
            max-height: 127px;
            object-fit: contain;
        }
        .divider_hr { border-bottom: 2px solid #4967ff; padding-top: 20px; } 
        .disabled_input_field { pointer-events:none; }
        .bg_field_color { background-color: #ccc !important; }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
        #documentType {
            padding: 10px !important;
            object-fit: contain;
            margin: auto;
            margin-top: 12px;
 
        }
        .dropzone .dz-preview {
            position: relative;
            display: inline-block;
        }
      

    .dtbl-footer {
        font-weight: bold !important;
    }

    html {
        background-color: #626f80 !important;
    }

    .dtsp-panesContainer {
        width: 88% !important;
    }

    .btn-default {
        font-size: 1.3em !important;
        margin-right: 10px !important;
        font-weight: bold;
    }
    
    

    .mb { margin-bottom: 0 !important; }

    .selectize-control.single .selectize-input {
        background-image: none !important;
        background-color: #fff !important;
    }
    .selectize-dropdown .selected {
        background-color: #1F7EE1 !important;
        color: #fff !important;
    }
    .option {
        border-top: 2px solid #1F7EE1 !important;
        padding-top: 14px !important;
        padding-bottom: 14px !important;
        padding-left: 14px !important;
        
    }
    .option:hover {
        background-color: #1F7EE1 !important;
        color: #fff !important;
    }
    .selectize-dropdown-content .option:nth-child(even) {
        background-color: #ECF2FF;
    }
    .selectize-control.single .selectize-input {
        padding-bottom: 6px !important;
    padding-top: 15px;

        
    }
    .normalize_input {
        width: 100%;
        padding-top: 14px;
        padding-bottom: 10px;
        box-shadow: none;
        padding-left: 14px;
        font-size: 16px !important;
        border-width: 1px !important;
        box-shadow: none !important;
        outline: none !important;
        color: blue !important;
        font-weight: 700 !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        border-color: #000 !important;
    }
    .selectize-dropdown .active:not(.selected) {
        background-color: #1F7EE1 !important;
        color: #fff !important;
    }
    .selectize-input>* {
        font-size: 24px !important;
        color: blue !important;
        font-weight: 700 !important;
        padding-left:6px !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
        padding-bottom: 10px;
        padding-top: 3px;
    }
    .selectize-control.single .selectize-input {
        border-radius: 10px !important;
    }
    .panel-body:hover {
        box-shadow: none;
    }
    input::placeholder {
            color: blue;
        }
    .two-decimals, .pay_ref_num, .ref_no {
        font-size: 24px !important;
        text-align: right;
        padding-right: 20px;
    }
    .form-group input:not([type="file"]) {
         font-size:24px !important; }
    .selectize-dropdown-content{ font-size: 24px; }
    .custom-select input { padding-left:6px !important;padding-bottom:6px !important; }
    .emp_heading {    color: #fff !important; font-size: 44px !important;
        top: 33px;
    position: relative; }
    .save_btn {
        font-size: 30px;
    padding-top: 17px;
    padding-bottom: 17px;
    padding-left: 17px;
    padding-right: 17px;
    }
    .month_emp_return {  padding-top: 22px;}
    .file_label { font-size:19px !important; }
   
    
</style>
<div id="wrapper" class="customer_profile">
    <div class="content">
        
    <input type="hidden" id="csrf_token" value="<?= $this->security->get_csrf_hash(); ?>" />
        <?php if (isset($document) && (staff_cant('view', 'customers') && is_customer_admin($document->id))) {?>
        <div class="alert alert-info">
            <?php echo e(_l('customer_admin_login_as_client_message', get_staff_full_name(get_staff_user_id()))); ?>
        </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-3">
                <?php if (isset($document)) { ?>
                <h4 class="tw-text-lg tw-font-semibold tw-text-neutral-800 tw-mt-0">
                    <div class="tw-space-x-3 tw-flex tw-items-center">
                    <span class="tw-truncate">
                            <?php echo  'Edit ' . (isset($document) ? $document->title . ' ' . $document->title : ''); ?>
                    </span>

                        
                    </div>
                   
                </h4>
                <?php } ?>
            </div>
            <div class="clearfix"></div>

            <!-- Remove Tabs and Add Title -->
           

            <div class="tw-mt-12 sm:tw-mt-0 <?php echo isset($emp_returns) ? 'col-md-12' : 'col-md-12'; ?>">

            <?php echo form_open($this->uri->uri_string(), ['class' => 'emp_return-form', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data']); ?>
     
                <div class="panel_s">
                    <div class="panel-body">
                       
                        <div class="divider_hr row"></div>    
                        <div class="row">
                            <div class="col-md-9  section-header">
                                <h3 class="month_emp_return">Month Employee Return</h3>
                            </div>
                            <div class="col-md-3">
                                <img height="145" src="<?php echo  base_url('assets/images/sars_logo.png'); ?>" >
                            </div>
                           

                        </div>
                        
                        <div class="divider_hr row"></div>    
                        <div class="row">
                            <div class="col-md-12 ">
                                 <div class="col-md-6 section-header">
                                    <h3 class="emp_heading">EMP 201</h3>
                                </div>
                                <div class="period_label">
                                        <h3 class="period_heading header_bar">
                                            DECEMBER 2023 - 202312
                                        </h3>
                                        <input type="hidden" class="year_month_number" value="" >
                                        <input name="financial_year" type="hidden" id="financial_year"  value="" >
                                        <input name="period_combo" type="hidden" id="period_combo"  value="" >
                                       
                                    
                                </div>
                            </div>

                           
                        </div>
                        
                     
                        <div class="row pad_30 " >

                            
                            <div class="col-md-8">
                                <div class="form-group mb">
                                    <label for="company_name" >Company Name/ Trading Name  </label>
                                    <select id="client" name="company_name" class="custom-select" required="required">
                                       <option value="">Please select</option> 
                                    
                                            <?php foreach ($documentClients as $client): ?>
                                                <option 
                                                    data-userid="<?php echo isset($client->userid) ? htmlspecialchars($client->userid) : ''; ?>"
                                                    data-reg_number="<?php echo isset($client->customers_company_registration_number) ? htmlspecialchars($client->customers_company_registration_number) : ''; ?>"
                                                    data-reg_date="<?php echo isset($client->customers_company_registration_date) ? htmlspecialchars($client->customers_company_registration_date) : ''; ?>"
                                                    data-compliance="<?php echo isset($client->customers_compliance_status) ? htmlspecialchars($client->customers_compliance_status) : ''; ?>"
                                                    data-paye_number="<?php echo isset($client->customers_paye_number) ? htmlspecialchars($client->customers_paye_number) : ''; ?>" 
                                                    data-sdl_number="<?php echo isset($client->customers_sdl_number) ? htmlspecialchars($client->customers_sdl_number) : ''; ?>" 
                                                    data-uif_number="<?php echo isset($client->customers_uif_number) ? htmlspecialchars($client->customers_uif_number) : ''; ?>" 
                                                    data-tax_number="<?php echo isset($client->customers_company_income_tax_number) ? htmlspecialchars($client->customers_company_income_tax_number) : ''; ?>" 
                                                   
                                                    data-sars_first_name="<?php echo isset($client->customers_first_name) ? htmlspecialchars($client->customers_first_name) : ''; ?>" 
                                                    data-sars_surname="<?php echo isset($client->customers_last_name) ? htmlspecialchars($client->customers_last_name) : ''; ?>" 
                                                    data-sars_business_number="<?php echo isset($client->customers_business_telephone_no) ? htmlspecialchars($client->customers_business_telephone_no) : ''; ?>" 
                                                    data-sars_mobile_number="<?php echo isset($client->customers_mobile_number) ? htmlspecialchars($client->customers_mobile_number) : ''; ?> " 
                                                    data-sars_email_address="<?php echo isset($client->customers_admin_email_address) ? htmlspecialchars($client->customers_admin_email_address) : ''; ?>" 
                                                    data-sars_customers_whatsapp_number="<?php echo isset($client->customers_whatsapp_number) ? htmlspecialchars($client->customers_whatsapp_number) : ''; ?>" 
                                                    data-sars_home_number="<?php echo isset($client->customers_home_telephone_no) ? htmlspecialchars($client->customers_home_telephone_no) : ''; ?>" 
                                                    data-sars_position="<?php echo isset($client->customers_sars_position) ? htmlspecialchars($client->customers_sars_position) : ''; ?>" 
                                                    data-code="<?php echo isset($client->customers_customer_code) ? htmlspecialchars($client->customers_customer_code) : ''; ?>" 
                                                    data-vat_number="<?php echo isset($client->customers_vat_number) ? htmlspecialchars($client->customers_vat_number) : ''; ?>" 
                                                    data-initial="<?php echo isset($client->customers_initial) ? htmlspecialchars($client->customers_initial) : ''; ?>" 
                                                    data-title="<?php echo isset($client->customers_title) ? htmlspecialchars($client->customers_title) : ''; ?>" 

 
                                                    value="<?php echo isset($client->company) ? htmlspecialchars($client->company) : ''; ?> " <?php echo (isset($emp_returns) && $emp_returns->company_name == $client->company) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($client->company); ?>
                                                </option>
                                             <?php endforeach; ?>
                                    </select>
                                </div>
                                <input type="hidden" class="client_user_id" name="client_user_id" value="<?php echo isset($emp_returns) ? $emp_returns->client_user_id : ''; ?>" />
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="client_code">Client code</label>
                                    <input required="required" type="text" id="client_code" name="client_code" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->client_code : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="client_id">Client Id</label>
                                    <input required="required" type="text" id="client_id" name="client_id" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->client_code : ''; ?>">
                                </div>
                            </div>



                        </div> 


                        <div class="row pad_30"> 
                                
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company_number">Company Number</label>
                                    <input required="required" type="text" id="company_number" name="company_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->company_number : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vat_number">VAT Number</label>
                                    <input required="required" type="text" id="vat_number" name="vat_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->vat_number : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pay_period">Pay Period</label>
                                    <select id="pay_period" name="pay_period" class="custom-select">
                                        <option value="">Please select </option> 
                                            <?php foreach ($documentPeriods as $row): ?>
                                                <option data-period_combo="<?php echo $row->period_combo; ?>" data-financial_year="<?php echo $row->year; ?>" value="<?php echo $row->periods; ?>" <?php echo (isset($emp_returns) && $emp_returns->pay_period == $row->periods) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($row->periods); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="income_tax_number">Income Tax Number</label>
                                    <input required="required" type="text" id="income_tax_number" name="income_tax_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->income_tax_number : ''; ?>">
                                </div>
                            </div>            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="paye_number">PAYE No</label>
                                    <input   type="text" id="paye_number" name="paye_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->paye_number : ''; ?>">
                                </div>
                            </div>
                            
                                               
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sdl_number">SDL No</label>
                                    <input   type="text" id="sdl_number" name="sdl_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->sdl_number : ''; ?>">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="uif_number">UIF No.</label>
                                    <input   type="text" id="uif_number" name="uif_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->uif_number : ''; ?>">
                                </div>
                            </div>  
                               

                        </div>
                        <div class="divider_hr row "></div> 
                        <div class="row pad_10">

                           <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input required="required" type="text" id="title" name="title" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->title : ''; ?>">
                                    </div>
                            </div>
                            <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="initial">Initial</label>
                                        <input required="required" type="text" id="initial" name="initial" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->initial : ''; ?>">
                                    </div>
                            </div>
                            <div class="col-md-4 ">
                                    <div class="form-group">
                                        <label for="first_name">First Name</label>
                                        <input required="required" type="text" id="first_name" name="first_name" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->first_name : ''; ?>">
                                    </div>
                                </div>
                              <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="surname">Surname</label>
                                        <input required="required" type="text" id="surname" name="surname" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->surname : ''; ?>">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="position">Position</label>
                                        <input required="required" type="text" id="position" name="position" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->position : ''; ?>">
                                    </div>
                                </div>

                                

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="telephone_number">Office Number</label>
                                        <input required="required" type="text" id="telephone_number" name="telephone_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->telephone_number : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mobile_number">Mobile Number</label>
                                        <input required="required" type="text" id="mobile_number" name="mobile_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->mobile_number : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="whatspp_number">WhatsApp Number</label>
                                        <input required="required" type="text" id="whatspp_number" name="whatspp_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->whatspp_number : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="home_number">Home Number</label>
                                        <input required="required" type="text" id="home_number" name="home_number" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->home_number : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input required="required" type="text" id="email" name="email" class="normalize_input disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->email : ''; ?>">
                                        <!-- <div class="input-group-append">
                                            <span class="input-group-text phone_icon_text_" id="basic-addon1" style="padding: 14.2px !important;">
                                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                            </span>
                                        </div> -->
                                        <span id="email-validation-message" style="color: red; display: none;">Invalid email address</span>
                                    </div>
                                </div>
                        </div>
                        <div class="divider_hr row "></div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="period_label">
                                    <h3 class="header_bar" style="text-align:center">
                                        Payroll Tax
                                    </h3>
                                </div>
                            </div>
                        </div>                        
                        
                        <div class="row pad_30">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pay_liability">PAYE Liability</label>
                                    <input  required="required" placeholder="0.00"  type="text" step="any" id="pay_liability" name="pay_liability" class="normalize_input liability_field two-decimals" value="<?php echo isset($emp_returns) ? $emp_returns->pay_liability : '0.00'; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sdl_liability">SDL Liability</label>
                                    <input required="required" placeholder="0.00" type="text" step="any" id="sdl_liability" name="sdl_liability" class="normalize_input liability_field two-decimals" value="<?php echo isset($emp_returns) ? $emp_returns->sdl_liability : '0.00'; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="uif_liability">UIF Liability</label>
                                    <input required="required" placeholder="0.00" type="text" step="any" id="uif_liability" name="uif_liability" class="normalize_input liability_field two-decimals" value="<?php echo isset($emp_returns) ? $emp_returns->uif_liability : '0.00'; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="penalty">Penalty</label>
                                    <input  placeholder="0.00" type="text" step="any" id="penalty" name="penalty" class="normalize_input liability_field two-decimals" value="<?php echo isset($emp_returns) ? $emp_returns->penalty :'0.00'; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="interest">Interest</label>
                                    <input  placeholder="0.00" type="text" step="any" id="interest" name="interest" class="normalize_input liability_field two-decimals" value="<?php echo isset($emp_returns) ? $emp_returns->interest : '0.00'; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="other">Other</label>
                                    <input   placeholder="0.00" type="text" step="any" id="other" name="other" class="normalize_input liability_field two-decimals" value="<?php echo isset($emp_returns) ? $emp_returns->other : '0.00'; ?>">
                                </div>
                            </div>
                        </div>
                    
                        <div class="divider_hr row "></div>
                        <div class="row pad_10">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_reference">Reference Number</label>
                                    <input required="required" type="text" id="payment_reference" name="payment_reference" class="normalize_input disabled_input_field ref_no" value="<?php echo isset($emp_returns) ? $emp_returns->payment_reference : ''; ?>">
                                </div>
                            </div> 
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label for="payment_reference_number">Check Digit </label>
                                    <input  type="text" id="payment_reference_number " name="payment_reference_number" class="normalize_input pay_ref_num" value="<?php echo isset($emp_returns) ? $emp_returns->payment_reference_number : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="custom-select">
                                        <option value="">Please Select</option>
                                        <?php foreach ($statusOptions as $value => $label): ?>
                                            <option value="<?php echo $value; ?>" <?php echo (isset($emp_returns) && $emp_returns->active == $value) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                           </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_payable">Tax Payable</label>
                                    <input required="required" type="text" step="any" id="tax_payable" name="tax_payable" class="normalize_input two-decimals disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->tax_payable : ''; ?>">
                                </div>
                            </div>  
                        </div>

                        <div class="divider_hr row "></div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="period_label">
                                    <h3 class="header_bar" style="text-align:center">
                                        Payment
                                    </h3>
                                </div>
                            </div>
                        </div> 

                        <div class="row pad_30">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_date">Payment Date</label>
                                    <input   type="text"  id="payment_date" name="payment_date" class="normalize_input datepicker" value="<?php echo isset($emp_returns) ? $emp_returns->payment_date :''; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <select id="payment_type" name="payment_type" class="custom-select">
                                        <option value="">Please Select</option>
                                        <?php foreach ($paymentTypes as $value => $label): ?>
                                            <option value="<?php echo $value; ?>" <?php echo (isset($emp_returns) && $emp_returns->payment_type == $value) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_amount">Payment Amount</label>
                                    
                                    <input  type="text" step="any" id="payment_amount" name="payment_amount" class="normalize_input two-decimals" value="<?php echo isset($emp_returns) ? $emp_returns->payment_amount : '0.00'; ?>">
                                </div>
                            </div>  
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="balance_outstanding">Balance Outstanding</label>
                                    <input  type="text" step="any" id="balance_outstanding" name="balance_outstanding" class="normalize_input two-decimals disabled_input_field" value="<?php echo isset($emp_returns) ? $emp_returns->balance_outstanding : '0.00'; ?>">
                                </div>
                            </div>  

                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_date">Payment Amount</label>
                                    <input type="text"  id="payment_amount" name="payment_amount" class="normalize_input" value="<?php echo isset($emp_returns) ? $emp_returns->payment_amount :'0'; ?>">
                                </div>
                            </div> -->
                                                
                           

                        </div>
                        <div class="divider_hr row "></div>
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="period_label">
                                    <h3 class="header_bar" style="text-align:center">
                                        Upload Documents
                                    </h3>
                                </div>
                            </div>
                        </div> 

                        <div class="row pad_30">
                          
                           <?php
                                function get_file_icon_or_image($file_path, $default_img) {
                                    $extension = pathinfo($file_path, PATHINFO_EXTENSION);
                                    $icon_base_url = base_url('assets/images/documents_icons/');
                                    $icons = [
                                        'pdf' => $icon_base_url . 'pdfr.png',
                                        'doc' => $icon_base_url . 'doc.png',
                                        'docx' => $icon_base_url . 'doc.png'
                                    ];
                                    
                                    return isset($icons[$extension]) ? $icons[$extension] : (file_exists($file_path) ? base_url($file_path) : $default_img);
                                }

                                $emp201_return_img = isset($emp_returns->file_emp201_return) ? get_file_icon_or_image('uploads/emp201/' . $emp_returns->file_emp201_return, 'https://dummyimage.com/qvga') : 'https://dummyimage.com/qvga';
                                $emp201_statement_img = isset($emp_returns->file_emp201_statement) ? get_file_icon_or_image('uploads/emp201/' . $emp_returns->file_emp201_statement, 'https://dummyimage.com/qvga') : 'https://dummyimage.com/qvga';
                                ?>                     
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sars_emp201_return">SARS EMP 201 Return</label>
                                        <input type="file" id="sars_emp201_return" name="sars_emp201_return" class="normalize_input" accept="image/*,.pdf,.doc,.docx" onchange="previewFile('file_emp201_return', 'preview_emp201_return')">
                                        <img style="display:none" id="preview_emp201_return" class="preview" src="<?php echo $emp201_return_img; ?>" alt="Preview image for emp201 return">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sars_paye_statement">SARS PAYE Statement</label>
                                        <input type="file" id="sars_paye_statement" name="sars_paye_statement" class="normalize_input file_label" accept="image/*,.pdf,.doc,.docx" onchange="previewFile('file_emp201_statement', 'preview_emp201_statement')">
                                        <img  style="display:none"  id="preview_emp201_statement" class="preview" src="<?php echo $emp201_statement_img; ?>" alt="Preview image for emp201 statement">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="emp201_working_papers">EMP 201 Working Papers</label>
                                        <input type="file" id="emp201_working_papers" name="emp201_working_papers" class="normalize_input file_label" accept="image/*,.pdf,.doc,.docx" onchange="previewFile('file_emp201_statement', 'preview_emp201_statement')">
                                        <img  style="display:none"  id="preview_emp201_statement" class="preview" src="<?php echo $emp201_statement_img; ?>" alt="Preview image for emp201 statement">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="emp201_pack">EMP 201 Pack</label>
                                        <input type="file" id="emp201_pack" name="emp201_pack" class="normalize_input file_label" accept="image/*,.pdf,.doc,.docx" onchange="previewFile('file_emp201_statement', 'preview_emp201_statement')">
                                        <img  style="display:none"  id="preview_emp201_statement" class="preview" src="<?php echo $emp201_statement_img; ?>" alt="Preview image for emp201 statement">
                                    </div>
                                </div>

                        </div>

            
                    </div>
                    
                    <div class="panel-footer text-right tw-space-x-1" id="profile-save-section">
                    
                        <button class="btn btn-primary only-save save_btn" id="save_emp_return">
                            <?php echo _l('submit'); ?>
                        </button>
                    </div>
                    
                </div>
              
            <?php echo form_close(); ?>    
 
            </div>
        </div>

    </div>
<div style="display:none;">

    <div id="content">
        <p>Content</p>
    </div>
</div>
</div>

<?php init_tail(); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.0/dist/sweetalert2.min.css"> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.0/dist/sweetalert2.all.min.js"></script>
<script
  src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
  integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.1/html2pdf.bundle.min.js"></script>

 
<script>
const baseUrl = "<?php echo base_url(); ?>";
let companyName = "<?php echo $company_name; ?>";
 
let clients = <?php echo isset($documentClients) ? json_encode($documentClients) : null; ?>;
let documentOptions  = <?php echo isset($documentType) ? json_encode($documentType) : null; ?>;
let display_financial_period = "";
$('#client').selectize({
        placeholder: 'Please select',
        create: false,
        prepend: true,
        sortField: 'text',
        options: documentOptions.map(function(client) {
            return {
                text: client.company,
                value: client.company,
                data: {
                    userid: client.userid,
                    reg_number: client.customers_company_registration_number,
                    reg_date: client.customers_company_registration_date,
                    compliance: client.customers_compliance_status,
                    code: client.customers_customer_code,
                    paye_number: client.customers_paye_number,
                    sdl_number: client.customers_sdl_number,
                    uif_number: client.customers_uif_number,
                    tax_number: client.customers_personal_income_tax_number, 
                    sars_first_name: client.customers_first_name,
                    sars_surname: client.customers_last_name,
                    sars_business_number: client.customers_business_telephone_no,
                    sars_mobile_number: client.customers_mobile_number,
                    sars_email_address: client.customers_admin_email_address,
                    sars_customers_whatsapp_number: client.customers_whatsapp_number,
                    sars_home_number: client.customers_home_telephone_no,
                    sars_position: client.customers_sars_position,
                    vat_number: client.customers_vat_number,
                    title: client.customers_title,
                    initial: client.customers_initial,
                    
                   
                }
            };
        }),
        onInitialize: function() {
            // Make sure the initial value is selected
            var selectize = this;
            var initialValue = "<?php echo isset($emp_returns) ? htmlspecialchars($emp_returns->company_name) : ''; ?>";
            if (initialValue) {
                selectize.setValue(initialValue);
            }
        }
});
 
$('#payment_type').selectize({
    placeholder: 'Please select',
    create: false,
});
$('#status').selectize({
    placeholder: 'Please select',
    create: false,
});

$('#pay_period').selectize({
        create: false, 
        onInitialize: function() {
            var selectize = this;
            var currentPeriod = getCurrentPeriod();

            // Check if currentPeriod exists in options
            if (selectize.options) {
                var currentOption = Object.values(selectize.options).find(option => option.value === currentPeriod);
                if (currentOption) {
                    selectize.setValue(currentOption.value);
                    $("#financial_year").val(currentOption.financial_year); 
                    $("#period_combo").val(currentOption.period_combo); 
                } else if (Object.keys(selectize.options).length > 0) {
                    var firstOption = Object.keys(selectize.options)[0];
                    selectize.setValue(firstOption);
                   
                  //  financial_year
                }
            }
            console.log("_selectize.options", selectize.options)
            // Update the label initially
            updatePeriodLabel(selectize.getValue());

            // Add event listener to update label on change
            selectize.on('change', function(value) {
                updatePeriodLabel(value);
            });
        }
});

function previewFile(inputId, imgId) {
    const fileInput = document.getElementById(inputId);
    const previewImg = document.getElementById(imgId);
    const file = fileInput.files[0];
    const reader = new FileReader();
    const fileExtension = file ? file.name.split('.').pop().toLowerCase() : '';

    if (fileExtension === 'pdf') {
        previewImg.src = '<?php echo base_url('assets/images/documents_icons/pdfr.png'); ?>';
        previewImg.style.display = 'block';
    } else if (fileExtension === 'doc' || fileExtension === 'docx') {
        previewImg.src = '<?php echo base_url('assets/images/documents_icons/doc.png'); ?>';
        previewImg.style.display = 'block';
    } else if (file) {
        reader.onloadend = function () {
            previewImg.src = reader.result;
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.src = 'https://dummyimage.com/qvga';
    }
}


function updatePeriodLabel(value) {
    const periodParts = value.split(" ");
    const month = periodParts[0].toUpperCase();
    const year = periodParts[1];
    const yearMonthNumber = year + String(new Date(Date.parse(month +" 1, " + year)).getMonth() + 1).padStart(2, '0');
    let peridHeading =  `${month} ${year} - ${yearMonthNumber}`;
     
    document.querySelector('.period_heading').innerText = value;
    $(".year_month_number").val(yearMonthNumber);
    $("#financial_year").val(year);
     
    let paye_number = $("#paye_number").val();
  
    let periodCombo = $("#period_combo").val();
    let payment_reference = paye_number+ " LC "+ periodCombo;
    $("#payment_reference").val(payment_reference);
}
 
function getCurrentPeriod() {
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const now = new Date();
    const month = months[now.getMonth()];
    const year = now.getFullYear();
    return `${month} ${year}`;
}

   
$(document).ready(function() {
   
    let today = new Date(); 
    $("#document_requested_date").datepicker({
        dateFormat: "DD, dd M yy",
        altField: "#document_requested_date",
        altFormat: "DD, dd M yy",
        defaultDate: today,
        onSelect: function(dateText) {
            // Format the selected date
            let selectedDate = $(this).datepicker("getDate");
            $(this).val($.datepicker.formatDate("DD, dd M yy", selectedDate)); 
        }
    }).datepicker("setDate", today); // Set initial date to today

    $("#approved_date").datepicker({
        dateFormat: "DD, dd M yy",
        altField: "#approved_date",
        altFormat: "DD, dd M yy",
        defaultDate: today,
        onSelect: function(dateText) {
            $(this).val($.datepicker.formatDate("DD, dd M yy", $(this).datepicker("getDate")));
        }
    }).datepicker("setDate", today); // Set initial date to today
    
 
    $('#client').change(function() {
        let selectedOption = $(this)[0].selectize.options[$(this).val()];
        let clientCode = selectedOption.code; 
        let paye_number = selectedOption.paye_number;
        let sdl_number = selectedOption.sdl_number;
        let uif_number = selectedOption.uif_number;
        let tax_number = selectedOption.tax_number;
        let userid = selectedOption.userid;
        
        let sars_first_name = selectedOption.sars_first_name;
        let sars_surname = selectedOption.sars_surname;
        let sars_business_number = selectedOption.sars_business_number;
        let sars_mobile_number = selectedOption.sars_mobile_number;
        let sars_email_address = selectedOption.sars_email_address;
        let sars_home_number = selectedOption.sars_home_number;
        let vat_number = selectedOption.vat_number;
        let sars_whatsapp_number = selectedOption.sars_customers_whatsapp_number;
        let sars_position = selectedOption.sars_position;
        let initial = selectedOption.initial;
        let title = selectedOption.title;
        
        let yearMonthNumber = $(".year_month_number").val(); 
        let payPeriodSelectize = $('#pay_period')[0].selectize;
        let selectedValue = payPeriodSelectize.getValue(); 
        let selectedOptionPeriod = payPeriodSelectize.options[selectedValue]; 
        let periodCombo = selectedOptionPeriod.period_combo; 
        let payment_reference = paye_number+ " LC "+ periodCombo; 
        $("#client_code").val(clientCode);
        $("#client_id").val(userid);
        let reg_number = selectedOption ? selectedOption.reg_number : '';
        $("#company_number").val(reg_number);
        $("#paye_number").val(paye_number);
        $("#income_tax_number").val(tax_number);
        $("#sdl_number").val(sdl_number);
        $(".client_user_id").val(userid);

        $("#uif_number").val(uif_number);
        $("#first_name").val(sars_first_name);
        $("#surname").val(sars_surname);
        $("#position").val(sars_position);
        $("#home_number").val(sars_home_number);
        $("#vat_number").val(vat_number);
        $("#title").val(title);
        $("#initial").val(initial);
        $("#whatspp_number").val(sars_whatsapp_number);
        $("#telephone_number").val(sars_business_number);
        $("#mobile_number").val(sars_mobile_number);
        $("#email").val(sars_email_address);

        $("#payment_reference").val(payment_reference);
    }); 

    $('#pay_period').change(function() {
        let selectedOption = $(this)[0].selectize.options[$(this).val()];
        $("#financial_year").val(selectedOption.financial_year); 
        $("#period_combo").val(selectedOption.period_combo);
        let paye_number = $("#paye_number").val();
        let payment_reference = paye_number+ " LC "+ selectedOption.period_combo; 
        $("#payment_reference").val(payment_reference);
        
        
    });
    
    function calculateTaxPayable() {
        let sum = 0;
        document.querySelectorAll('.liability_field').forEach(input => {
            // Remove spaces from the input value, parse it as a float, and add it to the sum
            let value = input.value.replace(/ /g, ''); 
            sum += parseFloat(value) || 0;
        });

        // Format the sum with a space as a thousand separator
        let formattedSum = sum.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

        // Set the formatted value in the tax_payable field
        document.getElementById('tax_payable').value = formattedSum;
    }


    document.querySelectorAll('.liability_field').forEach(input => {
        input.addEventListener('input', calculateTaxPayable);
    }); 
    // Call the function initially to set the value based on the existing data
    calculateTaxPayable();
    

    // Start of Numeric Input Formatting 


    $('.two-decimals').each(function() {
    var $input = $(this);

    function formatValue(value) {
        // Remove invalid characters except decimal points
        value = value.replace(/[^0-9.]/g, '');

        // Ensure only one decimal point
        var parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }

        // Limit to two decimal places
        if (parts.length === 2) {
            parts[1] = parts[1].slice(0, 2);
            value = parts.join('.');
        }

        // Format with space as thousand separators
        var integerPart = parts[0];
        var formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

        if (parts.length === 2) {
            return formattedIntegerPart + '.' + parts[1];
        }

        return formattedIntegerPart;
    }

    $input.on('input', function() {
        var value = $input.val();
        var formattedValue = formatValue(value);

        // Update the input value
        $input.val(formattedValue);
    });

    $input.on('blur', function() {
        var value = $input.val().replace(/ /g, ''); // Remove spaces before parsing
        if (value !== '' && !isNaN(value)) {
            $input.val(parseFloat(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
        }
    });
});


    

    // End of number formatting


    $('.pay_ref_num').each(function() {
        var $input = $(this);

        $input.on('input', function() {
            var value = $input.val();

            // Remove all non-numeric characters and limit to two digits
            value = value.replace(/[^0-9]/g, '').slice(0, 2);

            // Update the input value
            $input.val(value);

            // Clear custom validity
            $input[0].setCustomValidity('');
        });

        $input.on('blur', function() {
            // Ensure the field is not empty on blur
            if ($input.val() === '') {
                $input[0].setCustomValidity('This field is required');
            } else {
                $input[0].setCustomValidity('');
            }
        });
    });

    // $('#payment_amount').on('input', function() {
    //         this.value = this.value.replace(/[^0-9]/g, '');
    // });
 
 
});

function validateEmail(email) { 
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
document.getElementById('email').addEventListener('change', function() {
    const email = this.value;
    const validationMessage = document.getElementById('email-validation-message');

    if (!validateEmail(email)) {
        validationMessage.style.display = 'inline';
    } else {
        validationMessage.style.display = 'none';
    }
});



 

 
</script>
</body>

</html>
