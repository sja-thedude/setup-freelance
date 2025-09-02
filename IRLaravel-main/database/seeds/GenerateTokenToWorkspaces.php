<?php

use App\Repositories\WorkspaceRepository;
use Illuminate\Database\Seeder;

class GenerateTokenToWorkspaces extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param WorkspaceRepository $workspaceRepo
     * @return void
     */
    public function run(WorkspaceRepository $workspaceRepo)
    {
        $workspaces = $workspaceRepo->withTrashed()
            ->get(['id', 'token']);

        /** @var \App\Models\Workspace $workspace */
        foreach ($workspaces as $workspace) {
            if (empty($workspace->token)) {
                $workspace->token = strtoupper(Helper::createNewToken());
                $workspace->save();
            }
        }
    }
}
