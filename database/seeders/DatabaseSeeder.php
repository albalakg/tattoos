<?php

use Database\Seeders\LuContentTypeSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Model::unguard();

        $this->call(RoleSeeder::class);
        $this->call(LuContentTypeSeeder::class);

        Model::reguard();
    }
}
