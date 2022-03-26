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
            ['name' => 'Support Ticket Created',            'created_at' => now()],
            ['name' => 'Support Ticket Message Created',    'created_at' => now()],
        ];
        LuEmailType::insert($data);
        
    }
}
