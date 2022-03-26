<?php

namespace App\Domain\Content\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\General\Models\LuContentType;

class LuContentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LuContentType::truncate();
        $data = [
            ['name' => 'Course',        'created_at' => now()],
            ['name' => 'Course Area',   'created_at' => now()],
            ['name' => 'Lesson',        'created_at' => now()],
        ];
        LuContentType::insert($data);
        
    }
}
