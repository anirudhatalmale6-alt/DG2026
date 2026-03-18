<?php

return [
    'name' => 'CustomerAgedAnalysis',
    'version' => '1.0.0',
    'description' => 'Customer Aged Analysis report - summary and detailed views of outstanding balances by aging bucket.',
    'route_prefix' => 'aged-analysis',
    'middleware' => ['web', 'auth'],
    'excluded_invoice_statuses' => [1, 6], // Draft, Cancelled
    'unpaid_invoice_statuses' => [2, 3, 4], // Due, Overdue, Partial
    'aging_buckets' => [
        'current'  => [0, 30],
        '30_days'  => [31, 60],
        '60_days'  => [61, 90],
        '90_plus'  => [91, null],
    ],
    'client_code_field_id' => 38,
];
