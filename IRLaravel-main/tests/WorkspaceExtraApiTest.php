<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceExtraApiTest extends TestCase
{
    use MakeWorkspaceExtraTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWorkspaceExtra()
    {
        $workspaceExtra = $this->fakeWorkspaceExtraData();
        $this->json('POST', '/api/v1/workspaceExtras', $workspaceExtra);

        $this->assertApiResponse($workspaceExtra);
    }

    /**
     * @test
     */
    public function testReadWorkspaceExtra()
    {
        $workspaceExtra = $this->makeWorkspaceExtra();
        $this->json('GET', '/api/v1/workspaceExtras/'.$workspaceExtra->id);

        $this->assertApiResponse($workspaceExtra->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWorkspaceExtra()
    {
        $workspaceExtra = $this->makeWorkspaceExtra();
        $editedWorkspaceExtra = $this->fakeWorkspaceExtraData();

        $this->json('PUT', '/api/v1/workspaceExtras/'.$workspaceExtra->id, $editedWorkspaceExtra);

        $this->assertApiResponse($editedWorkspaceExtra);
    }

    /**
     * @test
     */
    public function testDeleteWorkspaceExtra()
    {
        $workspaceExtra = $this->makeWorkspaceExtra();
        $this->json('DELETE', '/api/v1/workspaceExtras/'.$workspaceExtra->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/workspaceExtras/'.$workspaceExtra->id);

        $this->assertStatus(404);
    }
}
