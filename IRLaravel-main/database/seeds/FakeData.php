<?php

use Illuminate\Database\Seeder;

class FakeData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // 1) Create groups and save them to the database
        $this->call(GroupsTableSeeder::class);

        //
        // 1) Create countries and save them to the database
        // 2) Create restaurant categories and save them to the database
        // 3) Create workspaces and save them to the database
        $this->call(FakeRestaurantCategory::class);

        //
        // 1) Create restaurant categories and save them to the database
        // 2) Create workspaces and save them to the database
        $this->call(FakeProductFavorite::class);

        //
        // 1) Create groups and save them to the database
        $this->call(FakeCoupons::class);
    }
}
