<?php

use App\NotificationPlan;
use App\Repositories\NotificationPlanRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationPlanRepositoryTest extends TestCase
{
    use MakeNotificationPlanTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationPlanRepository
     */
    protected $notificationPlanRepo;

    public function setUp()
    {
        parent::setUp();
        $this->notificationPlanRepo = App::make(NotificationPlanRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateNotificationPlan()
    {
        $notificationPlan = $this->fakeNotificationPlanData();
        $createdNotificationPlan = $this->notificationPlanRepo->create($notificationPlan);
        $createdNotificationPlan = $createdNotificationPlan->toArray();
        $this->assertArrayHasKey('id', $createdNotificationPlan);
        $this->assertNotNull($createdNotificationPlan['id'], 'Created NotificationPlan must have id specified');
        $this->assertNotNull(NotificationPlan::find($createdNotificationPlan['id']), 'NotificationPlan with given id must be in DB');
        $this->assertModelData($notificationPlan, $createdNotificationPlan);
    }

    /**
     * @test read
     */
    public function testReadNotificationPlan()
    {
        $notificationPlan = $this->makeNotificationPlan();
        $dbNotificationPlan = $this->notificationPlanRepo->find($notificationPlan->id);
        $dbNotificationPlan = $dbNotificationPlan->toArray();
        $this->assertModelData($notificationPlan->toArray(), $dbNotificationPlan);
    }

    /**
     * @test update
     */
    public function testUpdateNotificationPlan()
    {
        $notificationPlan = $this->makeNotificationPlan();
        $fakeNotificationPlan = $this->fakeNotificationPlanData();
        $updatedNotificationPlan = $this->notificationPlanRepo->update($fakeNotificationPlan, $notificationPlan->id);
        $this->assertModelData($fakeNotificationPlan, $updatedNotificationPlan->toArray());
        $dbNotificationPlan = $this->notificationPlanRepo->find($notificationPlan->id);
        $this->assertModelData($fakeNotificationPlan, $dbNotificationPlan->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteNotificationPlan()
    {
        $notificationPlan = $this->makeNotificationPlan();
        $resp = $this->notificationPlanRepo->delete($notificationPlan->id);
        $this->assertTrue($resp);
        $this->assertNull(NotificationPlan::find($notificationPlan->id), 'NotificationPlan should not exist in DB');
    }
}
