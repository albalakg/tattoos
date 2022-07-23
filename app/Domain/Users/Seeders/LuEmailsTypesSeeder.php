<?php

namespace App\Domain\Users\Seeders;

use App\Domain\Emails\Models\LuEmailType;
use Illuminate\Database\Seeder;

class LuEmailsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LuEmailType::truncate();
        $data = [
            ['id' => LuEmailType::SUPPORT_TICKET_EMAIL,         'name' => LuEmailType::SUPPORT_TICKET_EMAIL_TEXT,           'created_at' => now()],
            ['id' => LuEmailType::SUPPORT_TICKET_MESSAGE_EMAIL, 'name' => LuEmailType::SUPPORT_TICKET_MESSAGE_EMAIL_TEXT,   'created_at' => now()],
            ['id' => LuEmailType::FORGOT_PASSWORD_EMAIL,        'name' => LuEmailType::FORGOT_PASSWORD_EMAIL_TEXT,          'created_at' => now()],
            ['id' => LuEmailType::EMAIL_VERIFICATION_EMAIL,     'name' => LuEmailType::EMAIL_VERIFICATION_EMAIL_TEXT,       'created_at' => now()],
            ['id' => LuEmailType::APPLICATION_ERROR_EMAIL,      'name' => LuEmailType::APPLICATION_ERROR_EMAIL_TEXT,        'created_at' => now()],
            ['id' => LuEmailType::ORDER_STATUS_UPDATE_EMAIL,    'name' => LuEmailType::ORDER_STATUS_UPDATE_EMAIL_TEXT,      'created_at' => now()],
            ['id' => LuEmailType::ADD_COURSE_TO_USER_EMAIL,     'name' => LuEmailType::ADD_COURSE_TO_USER_EMAIL_TEXT,       'created_at' => now()],
            ['id' => LuEmailType::DELETE_USER_REQUEST_EMAIL,    'name' => LuEmailType::DELETE_USER_REQUEST_EMAIL_TEXT,      'created_at' => now()],
            ['id' => LuEmailType::UPDATE_EMAIL_REQUEST_EMAIL,   'name' => LuEmailType::UPDATE_EMAIL_REQUEST_EMAIL_TEXT,     'created_at' => now()],
        ];
        LuEmailType::insert($data);
        
    }
}
