<?php

return [

    /*
    | The list of all the providers and their configurations
    |
    */

    'payplus' => [
        
        'address'       => env('PAYPLUS_ADDRESS'),
        'api_key'       => env('PAYPLUS_API_KEY'),
        'secret_key'    => env('PAYPLUS_SECRET_KEY'),

    ],


    
    'paypal' => [
        
        'address'   => env('PAYPAL_ADDRESS'),
        'token'     => env('PAYPAL_TOKEN'),

    ],

];
