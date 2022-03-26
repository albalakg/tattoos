<?php

namespace App\Domain\Users\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Users\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        $roles = [
            ['id' => Role::NORMAL, 'name' => 'Normal', 'created_at' => now()],
            ['id' => Role::ADMIN, 'name' => 'Admin', 'created_at' => now()],
        ];
        Role::insert($roles);
    }
}
