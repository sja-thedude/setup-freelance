<?php

use App\NotificationCategory;
use App\Repositories\NotificationCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationCategoryRepositoryTest extends TestCase
{
    use MakeNotificationCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationCategoryRepository
     */
    protected $notificationCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->notificationCategoryRepo = App::make(NotificationCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateNotificationCategory()
    {
        $notificationCategory = $this->fakeNotificationCategoryData();
        $createdNotificationCategory = $this->notificationCategoryRepo->create($notificationCategory);
        $createdNotificationCategory = $createdNotificationCategory->toArray();
        $this->assertArrayHasKey('id', $createdNotificationCategory);
        $this->assertNotNull($createdNotificationCategory['id'], 'Created NotificationCategory must have id specified');
        $this->assertNotNull(NotificationCategory::find($createdNotificationCategory['id']), 'NotificationCategory with given id must be in DB');
        $this->assertModelData($notificationCategory, $createdNotificationCategory);
    }

    /**
     * @test read
     */
    public function testReadNotificationCategory()
    {
        $notificationCategory = $this->makeNotificationCategory();
        $dbNotificationCategory = $this->notificationCategoryRepo->find($notificationCategory->id);
        $dbNotificationCategory = $dbNotificationCategory->toArray();
        $this->assertModelData($notificationCategory->toArray(), $dbNotificationCategory);
    }

    /**
     * @test update
     */
    public function testUpdateNotificationCategory()
    {
        $notificationCategory = $this->makeNotificationCategory();
        $fakeNotificationCategory = $this->fakeNotificationCategoryData();
        $updatedNotificationCategory = $this->notificationCategoryRepo->update($fakeNotificationCategory, $notificationCategory->id);
        $this->assertModelData($fakeNotificationCategory, $updatedNotificationCategory->toArray());
        $dbNotificationCategory = $this->notificationCategoryRepo->find($notificationCategory->id);
        $this->assertModelData($fakeNotificationCategory, $dbNotificationCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteNotificationCategory()
    {
        $notificationCategory = $this->makeNotificationCategory();
        $resp = $this->notificationCategoryRepo->delete($notificationCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(NotificationCategory::find($notificationCategory->id), 'NotificationCategory should not exist in DB');
    }
}
