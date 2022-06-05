<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/mail/email-confirmation', function() {
    $data = [
        'name'  => 'עדן',
        'token' => 'ASD3FV32f233fdfsadfdsf'
    ];
    return view('mails.auth.emailConfirmation', ['data' => $data]);
});

Route::get('/', function () {
    return view('welcome');
});
