<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'role_id' => Role::ROLE_ADMIN,
                'email' => 'admin@yopmail.com',
                'password' => bcrypt('123456789'),
                'is_super_admin' => true,
                'is_admin' => true,
                'is_verified' => true,
                'platform' => User::PLATFORM_BACKOFFICE,
                'name' => 'Super Admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'birthday' => Carbon::now()
            ],
        ]);
    }
}
