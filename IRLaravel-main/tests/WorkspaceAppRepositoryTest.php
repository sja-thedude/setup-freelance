<?php

use App\WorkspaceApp;
use App\Repositories\WorkspaceAppRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceAppRepositoryTest extends TestCase
{
    use MakeWorkspaceAppTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkspaceAppRepository
     */
    protected $workspaceAppRepo;

    public function setUp()
    {
        parent::setUp();
        $this->workspaceAppRepo = App::make(WorkspaceAppRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWorkspaceApp()
    {
        $workspaceApp = $this->fakeWorkspaceAppData();
        $createdWorkspaceApp = $this->workspaceAppRepo->create($workspaceApp);
        $createdWorkspaceApp = $createdWorkspaceApp->toArray();
        $this->assertArrayHasKey('id', $createdWorkspaceApp);
        $this->assertNotNull($createdWorkspaceApp['id'], 'Created WorkspaceApp must have id specified');
        $this->assertNotNull(WorkspaceApp::find($createdWorkspaceApp['id']), 'WorkspaceApp with given id must be in DB');
        $this->assertModelData($workspaceApp, $createdWorkspaceApp);
    }

    /**
     * @test read
     */
    public function testReadWorkspaceApp()
    {
        $workspaceApp = $this->makeWorkspaceApp();
        $dbWorkspaceApp = $this->workspaceAppRepo->find($workspaceApp->id);
        $dbWorkspaceApp = $dbWorkspaceApp->toArray();
        $this->assertModelData($workspaceApp->toArray(), $dbWorkspaceApp);
    }

    /**
     * @test update
     */
    public function testUpdateWorkspaceApp()
    {
        $workspaceApp = $this->makeWorkspaceApp();
        $fakeWorkspaceApp = $this->fakeWorkspaceAppData();
        $updatedWorkspaceApp = $this->workspaceAppRepo->update($fakeWorkspaceApp, $workspaceApp->id);
        $this->assertModelData($fakeWorkspaceApp, $updatedWorkspaceApp->toArray());
        $dbWorkspaceApp = $this->workspaceAppRepo->find($workspaceApp->id);
        $this->assertModelData($fakeWorkspaceApp, $dbWorkspaceApp->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWorkspaceApp()
    {
        $workspaceApp = $this->makeWorkspaceApp();
        $resp = $this->workspaceAppRepo->delete($workspaceApp->id);
        $this->assertTrue($resp);
        $this->assertNull(WorkspaceApp::find($workspaceApp->id), 'WorkspaceApp should not exist in DB');
    }
}
