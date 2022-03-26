<?php

namespace App\Domain\Payment\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Payment\Models\LuSupplierType;

class LuSupplierTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LuSupplierType::truncate();
        $data = [
            ['name' => 'ContentGuard',      'created_at' => now()],
            ['name' => 'Paypal',            'created_at' => now()],
        ];
        LuSupplierType::insert($data);
        
    }
}
