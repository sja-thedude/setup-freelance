<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class AlterUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = User::find(User::SUPER_ADMIN_ID);
        
        if(!empty($superAdmin)) {
            $superAdmin->role_id = Role::ROLE_ADMIN;
            $superAdmin->save();
        }
    }
}
