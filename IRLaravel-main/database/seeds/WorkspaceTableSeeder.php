<?php

use Illuminate\Database\Seeder;
use App\Models\Workspace;

class WorkspaceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Init default workspace
        /*Workspace::updateOrcreate([
            'id' => 1,
        ], [
            'id' => 1,
            'user_id' => 1,
            'name' => 'Default',
            'active' => Workspace::IS_YES,
        ]);*/
    }
}
