<?php

use App\WorkspaceJob;
use App\Repositories\WorkspaceJobRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceJobRepositoryTest extends TestCase
{
    use MakeWorkspaceJobTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkspaceJobRepository
     */
    protected $workspaceJobRepo;

    public function setUp()
    {
        parent::setUp();
        $this->workspaceJobRepo = App::make(WorkspaceJobRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWorkspaceJob()
    {
        $workspaceJob = $this->fakeWorkspaceJobData();
        $createdWorkspaceJob = $this->workspaceJobRepo->create($workspaceJob);
        $createdWorkspaceJob = $createdWorkspaceJob->toArray();
        $this->assertArrayHasKey('id', $createdWorkspaceJob);
        $this->assertNotNull($createdWorkspaceJob['id'], 'Created WorkspaceJob must have id specified');
        $this->assertNotNull(WorkspaceJob::find($createdWorkspaceJob['id']), 'WorkspaceJob with given id must be in DB');
        $this->assertModelData($workspaceJob, $createdWorkspaceJob);
    }

    /**
     * @test read
     */
    public function testReadWorkspaceJob()
    {
        $workspaceJob = $this->makeWorkspaceJob();
        $dbWorkspaceJob = $this->workspaceJobRepo->find($workspaceJob->id);
        $dbWorkspaceJob = $dbWorkspaceJob->toArray();
        $this->assertModelData($workspaceJob->toArray(), $dbWorkspaceJob);
    }

    /**
     * @test update
     */
    public function testUpdateWorkspaceJob()
    {
        $workspaceJob = $this->makeWorkspaceJob();
        $fakeWorkspaceJob = $this->fakeWorkspaceJobData();
        $updatedWorkspaceJob = $this->workspaceJobRepo->update($fakeWorkspaceJob, $workspaceJob->id);
        $this->assertModelData($fakeWorkspaceJob, $updatedWorkspaceJob->toArray());
        $dbWorkspaceJob = $this->workspaceJobRepo->find($workspaceJob->id);
        $this->assertModelData($fakeWorkspaceJob, $dbWorkspaceJob->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWorkspaceJob()
    {
        $workspaceJob = $this->makeWorkspaceJob();
        $resp = $this->workspaceJobRepo->delete($workspaceJob->id);
        $this->assertTrue($resp);
        $this->assertNull(WorkspaceJob::find($workspaceJob->id), 'WorkspaceJob should not exist in DB');
    }
}
