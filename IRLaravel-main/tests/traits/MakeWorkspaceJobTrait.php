<?php

use Faker\Factory as Faker;
use App\Models\WorkspaceJob;
use App\Repositories\WorkspaceJobRepository;

trait MakeWorkspaceJobTrait
{
    /**
     * Create fake instance of WorkspaceJob and save it in database
     *
     * @param array $workspaceJobFields
     * @return WorkspaceJob
     */
    public function makeWorkspaceJob($workspaceJobFields = [])
    {
        /** @var WorkspaceJobRepository $workspaceJobRepo */
        $workspaceJobRepo = App::make(WorkspaceJobRepository::class);
        $theme = $this->fakeWorkspaceJobData($workspaceJobFields);
        return $workspaceJobRepo->create($theme);
    }

    /**
     * Get fake instance of WorkspaceJob
     *
     * @param array $workspaceJobFields
     * @return WorkspaceJob
     */
    public function fakeWorkspaceJob($workspaceJobFields = [])
    {
        return new WorkspaceJob($this->fakeWorkspaceJobData($workspaceJobFields));
    }

    /**
     * Get fake data of WorkspaceJob
     *
     * @param array $workspaceJobFields
     * @return array
     */
    public function fakeWorkspaceJobData($workspaceJobFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'workspace_id' => $fake->word,
            'name' => $fake->word,
            'email' => $fake->word,
            'phone' => $fake->word,
            'content' => $fake->text
        ], $workspaceJobFields);
    }
}
