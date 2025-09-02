<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use Carbon\Carbon;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
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
        ]);
    }
}
