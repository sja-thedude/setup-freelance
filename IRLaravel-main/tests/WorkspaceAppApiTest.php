<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceAppApiTest extends TestCase
{
    use MakeWorkspaceAppTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWorkspaceApp()
    {
        $workspaceApp = $this->fakeWorkspaceAppData();
        $this->json('POST', '/api/v1/workspaceApps', $workspaceApp);

        $this->assertApiResponse($workspaceApp);
    }

    /**
     * @test
     */
    public function testReadWorkspaceApp()
    {
        $workspaceApp = $this->makeWorkspaceApp();
        $this->json('GET', '/api/v1/workspaceApps/'.$workspaceApp->id);

        $this->assertApiResponse($workspaceApp->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWorkspaceApp()
    {
        $workspaceApp = $this->makeWorkspaceApp();
        $editedWorkspaceApp = $this->fakeWorkspaceAppData();

        $this->json('PUT', '/api/v1/workspaceApps/'.$workspaceApp->id, $editedWorkspaceApp);

        $this->assertApiResponse($editedWorkspaceApp);
    }

    /**
     * @test
     */
    public function testDeleteWorkspaceApp()
    {
        $workspaceApp = $this->makeWorkspaceApp();
        $this->json('DELETE', '/api/v1/workspaceApps/'.$workspaceApp->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/workspaceApps/'.$workspaceApp->id);

        $this->assertStatus(404);
    }
}
