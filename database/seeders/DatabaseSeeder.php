<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Users\Seeders\RoleSeeder;
use App\Domain\Users\Seeders\UserSeeder;
use App\Domain\Users\Seeders\LuEmailsTypesSeeder;
use App\Domain\Content\Seeders\LuContentTypeSeeder;
use App\Domain\Payment\Seeders\LuSupplierTypeSeeder;
use App\Domain\Support\Seeders\SupportCategorySeeder;

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
        $this->call(UserSeeder::class);
        $this->call(LuContentTypeSeeder::class);
        $this->call(LuSupplierTypeSeeder::class);
        $this->call(LuEmailsTypesSeeder::class);
        $this->call(SupportCategorySeeder::class);

        Model::reguard();
    }
}
