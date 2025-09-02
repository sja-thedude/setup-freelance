<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceApiTest extends TestCase
{
    use MakeWorkspaceTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWorkspace()
    {
        $workspace = $this->fakeWorkspaceData();
        $this->json('POST', '/api/v1/workspaces', $workspace);

        $this->assertApiResponse($workspace);
    }

    /**
     * @test
     */
    public function testReadWorkspace()
    {
        $workspace = $this->makeWorkspace();
        $this->json('GET', '/api/v1/workspaces/'.$workspace->id);

        $this->assertApiResponse($workspace->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWorkspace()
    {
        $workspace = $this->makeWorkspace();
        $editedWorkspace = $this->fakeWorkspaceData();

        $this->json('PUT', '/api/v1/workspaces/'.$workspace->id, $editedWorkspace);

        $this->assertApiResponse($editedWorkspace);
    }

    /**
     * @test
     */
    public function testDeleteWorkspace()
    {
        $workspace = $this->makeWorkspace();
        $this->json('DELETE', '/api/v1/workspaces/'.$workspace->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/workspaces/'.$workspace->id);

        $this->assertStatus(404);
    }
}
