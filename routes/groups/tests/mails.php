<?php

use Illuminate\Support\Carbon;
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

Route::get('/update-email', function() {
    $data = [
        'name'  => 'עדן',
        'token' => 'ASD3FV32f233fdfsadfdsf'
    ];
    return view('mails.profile.updateEmailRequest', ['data' => $data]);
});

Route::get('/delete-account-request', function() {
    $data = [
        'name'  => 'עדן',
        'token' => 'ASD3FV32f233fdfsadfdsf'
    ];
    return view('mails.profile.deleteAccountRequest', ['data' => $data]);
});

Route::get('/new-course', function() {
    $date = new Carbon;
    $data = [
        'name'          => 'עדן',
        'course_name'   => 'כדורגל בסיסי',
        'course_id'     => 1,
        'end_at'        => $date->addDays(364)->format('d/m/Y'),
    ];
    return view('mails.user.newCourse', ['data' => $data]);
});

Route::get('/course-almost-expired', function() {
    $date = new Carbon;
    $data = [
        'name'          => 'עדן',
        'course_name'   => 'כדורגל בסיסי',
        'course_id'     => 1,
        'end_at'        => $date->addDays(364)->format('d/m/Y'),
    ];
    return view('mails.user.courseAlmostExpired', ['data' => $data]);
});

Route::get('/activity-reminder', function() {
    $date = new Carbon;
    $data = [
        'name'          => 'עדן',
        'course_id'     => 1,
        'lesson_id'     => 1,
    ];
    return view('mails.user.activityReminder', ['data' => $data]);
});

Route::get('/course-completed', function() {
    $date = new Carbon;
    $data = [
        'name'          => 'עדן',
        'course_name'   => 'כדורגל בסיסי',
        'end_at'        => $date->addDays(364)->format('d/m/Y'),
    ];
    return view('mails.user.userCompletedCourse', ['data' => $data]);
});

Route::get('/order-completed', function() {
    $date = new Carbon;
    $data = [
        'name'          => 'עדן',
        'course_name'   => 'כדורגל בסיסי',
        'order_number'  => 'ON32902347',
        'price'         => 1500,
        'course_id'     => 1,
        'end_at'        => $date->addDays(364)->format('d/m/Y'),
    ];
    return view('mails.orders.orderCompleted', ['data' => $data]);
});