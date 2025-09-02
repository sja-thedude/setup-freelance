<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Workspace;

class UpdateSlugWorkspaceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $workspaces = Workspace::whereNull('slug')->withTrashed()->get();

        if(!$workspaces->isEmpty()) {
            foreach($workspaces as $workspace) {
                $workspace->slug = str_slug($workspace->name, '-');
                $workspace->save();
            }
        }
    }
}
