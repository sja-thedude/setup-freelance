<?php

use Faker\Factory as Faker;
use App\Models\WorkspaceApp;
use App\Repositories\WorkspaceAppRepository;

trait MakeWorkspaceAppTrait
{
    /**
     * Create fake instance of WorkspaceApp and save it in database
     *
     * @param array $workspaceAppFields
     * @return WorkspaceApp
     */
    public function makeWorkspaceApp($workspaceAppFields = [])
    {
        /** @var WorkspaceAppRepository $workspaceAppRepo */
        $workspaceAppRepo = App::make(WorkspaceAppRepository::class);
        $theme = $this->fakeWorkspaceAppData($workspaceAppFields);
        return $workspaceAppRepo->create($theme);
    }

    /**
     * Get fake instance of WorkspaceApp
     *
     * @param array $workspaceAppFields
     * @return WorkspaceApp
     */
    public function fakeWorkspaceApp($workspaceAppFields = [])
    {
        return new WorkspaceApp($this->fakeWorkspaceAppData($workspaceAppFields));
    }

    /**
     * Get fake data of WorkspaceApp
     *
     * @param array $workspaceAppFields
     * @return array
     */
    public function fakeWorkspaceAppData($workspaceAppFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'active' => $fake->word,
            'workspace_id' => $fake->word,
            'theme' => $fake->word
        ], $workspaceAppFields);
    }
}
