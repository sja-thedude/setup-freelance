<?php

use App\Notification;
use App\Repositories\NotificationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationRepositoryTest extends TestCase
{
    use MakeNotificationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationRepository
     */
    protected $notificationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->notificationRepo = App::make(NotificationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateNotification()
    {
        $notification = $this->fakeNotificationData();
        $createdNotification = $this->notificationRepo->create($notification);
        $createdNotification = $createdNotification->toArray();
        $this->assertArrayHasKey('id', $createdNotification);
        $this->assertNotNull($createdNotification['id'], 'Created Notification must have id specified');
        $this->assertNotNull(Notification::find($createdNotification['id']), 'Notification with given id must be in DB');
        $this->assertModelData($notification, $createdNotification);
    }

    /**
     * @test read
     */
    public function testReadNotification()
    {
        $notification = $this->makeNotification();
        $dbNotification = $this->notificationRepo->find($notification->id);
        $dbNotification = $dbNotification->toArray();
        $this->assertModelData($notification->toArray(), $dbNotification);
    }

    /**
     * @test update
     */
    public function testUpdateNotification()
    {
        $notification = $this->makeNotification();
        $fakeNotification = $this->fakeNotificationData();
        $updatedNotification = $this->notificationRepo->update($fakeNotification, $notification->id);
        $this->assertModelData($fakeNotification, $updatedNotification->toArray());
        $dbNotification = $this->notificationRepo->find($notification->id);
        $this->assertModelData($fakeNotification, $dbNotification->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteNotification()
    {
        $notification = $this->makeNotification();
        $resp = $this->notificationRepo->delete($notification->id);
        $this->assertTrue($resp);
        $this->assertNull(Notification::find($notification->id), 'Notification should not exist in DB');
    }
}
