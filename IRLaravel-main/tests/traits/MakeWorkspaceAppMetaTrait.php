<?php

use Faker\Factory as Faker;
use App\Models\WorkspaceAppMeta;
use App\Repositories\WorkspaceAppMetaRepository;

trait MakeWorkspaceAppMetaTrait
{
    /**
     * Create fake instance of WorkspaceAppMeta and save it in database
     *
     * @param array $workspaceAppMetaFields
     * @return WorkspaceAppMeta
     */
    public function makeWorkspaceAppMeta($workspaceAppMetaFields = [])
    {
        /** @var WorkspaceAppMetaRepository $workspaceAppMetaRepo */
        $workspaceAppMetaRepo = App::make(WorkspaceAppMetaRepository::class);
        $theme = $this->fakeWorkspaceAppMetaData($workspaceAppMetaFields);
        return $workspaceAppMetaRepo->create($theme);
    }

    /**
     * Get fake instance of WorkspaceAppMeta
     *
     * @param array $workspaceAppMetaFields
     * @return WorkspaceAppMeta
     */
    public function fakeWorkspaceAppMeta($workspaceAppMetaFields = [])
    {
        return new WorkspaceAppMeta($this->fakeWorkspaceAppMetaData($workspaceAppMetaFields));
    }

    /**
     * Get fake data of WorkspaceAppMeta
     *
     * @param array $workspaceAppMetaFields
     * @return array
     */
    public function fakeWorkspaceAppMetaData($workspaceAppMetaFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'active' => $fake->word,
            'order' => $fake->randomDigitNotNull,
            'workspace_app_id' => $fake->word,
            'default' => $fake->word,
            'name' => $fake->word,
            'title' => $fake->word,
            'description' => $fake->text,
            'content' => $fake->text,
            'icon' => $fake->text,
            'url' => $fake->text,
            'meta_data' => $fake->text
        ], $workspaceAppMetaFields);
    }
}
