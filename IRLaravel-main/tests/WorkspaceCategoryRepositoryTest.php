<?php

use App\WorkspaceCategory;
use App\Repositories\WorkspaceCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WorkspaceCategoryRepositoryTest extends TestCase
{
    use MakeWorkspaceCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkspaceCategoryRepository
     */
    protected $workspaceCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->workspaceCategoryRepo = App::make(WorkspaceCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWorkspaceCategory()
    {
        $workspaceCategory = $this->fakeWorkspaceCategoryData();
        $createdWorkspaceCategory = $this->workspaceCategoryRepo->create($workspaceCategory);
        $createdWorkspaceCategory = $createdWorkspaceCategory->toArray();
        $this->assertArrayHasKey('id', $createdWorkspaceCategory);
        $this->assertNotNull($createdWorkspaceCategory['id'], 'Created WorkspaceCategory must have id specified');
        $this->assertNotNull(WorkspaceCategory::find($createdWorkspaceCategory['id']), 'WorkspaceCategory with given id must be in DB');
        $this->assertModelData($workspaceCategory, $createdWorkspaceCategory);
    }

    /**
     * @test read
     */
    public function testReadWorkspaceCategory()
    {
        $workspaceCategory = $this->makeWorkspaceCategory();
        $dbWorkspaceCategory = $this->workspaceCategoryRepo->find($workspaceCategory->id);
        $dbWorkspaceCategory = $dbWorkspaceCategory->toArray();
        $this->assertModelData($workspaceCategory->toArray(), $dbWorkspaceCategory);
    }

    /**
     * @test update
     */
    public function testUpdateWorkspaceCategory()
    {
        $workspaceCategory = $this->makeWorkspaceCategory();
        $fakeWorkspaceCategory = $this->fakeWorkspaceCategoryData();
        $updatedWorkspaceCategory = $this->workspaceCategoryRepo->update($fakeWorkspaceCategory, $workspaceCategory->id);
        $this->assertModelData($fakeWorkspaceCategory, $updatedWorkspaceCategory->toArray());
        $dbWorkspaceCategory = $this->workspaceCategoryRepo->find($workspaceCategory->id);
        $this->assertModelData($fakeWorkspaceCategory, $dbWorkspaceCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWorkspaceCategory()
    {
        $workspaceCategory = $this->makeWorkspaceCategory();
        $resp = $this->workspaceCategoryRepo->delete($workspaceCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(WorkspaceCategory::find($workspaceCategory->id), 'WorkspaceCategory should not exist in DB');
    }
}
