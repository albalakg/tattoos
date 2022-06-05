<?php

use Illuminate\Support\Facades\Route;

Route::get('/email-confirmation', function() {
    $data = [
        'name'  => 'עדן',
        'token' => 'ASD3FV32f233fdfsadfdsf'
    ];
    return view('mails.auth.emailConfirmation', ['data' => $data]);
});