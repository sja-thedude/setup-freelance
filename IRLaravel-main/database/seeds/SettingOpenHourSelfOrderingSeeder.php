<?php

use App\Helpers\OrderHelper;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class SettingOpenHourSelfOrderingSeeder extends Seeder
{
    public function run()
    {
        $workspaces = Workspace::select('id')->get();

        $workspaces->each(function (Workspace $workspace) {
            $ouSelfOrdering = $workspace->settingOpenHours()
                ->where('workspace_id', $workspace->id)
                ->where('type', OrderHelper::TYPE_SELF_ORDERING)
                ->first();

            if (empty($ouSelfOrdering)) {
                // Create new if not exists
                $workspace->settingOpenHours()->create([
                    'workspace_id' => $workspace->id,
                    'type' => OrderHelper::TYPE_SELF_ORDERING,
                    'active' => false,
                ]);
            }
        });
    }
}
