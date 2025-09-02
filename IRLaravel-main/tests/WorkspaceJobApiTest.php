<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceJobApiTest extends TestCase
{
    use MakeWorkspaceJobTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWorkspaceJob()
    {
        $workspaceJob = $this->fakeWorkspaceJobData();
        $this->json('POST', '/api/v1/workspaceJobs', $workspaceJob);

        $this->assertApiResponse($workspaceJob);
    }

    /**
     * @test
     */
    public function testReadWorkspaceJob()
    {
        $workspaceJob = $this->makeWorkspaceJob();
        $this->json('GET', '/api/v1/workspaceJobs/'.$workspaceJob->id);

        $this->assertApiResponse($workspaceJob->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWorkspaceJob()
    {
        $workspaceJob = $this->makeWorkspaceJob();
        $editedWorkspaceJob = $this->fakeWorkspaceJobData();

        $this->json('PUT', '/api/v1/workspaceJobs/'.$workspaceJob->id, $editedWorkspaceJob);

        $this->assertApiResponse($editedWorkspaceJob);
    }

    /**
     * @test
     */
    public function testDeleteWorkspaceJob()
    {
        $workspaceJob = $this->makeWorkspaceJob();
        $this->json('DELETE', '/api/v1/workspaceJobs/'.$workspaceJob->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/workspaceJobs/'.$workspaceJob->id);

        $this->assertStatus(404);
    }
}
