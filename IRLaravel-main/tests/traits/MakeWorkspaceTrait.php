<?php

use Faker\Factory as Faker;
use App\Models\Workspace;
use App\Repositories\WorkspaceRepository;

trait MakeWorkspaceTrait
{
    /**
     * Create fake instance of Workspace and save it in database
     *
     * @param array $workspaceFields
     * @return Workspace
     */
    public function makeWorkspace($workspaceFields = [])
    {
        /** @var WorkspaceRepository $workspaceRepo */
        $workspaceRepo = App::make(WorkspaceRepository::class);
        $theme = $this->fakeWorkspaceData($workspaceFields);
        return $workspaceRepo->create($theme);
    }

    /**
     * Get fake instance of Workspace
     *
     * @param array $workspaceFields
     * @return Workspace
     */
    public function fakeWorkspace($workspaceFields = [])
    {
        return new Workspace($this->fakeWorkspaceData($workspaceFields));
    }

    /**
     * Get fake data of Workspace
     *
     * @param array $workspaceFields
     * @return array
     */
    public function fakeWorkspaceData($workspaceFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->word,
            'name' => $fake->word,
            'active' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s'),
            'account_manager_id' => $fake->word,
            'gsm' => $fake->word,
            'manager_name' => $fake->word,
            'address' => $fake->text,
            'btw_nr' => $fake->word,
            'email' => $fake->word,
            'language' => $fake->word,
            'country_id' => $fake->word,
            'first_login' => $fake->date('Y-m-d H:i:s'),
            'status' => $fake->word,
            'address_lat' => $fake->word,
            'address_long' => $fake->word,
            'is_online' => $fake->word,
            'is_test_mode' => $fake->word
        ], $workspaceFields);
    }
}
