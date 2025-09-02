<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceAppMetaApiTest extends TestCase
{
    use MakeWorkspaceAppMetaTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWorkspaceAppMeta()
    {
        $workspaceAppMeta = $this->fakeWorkspaceAppMetaData();
        $this->json('POST', '/api/v1/workspaceAppMetas', $workspaceAppMeta);

        $this->assertApiResponse($workspaceAppMeta);
    }

    /**
     * @test
     */
    public function testReadWorkspaceAppMeta()
    {
        $workspaceAppMeta = $this->makeWorkspaceAppMeta();
        $this->json('GET', '/api/v1/workspaceAppMetas/'.$workspaceAppMeta->id);

        $this->assertApiResponse($workspaceAppMeta->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWorkspaceAppMeta()
    {
        $workspaceAppMeta = $this->makeWorkspaceAppMeta();
        $editedWorkspaceAppMeta = $this->fakeWorkspaceAppMetaData();

        $this->json('PUT', '/api/v1/workspaceAppMetas/'.$workspaceAppMeta->id, $editedWorkspaceAppMeta);

        $this->assertApiResponse($editedWorkspaceAppMeta);
    }

    /**
     * @test
     */
    public function testDeleteWorkspaceAppMeta()
    {
        $workspaceAppMeta = $this->makeWorkspaceAppMeta();
        $this->json('DELETE', '/api/v1/workspaceAppMetas/'.$workspaceAppMeta->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/workspaceAppMetas/'.$workspaceAppMeta->id);

        $this->assertStatus(404);
    }
}
