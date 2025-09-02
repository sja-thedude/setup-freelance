<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    protected $limit = [
        'group' => 20,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create groups and save them to the database
        $groups = factory(App\Models\Group::class, array_get($this->limit, 'group', 10))->create();
    }
}
