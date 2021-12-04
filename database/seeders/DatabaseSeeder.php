<?php

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\LuSupplierTypeSeeder;
use Illuminate\Database\Eloquent\Model;
use Database\Seeders\LuContentTypeSeeder;

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
        $this->call(LuSupplierTypeSeeder::class);

        Model::reguard();
    }
}
