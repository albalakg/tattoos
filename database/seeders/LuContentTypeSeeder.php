<?php

namespace Database\Seeders;

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
            ['name' => 'Course', 'created_at' => now()],
        ];
        LuContentType::insert($data);
        
    }
}
