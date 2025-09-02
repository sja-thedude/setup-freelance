<?php

use Faker\Factory as Faker;
use App\Models\Notification;
use App\Repositories\NotificationRepository;

trait MakeNotificationTrait
{
    /**
     * Create fake instance of Notification and save it in database
     *
     * @param array $notificationFields
     * @return Notification
     */
    public function makeNotification($notificationFields = [])
    {
        /** @var NotificationRepository $notificationRepo */
        $notificationRepo = App::make(NotificationRepository::class);
        $theme = $this->fakeNotificationData($notificationFields);
        return $notificationRepo->create($theme);
    }

    /**
     * Get fake instance of Notification
     *
     * @param array $notificationFields
     * @return Notification
     */
    public function fakeNotification($notificationFields = [])
    {
        return new Notification($this->fakeNotificationData($notificationFields));
    }

    /**
     * Get fake data of Notification
     *
     * @param array $notificationFields
     * @return array
     */
    public function fakeNotificationData($notificationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'notification_plan_id' => $fake->word,
            'platform' => $fake->word,
            'title' => $fake->word,
            'description' => $fake->text,
            'sent_time' => $fake->date('Y-m-d H:i:s'),
            'user_id' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $notificationFields);
    }
}
