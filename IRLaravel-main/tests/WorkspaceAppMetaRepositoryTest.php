<?php

use App\WorkspaceAppMeta;
use App\Repositories\WorkspaceAppMetaRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceAppMetaRepositoryTest extends TestCase
{
    use MakeWorkspaceAppMetaTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkspaceAppMetaRepository
     */
    protected $workspaceAppMetaRepo;

    public function setUp()
    {
        parent::setUp();
        $this->workspaceAppMetaRepo = App::make(WorkspaceAppMetaRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWorkspaceAppMeta()
    {
        $workspaceAppMeta = $this->fakeWorkspaceAppMetaData();
        $createdWorkspaceAppMeta = $this->workspaceAppMetaRepo->create($workspaceAppMeta);
        $createdWorkspaceAppMeta = $createdWorkspaceAppMeta->toArray();
        $this->assertArrayHasKey('id', $createdWorkspaceAppMeta);
        $this->assertNotNull($createdWorkspaceAppMeta['id'], 'Created WorkspaceAppMeta must have id specified');
        $this->assertNotNull(WorkspaceAppMeta::find($createdWorkspaceAppMeta['id']), 'WorkspaceAppMeta with given id must be in DB');
        $this->assertModelData($workspaceAppMeta, $createdWorkspaceAppMeta);
    }

    /**
     * @test read
     */
    public function testReadWorkspaceAppMeta()
    {
        $workspaceAppMeta = $this->makeWorkspaceAppMeta();
        $dbWorkspaceAppMeta = $this->workspaceAppMetaRepo->find($workspaceAppMeta->id);
        $dbWorkspaceAppMeta = $dbWorkspaceAppMeta->toArray();
        $this->assertModelData($workspaceAppMeta->toArray(), $dbWorkspaceAppMeta);
    }

    /**
     * @test update
     */
    public function testUpdateWorkspaceAppMeta()
    {
        $workspaceAppMeta = $this->makeWorkspaceAppMeta();
        $fakeWorkspaceAppMeta = $this->fakeWorkspaceAppMetaData();
        $updatedWorkspaceAppMeta = $this->workspaceAppMetaRepo->update($fakeWorkspaceAppMeta, $workspaceAppMeta->id);
        $this->assertModelData($fakeWorkspaceAppMeta, $updatedWorkspaceAppMeta->toArray());
        $dbWorkspaceAppMeta = $this->workspaceAppMetaRepo->find($workspaceAppMeta->id);
        $this->assertModelData($fakeWorkspaceAppMeta, $dbWorkspaceAppMeta->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWorkspaceAppMeta()
    {
        $workspaceAppMeta = $this->makeWorkspaceAppMeta();
        $resp = $this->workspaceAppMetaRepo->delete($workspaceAppMeta->id);
        $this->assertTrue($resp);
        $this->assertNull(WorkspaceAppMeta::find($workspaceAppMeta->id), 'WorkspaceAppMeta should not exist in DB');
    }
}
