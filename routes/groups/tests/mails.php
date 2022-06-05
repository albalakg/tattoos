<?php

use Illuminate\Support\Facades\Route;

Route::get('/email-confirmation', function() {
    $data = [
        'name'  => 'עדן',
        'token' => 'ASD3FV32f233fdfsadfdsf'
    ];
    return view('mails.auth.emailConfirmation', ['data' => $data]);
});

Route::get('/forgot-password', function() {
    $data = [
        'name'  => 'עדן',
        'token' => 'ASD3FV32f233fdfsadfdsf'
    ];
    return view('mails.auth.forgotPassword', ['data' => $data]);
});

Route::get('/email-confirmation', function() {
    $data = [
        'name'  => 'עדן',
        'token' => 'ASD3FV32f233fdfsadfdsf'
    ];
    return view('mails.profile.updateEmailRequest', ['data' => $data]);
});