<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Pagination
    |--------------------------------------------------------------------------
    |
    | Default pagination count for index methods
    |
    */
    'default_pagination' => 15,

    /*
    |--------------------------------------------------------------------------
    | API Response Settings
    |--------------------------------------------------------------------------
    |
    | Settings for API response formatting
    |
    */
    'api_response' => [
        'success_code' => 200,
        'error_code' => 400,
        'not_found_code' => 404,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Binding Settings
    |--------------------------------------------------------------------------
    |
    | Whether to automatically bind interfaces to implementations
    |
    */
    'auto_binding' => true,

    /*
    |--------------------------------------------------------------------------
    | File Generation Settings
    |--------------------------------------------------------------------------
    |
    | Settings for file generation behavior
    |
    */
    'file_generation' => [
        'overwrite_existing' => false,
        'create_directories' => true,
    ],
];
