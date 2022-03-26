<?php

namespace App\Domain\Support\Seeders;

use App\Domain\Helpers\StatusService;
use App\Domain\Support\Models\SupportCategory;
use Illuminate\Database\Seeder;

class SupportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SupportCategory::truncate();
        $data = [
            [
                'name'          => 'Contact Us',
                'status'        => StatusService::ACTIVE,
                'created_at'    => now(),
                'created_by'    => 0
            ],
        ];
        SupportCategory::insert($data);
    }
}
