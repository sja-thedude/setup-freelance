<?php

use Faker\Factory as Faker;
use App\Models\WorkspaceCategory;
use App\Repositories\WorkspaceCategoryRepository;

trait MakeWorkspaceCategoryTrait
{
    /**
     * Create fake instance of WorkspaceCategory and save it in database
     *
     * @param array $workspaceCategoryFields
     * @return WorkspaceCategory
     */
    public function makeWorkspaceCategory($workspaceCategoryFields = [])
    {
        /** @var WorkspaceCategoryRepository $workspaceCategoryRepo */
        $workspaceCategoryRepo = App::make(WorkspaceCategoryRepository::class);
        $theme = $this->fakeWorkspaceCategoryData($workspaceCategoryFields);
        return $workspaceCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of WorkspaceCategory
     *
     * @param array $workspaceCategoryFields
     * @return WorkspaceCategory
     */
    public function fakeWorkspaceCategory($workspaceCategoryFields = [])
    {
        return new WorkspaceCategory($this->fakeWorkspaceCategoryData($workspaceCategoryFields));
    }

    /**
     * Get fake data of WorkspaceCategory
     *
     * @param array $workspaceCategoryFields
     * @return array
     */
    public function fakeWorkspaceCategoryData($workspaceCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'restaurant_category_id' => $fake->word
        ], $workspaceCategoryFields);
    }
}
