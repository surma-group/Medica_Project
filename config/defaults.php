<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency ID to use when creating new records.
    | You can set this in your .env file.
    |
    */

    'currency_id' => env('DEFAULT_CURRENCY_ID', 16),


    /*
    |--------------------------------------------------------------------------
    | Default Timezone
    |--------------------------------------------------------------------------
    |
    | This is the default timezone ID to use when creating new records.
    | You can set this in your .env file.
    |
    */

    'timezone_id' => env('DEFAULT_TIMEZONE_ID', 45),


    /*
    |--------------------------------------------------------------------------
    | Default Status
    |--------------------------------------------------------------------------
    |
    | The default status for new records: 1 = Active, 0 = Inactive
    | You can set this in your .env file.
    |
    */

    'status' => env('DEFAULT_STATUS', 1),

];
