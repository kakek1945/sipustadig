<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Library Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your library as it will be displayed throughout
    | the application.
    |
    */
    'name' => env('APP_NAME', 'Sipustadig'),

    /*
    |--------------------------------------------------------------------------
    | Loan Defaults
    |--------------------------------------------------------------------------
    |
    | Default values for loan settings.
    |
    */
    'loan' => [
        'max_borrow_days' => env('LOAN_MAX_DAYS', 7),
        'max_books' => env('LOAN_MAX_BOOKS', 3),
        'max_extensions' => env('LOAN_MAX_EXTENSIONS', 2),
        'max_overdue_days' => env('LOAN_MAX_OVERDUE_DAYS', 14),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fine Defaults
    |--------------------------------------------------------------------------
    |
    | Default values for fine calculation.
    |
    */
    'fine' => [
        'daily_rate' => env('FINE_DAILY_RATE', 1000),
        'max_amount' => env('FINE_MAX_AMOUNT', 50000),
        'waiver_requires_reason' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Member Defaults
    |--------------------------------------------------------------------------
    |
    | Default values for member settings.
    |
    */
    'member' => [
        'max_unpaid_fines' => env('MEMBER_MAX_UNPAID_FINES', 50000),
        'card_validity_years' => env('MEMBER_CARD_VALIDITY_YEARS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Barcode/QR Settings
    |--------------------------------------------------------------------------
    |
    | Settings for barcode and QR code generation.
    |
    */
    'barcode' => [
        'prefix' => 'BC',
        'type' => 'qr', // qr, code128, ean13
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for report generation.
    |
    */
    'report' => [
        'date_format' => 'd/m/Y',
        'datetime_format' => 'd/m/Y H:i',
        'paper_size' => 'A4',
        'orientation' => 'portrait', // portrait, landscape
    ],

];
