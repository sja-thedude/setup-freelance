<?php

use App\Models\WorkspaceExtra;
use App\Repositories\WorkspaceAppRepository;
use App\Repositories\WorkspaceExtraRepository;
use Illuminate\Database\Seeder;

class MigrateWorkspaceAppFromWorkspaceExtra extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param WorkspaceExtraRepository $workspaceAppRepo
     * @param WorkspaceAppRepository $workspaceAppRepo
     * @return void
     */
    public function run(WorkspaceExtraRepository $workspaceExtraRepo, WorkspaceAppRepository $workspaceAppRepo)
    {
        $workspaceExtras = $workspaceExtraRepo->where('type', WorkspaceExtra::OWN_MOBILE_APP)
            ->get();

        \DB::beginTransaction();

        // Cleanup Workspace App not in list Workspace Extra
        $workspaceIds = $workspaceExtras->pluck('workspace_id')->toArray();

        \DB::table('workspace_apps')
            ->whereNotIn('workspace_id', $workspaceIds)
            ->delete();

        /** @var \App\Models\WorkspaceExtra $workspaceExtra */
        foreach ($workspaceExtras as $workspaceExtra) {
            /** @var \App\Models\WorkspaceApp|null $workspaceApp */
            $workspaceApp = $workspaceAppRepo->where('workspace_id', $workspaceExtra->workspace_id)
                ->first();

            if (empty($workspaceApp)) {
                $workspaceApp = $workspaceAppRepo->create([
                    'workspace_id' => $workspaceExtra->workspace_id,
                    'active' => $workspaceExtra->active,
                ]);
            } else {
                $workspaceApp->active = $workspaceExtra->active;
                $workspaceApp->save();
            }
        }

        \DB::commit();
    }
}
