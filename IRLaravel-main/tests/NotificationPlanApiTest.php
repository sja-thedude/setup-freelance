<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationPlanApiTest extends TestCase
{
    use MakeNotificationPlanTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateNotificationPlan()
    {
        $notificationPlan = $this->fakeNotificationPlanData();
        $this->json('POST', '/api/v1/notificationPlans', $notificationPlan);

        $this->assertApiResponse($notificationPlan);
    }

    /**
     * @test
     */
    public function testReadNotificationPlan()
    {
        $notificationPlan = $this->makeNotificationPlan();
        $this->json('GET', '/api/v1/notificationPlans/'.$notificationPlan->id);

        $this->assertApiResponse($notificationPlan->toArray());
    }

    /**
     * @test
     */
    public function testUpdateNotificationPlan()
    {
        $notificationPlan = $this->makeNotificationPlan();
        $editedNotificationPlan = $this->fakeNotificationPlanData();

        $this->json('PUT', '/api/v1/notificationPlans/'.$notificationPlan->id, $editedNotificationPlan);

        $this->assertApiResponse($editedNotificationPlan);
    }

    /**
     * @test
     */
    public function testDeleteNotificationPlan()
    {
        $notificationPlan = $this->makeNotificationPlan();
        $this->json('DELETE', '/api/v1/notificationPlans/'.$notificationPlan->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/notificationPlans/'.$notificationPlan->id);

        $this->assertStatus(404);
    }
}
