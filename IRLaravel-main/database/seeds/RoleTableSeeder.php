<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id' => Role::ROLE_ADMIN,
                'platform' => Role::PLATFORM_BACKOFFICE,
                'active' => true,
                'name' => 'Account Manager'
            ],
            [
                'id' => Role::ROLE_MANAGER,
                'platform' => Role::PLATFORM_MANAGER,
                'active' => true,
                'name' => 'Manager'
            ],
            [
                'id' => Role::ROLE_USER,
                'platform' => Role::PLATFORM_FRONTEND,
                'active' => true,
                'name' => 'Client'
            ]
        ];

        foreach ($roles as $roleData) {
            // Insert only if role with this id doesn't exist
            Role::updateOrCreate(
                ['id' => $roleData['id']], // unique key to check
                $roleData                 // values to insert/update
            );
        }
    }
}