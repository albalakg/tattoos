<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel/laravel.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel/laravel.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'login' => [
            'driver' => 'daily',
            'path' => storage_path('logs/login/login.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'mail' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mail/mail.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'users' => [
            'driver' => 'daily',
            'path' => storage_path('logs/users/users.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'files' => [
            'driver' => 'daily',
            'path' => storage_path('logs/files/files.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'custom' => [
            'driver' => 'daily',
            'path' => storage_path('logs/custom/custom.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'logsBp' => [
            'driver' => 'daily',
            'path' => storage_path('logs/logsBp/logsBp.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'content' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/content.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        // TODO: DELETE AFTER WEDDING
        'wedding' => [
            'driver' => 'daily',
            'path' => storage_path('logs/wedding/wedding.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'videos' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/videos/videos.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'challenges' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/challenges/challenges.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'courseCategories' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/courseCategories/courseCategories.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'courseAreas' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/courseAreas/courseAreas.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'trainers' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/trainers/trainers.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'courseLessons' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/courseLessons/courseLessons.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'trainingOptions' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/trainingOptions/trainingOptions.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'marketingToken' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/marketingToken/marketingToken.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'userFavorites' => [
            'driver' => 'daily',
            'path' => storage_path('logs/userFavorites/userFavorites.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'userCourses' => [
            'driver' => 'daily',
            'path' => storage_path('logs/userCourses/userCourses.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'userSchedules' => [
            'driver' => 'daily',
            'path' => storage_path('logs/userSchedules/userSchedules.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'CompleteUserCourseService' => [
            'driver' => 'daily',
            'path' => storage_path('logs/CompleteUserCourseService/CompleteUserCourseService.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'coupons' => [
            'driver' => 'daily',
            'path' => storage_path('logs/coupons/coupons.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'orders' => [
            'driver' => 'daily',
            'path' => storage_path('logs/orders/orders.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'policies' => [
            'driver' => 'daily',
            'path' => storage_path('logs/policies/policies.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'payment' => [
            'driver' => 'daily',
            'path' => storage_path('logs/payment/payment.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'support' => [
            'driver' => 'daily',
            'path' => storage_path('logs/support/support.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'courses' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/courses/courses.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'courseRecommendations' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/courseRecommendations/courseRecommendations.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'tests' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/tests/tests.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'skills' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/skills/skills.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'equipment' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/equipment/equipment.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'terms' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/terms/terms.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'files' => [
            'driver' => 'daily',
            'path' => storage_path('logs/content/files/files.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'auth' => [
            'driver' => 'daily',
            'path' => storage_path('logs/auth/auth.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'scheduler' => [
            'driver' => 'daily',
            'path' => storage_path('logs/scheduler/scheduler.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'mail' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mails/mail.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'global' => [
            'driver' => 'daily',
            'path' => storage_path('logs/global/global.log'),
            'level' => 'debug',
            'days' => 1,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel/laravel.log'),
            'level' => 'debug',
            'days' => 1,
        ],
    ],

];
