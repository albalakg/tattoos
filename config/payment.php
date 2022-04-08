<?php

return [

    /*
    | The list of all the providers and their configurations
    |
    */

    'payplus' => [
        
        'address' => env('PAYPLUS_ADDRESS'),
        'token' => env('PAYPLUS_TOKEN'),

    ],


    
    'paypal' => [
        
        'address' => env('PAYPAL_ADDRESS'),
        'token' => env('PAYPAL_TOKEN'),

    ],

];
