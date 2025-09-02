<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationApiTest extends TestCase
{
    use MakeNotificationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateNotification()
    {
        $notification = $this->fakeNotificationData();
        $this->json('POST', '/api/v1/notifications', $notification);

        $this->assertApiResponse($notification);
    }

    /**
     * @test
     */
    public function testReadNotification()
    {
        $notification = $this->makeNotification();
        $this->json('GET', '/api/v1/notifications/'.$notification->id);

        $this->assertApiResponse($notification->toArray());
    }

    /**
     * @test
     */
    public function testUpdateNotification()
    {
        $notification = $this->makeNotification();
        $editedNotification = $this->fakeNotificationData();

        $this->json('PUT', '/api/v1/notifications/'.$notification->id, $editedNotification);

        $this->assertApiResponse($editedNotification);
    }

    /**
     * @test
     */
    public function testDeleteNotification()
    {
        $notification = $this->makeNotification();
        $this->json('DELETE', '/api/v1/notifications/'.$notification->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/notifications/'.$notification->id);

        $this->assertStatus(404);
    }
}
