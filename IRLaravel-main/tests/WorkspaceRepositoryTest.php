<?php

use App\Workspace;
use App\Repositories\WorkspaceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceRepositoryTest extends TestCase
{
    use MakeWorkspaceTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkspaceRepository
     */
    protected $workspaceRepo;

    public function setUp()
    {
        parent::setUp();
        $this->workspaceRepo = App::make(WorkspaceRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWorkspace()
    {
        $workspace = $this->fakeWorkspaceData();
        $createdWorkspace = $this->workspaceRepo->create($workspace);
        $createdWorkspace = $createdWorkspace->toArray();
        $this->assertArrayHasKey('id', $createdWorkspace);
        $this->assertNotNull($createdWorkspace['id'], 'Created Workspace must have id specified');
        $this->assertNotNull(Workspace::find($createdWorkspace['id']), 'Workspace with given id must be in DB');
        $this->assertModelData($workspace, $createdWorkspace);
    }

    /**
     * @test read
     */
    public function testReadWorkspace()
    {
        $workspace = $this->makeWorkspace();
        $dbWorkspace = $this->workspaceRepo->find($workspace->id);
        $dbWorkspace = $dbWorkspace->toArray();
        $this->assertModelData($workspace->toArray(), $dbWorkspace);
    }

    /**
     * @test update
     */
    public function testUpdateWorkspace()
    {
        $workspace = $this->makeWorkspace();
        $fakeWorkspace = $this->fakeWorkspaceData();
        $updatedWorkspace = $this->workspaceRepo->update($fakeWorkspace, $workspace->id);
        $this->assertModelData($fakeWorkspace, $updatedWorkspace->toArray());
        $dbWorkspace = $this->workspaceRepo->find($workspace->id);
        $this->assertModelData($fakeWorkspace, $dbWorkspace->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWorkspace()
    {
        $workspace = $this->makeWorkspace();
        $resp = $this->workspaceRepo->delete($workspace->id);
        $this->assertTrue($resp);
        $this->assertNull(Workspace::find($workspace->id), 'Workspace should not exist in DB');
    }
}
