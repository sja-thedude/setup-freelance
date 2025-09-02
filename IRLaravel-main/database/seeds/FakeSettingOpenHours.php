<?php

use App\Models\SettingOpenHour;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FakeSettingOpenHours extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $workspaces = Workspace::select('id')->get();
        $types = SettingOpenHour::getTypes();
        $timestamp = Carbon::now();

        $workspaces->each(function (Workspace $workspace) use ($types, $timestamp) {
            foreach ($types as $type => $label) {
                $workspace->settingOpenHours()->updateOrCreate([
                    'workspace_id' => $workspace->id,
                    'type' => $type,
                ], [
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'active' => rand(0, 1) == 1,
                ]);
            }
        });
    }
}
