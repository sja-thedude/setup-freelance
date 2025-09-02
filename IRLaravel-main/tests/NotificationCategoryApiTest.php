<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationCategoryApiTest extends TestCase
{
    use MakeNotificationCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateNotificationCategory()
    {
        $notificationCategory = $this->fakeNotificationCategoryData();
        $this->json('POST', '/api/v1/notificationCategories', $notificationCategory);

        $this->assertApiResponse($notificationCategory);
    }

    /**
     * @test
     */
    public function testReadNotificationCategory()
    {
        $notificationCategory = $this->makeNotificationCategory();
        $this->json('GET', '/api/v1/notificationCategories/'.$notificationCategory->id);

        $this->assertApiResponse($notificationCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateNotificationCategory()
    {
        $notificationCategory = $this->makeNotificationCategory();
        $editedNotificationCategory = $this->fakeNotificationCategoryData();

        $this->json('PUT', '/api/v1/notificationCategories/'.$notificationCategory->id, $editedNotificationCategory);

        $this->assertApiResponse($editedNotificationCategory);
    }

    /**
     * @test
     */
    public function testDeleteNotificationCategory()
    {
        $notificationCategory = $this->makeNotificationCategory();
        $this->json('DELETE', '/api/v1/notificationCategories/'.$notificationCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/notificationCategories/'.$notificationCategory->id);

        $this->assertStatus(404);
    }
}
