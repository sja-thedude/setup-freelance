<?php

use App\WorkspaceExtra;
use App\Repositories\WorkspaceExtraRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceExtraRepositoryTest extends TestCase
{
    use MakeWorkspaceExtraTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkspaceExtraRepository
     */
    protected $workspaceExtraRepo;

    public function setUp()
    {
        parent::setUp();
        $this->workspaceExtraRepo = App::make(WorkspaceExtraRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWorkspaceExtra()
    {
        $workspaceExtra = $this->fakeWorkspaceExtraData();
        $createdWorkspaceExtra = $this->workspaceExtraRepo->create($workspaceExtra);
        $createdWorkspaceExtra = $createdWorkspaceExtra->toArray();
        $this->assertArrayHasKey('id', $createdWorkspaceExtra);
        $this->assertNotNull($createdWorkspaceExtra['id'], 'Created WorkspaceExtra must have id specified');
        $this->assertNotNull(WorkspaceExtra::find($createdWorkspaceExtra['id']), 'WorkspaceExtra with given id must be in DB');
        $this->assertModelData($workspaceExtra, $createdWorkspaceExtra);
    }

    /**
     * @test read
     */
    public function testReadWorkspaceExtra()
    {
        $workspaceExtra = $this->makeWorkspaceExtra();
        $dbWorkspaceExtra = $this->workspaceExtraRepo->find($workspaceExtra->id);
        $dbWorkspaceExtra = $dbWorkspaceExtra->toArray();
        $this->assertModelData($workspaceExtra->toArray(), $dbWorkspaceExtra);
    }

    /**
     * @test update
     */
    public function testUpdateWorkspaceExtra()
    {
        $workspaceExtra = $this->makeWorkspaceExtra();
        $fakeWorkspaceExtra = $this->fakeWorkspaceExtraData();
        $updatedWorkspaceExtra = $this->workspaceExtraRepo->update($fakeWorkspaceExtra, $workspaceExtra->id);
        $this->assertModelData($fakeWorkspaceExtra, $updatedWorkspaceExtra->toArray());
        $dbWorkspaceExtra = $this->workspaceExtraRepo->find($workspaceExtra->id);
        $this->assertModelData($fakeWorkspaceExtra, $dbWorkspaceExtra->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWorkspaceExtra()
    {
        $workspaceExtra = $this->makeWorkspaceExtra();
        $resp = $this->workspaceExtraRepo->delete($workspaceExtra->id);
        $this->assertTrue($resp);
        $this->assertNull(WorkspaceExtra::find($workspaceExtra->id), 'WorkspaceExtra should not exist in DB');
    }
}
