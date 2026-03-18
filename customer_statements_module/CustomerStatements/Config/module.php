<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Customer Statements Module
    |--------------------------------------------------------------------------
    |
    | Configuration for the Customer Statements module.
    | This module generates customer account statements from existing
    | invoices and payments data in the GrowCRM system.
    |
    */

    'name' => 'CustomerStatements',
    'version' => '1.0.0',
    'description' => 'Customer Statement of Account generation, PDF export, and email delivery.',

    // Route prefix for all statement routes
    'route_prefix' => 'statements',

    // Middleware applied to all routes
    'middleware' => ['web', 'auth'],

    // Invoice statuses to exclude from statements
    'excluded_invoice_statuses' => [
        1, // Draft
        6, // Cancelled
    ],

    // Invoice statuses considered unpaid (for aging calculation)
    'unpaid_invoice_statuses' => [
        2, // Due
        3, // Overdue
        4, // Partial
    ],

    // Aging buckets (in days)
    'aging_buckets' => [
        'current'  => [0, 30],
        '30_days'  => [31, 60],
        '60_days'  => [61, 90],
        '90_plus'  => [91, null], // null = no upper limit
    ],

    // PDF settings
    'pdf' => [
        'paper_size' => 'A4',
        'orientation' => 'portrait',
        'logo_path' => 'storage/logos/app/cims_inv_logo.png',
        'banking_image_path' => 'storage/logos/app/banking_atp.png',
    ],

    // Custom field ID for Client Code
    'client_code_field_id' => 38,

];
