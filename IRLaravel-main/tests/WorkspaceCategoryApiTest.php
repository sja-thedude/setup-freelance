<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceCategoryApiTest extends TestCase
{
    use MakeWorkspaceCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWorkspaceCategory()
    {
        $workspaceCategory = $this->fakeWorkspaceCategoryData();
        $this->json('POST', '/api/v1/workspaceCategories', $workspaceCategory);

        $this->assertApiResponse($workspaceCategory);
    }

    /**
     * @test
     */
    public function testReadWorkspaceCategory()
    {
        $workspaceCategory = $this->makeWorkspaceCategory();
        $this->json('GET', '/api/v1/workspaceCategories/'.$workspaceCategory->id);

        $this->assertApiResponse($workspaceCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWorkspaceCategory()
    {
        $workspaceCategory = $this->makeWorkspaceCategory();
        $editedWorkspaceCategory = $this->fakeWorkspaceCategoryData();

        $this->json('PUT', '/api/v1/workspaceCategories/'.$workspaceCategory->id, $editedWorkspaceCategory);

        $this->assertApiResponse($editedWorkspaceCategory);
    }

    /**
     * @test
     */
    public function testDeleteWorkspaceCategory()
    {
        $workspaceCategory = $this->makeWorkspaceCategory();
        $this->json('DELETE', '/api/v1/workspaceCategories/'.$workspaceCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/workspaceCategories/'.$workspaceCategory->id);

        $this->assertStatus(404);
    }
}
