<?php

return [
    'name' => 'JobCards',

    // Available client_master fields that can be configured per job type
    'available_client_fields' => [
        'client_code'           => 'Client Code',
        'company_name'          => 'Company Name',
        'trading_name'          => 'Trading Name',
        'company_reg_number'    => 'Company Registration Number',
        'company_type'          => 'Company Type',
        'company_reg_date'      => 'Company Registration Date',
        'bizportal_number'      => 'BizPortal Number',
        'financial_year_end'    => 'Financial Year End',
        'tax_number'            => 'Income Tax Number',
        'tax_reg_date'          => 'Tax Registration Date',
        'vat_number'            => 'VAT Number',
        'vat_reg_date'          => 'VAT Registration Date',
        'vat_return_cycle'      => 'VAT Return Cycle',
        'paye_number'           => 'PAYE Number',
        'sdl_number'            => 'SDL Number',
        'uif_number'            => 'UIF Number',
        'dept_labour_number'    => 'Dept of Labour Number',
        'wca_coida_number'      => 'WCA/COIDA Number',
        'payroll_liability_date'=> 'Payroll Liability Date',
        'email'                 => 'Email',
        'phone_business'        => 'Business Phone',
        'phone_mobile'          => 'Mobile Phone',
        'phone_whatsapp'        => 'WhatsApp',
        'website'               => 'Website',
        'bank_account_holder'   => 'Bank Account Holder',
        'bank_account_number'   => 'Bank Account Number',
        'bank_name'             => 'Bank Name',
        'bank_branch_code'      => 'Bank Branch Code',
        'bank_account_type'     => 'Bank Account Type',
        'sars_login'            => 'SARS eFiling Login',
        'sars_rep_first_name'   => 'SARS Rep First Name',
        'sars_rep_surname'      => 'SARS Rep Surname',
        'sars_rep_id_number'    => 'SARS Rep ID Number',
        'sars_rep_tax_number'   => 'SARS Rep Tax Number',
        'director_first_name'   => 'Director First Name',
        'director_surname'      => 'Director Surname',
        'director_id_number'    => 'Director ID Number',
        'number_of_directors'   => 'Number of Directors',
        'number_of_shares'      => 'Number of Shares',
        'cipc_annual_returns'   => 'CIPC Annual Returns',
    ],

    // Job card statuses
    'statuses' => [
        'draft'       => ['label' => 'Draft',       'color' => '#6c757d', 'icon' => 'fa-file'],
        'in_progress' => ['label' => 'In Progress', 'color' => '#007bff', 'icon' => 'fa-spinner'],
        'review'      => ['label' => 'Under Review', 'color' => '#ffc107', 'icon' => 'fa-eye'],
        'completed'   => ['label' => 'Completed',   'color' => '#28a745', 'icon' => 'fa-check-circle'],
        'submitted'   => ['label' => 'Submitted',   'color' => '#17a2b8', 'icon' => 'fa-paper-plane'],
        'cancelled'   => ['label' => 'Cancelled',   'color' => '#dc3545', 'icon' => 'fa-times-circle'],
    ],

    // Priority levels
    'priorities' => [
        'low'    => ['label' => 'Low',    'color' => '#6c757d'],
        'normal' => ['label' => 'Normal', 'color' => '#007bff'],
        'high'   => ['label' => 'High',   'color' => '#ffc107'],
        'urgent' => ['label' => 'Urgent', 'color' => '#dc3545'],
    ],

    // Job code prefix
    'code_prefix' => 'JC',

    // Documents storage path (relative to storage)
    'documents_path' => 'job_cards/documents',
    'packs_path'     => 'job_cards/packs',
];
