<?php

return [
    'name' => 'CIMSAppointments',

    /*
    |--------------------------------------------------------------------------
    | Grow CRM Database Connection
    |--------------------------------------------------------------------------
    | The database connection name for Grow CRM. This should match the
    | connection defined in config/database.php.
    */
    'growcrm_connection' => env('GROWCRM_DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Client Code Custom Field ID
    |--------------------------------------------------------------------------
    | The field ID in Grow CRM's tblcustomfieldsvalues that stores the
    | client_code linking clients to client_master.
    */
    'client_code_field_id' => env('CIMS_CLIENT_CODE_FIELD_ID', 38),
];
