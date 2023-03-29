<?php

namespace App\Domain\Users\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Users\Models\Role;
use App\Domain\Users\Models\User;
use App\Domain\Helpers\StatusService;
use App\Domain\Users\Services\UserService;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_service = new UserService();
        
        $users = [
            [
                'role'          => 'admin',
                'first_name'    => 'Goldens',
                'last_name'     => 'Academy',
                'email'         => 'demo@goldens.com',
                'password'      => 'goldensDemoPa55',
                'created_at'    => now(),
                'status'        => StatusService::ACTIVE,
            ],
        ];
        
        foreach($users AS $user_data)
        {
            if(!User::where('email', $user_data['email'])->exists()) {
                $user = $user_service->createUserByAdmin($user_data, 0);
                $user_service->activateUser($user->id);
            }
        }
    }
}
