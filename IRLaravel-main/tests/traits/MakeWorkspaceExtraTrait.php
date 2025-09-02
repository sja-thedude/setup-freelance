<?php

use Faker\Factory as Faker;
use App\Models\WorkspaceExtra;
use App\Repositories\WorkspaceExtraRepository;

trait MakeWorkspaceExtraTrait
{
    /**
     * Create fake instance of WorkspaceExtra and save it in database
     *
     * @param array $workspaceExtraFields
     * @return WorkspaceExtra
     */
    public function makeWorkspaceExtra($workspaceExtraFields = [])
    {
        /** @var WorkspaceExtraRepository $workspaceExtraRepo */
        $workspaceExtraRepo = App::make(WorkspaceExtraRepository::class);
        $theme = $this->fakeWorkspaceExtraData($workspaceExtraFields);
        return $workspaceExtraRepo->create($theme);
    }

    /**
     * Get fake instance of WorkspaceExtra
     *
     * @param array $workspaceExtraFields
     * @return WorkspaceExtra
     */
    public function fakeWorkspaceExtra($workspaceExtraFields = [])
    {
        return new WorkspaceExtra($this->fakeWorkspaceExtraData($workspaceExtraFields));
    }

    /**
     * Get fake data of WorkspaceExtra
     *
     * @param array $workspaceExtraFields
     * @return array
     */
    public function fakeWorkspaceExtraData($workspaceExtraFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'workspace_id' => $fake->word,
            'type' => $fake->randomDigitNotNull,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $workspaceExtraFields);
    }
}
