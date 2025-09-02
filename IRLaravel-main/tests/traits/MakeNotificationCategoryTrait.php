<?php

use Faker\Factory as Faker;
use App\Models\NotificationCategory;
use App\Repositories\NotificationCategoryRepository;

trait MakeNotificationCategoryTrait
{
    /**
     * Create fake instance of NotificationCategory and save it in database
     *
     * @param array $notificationCategoryFields
     * @return NotificationCategory
     */
    public function makeNotificationCategory($notificationCategoryFields = [])
    {
        /** @var NotificationCategoryRepository $notificationCategoryRepo */
        $notificationCategoryRepo = App::make(NotificationCategoryRepository::class);
        $theme = $this->fakeNotificationCategoryData($notificationCategoryFields);
        return $notificationCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of NotificationCategory
     *
     * @param array $notificationCategoryFields
     * @return NotificationCategory
     */
    public function fakeNotificationCategory($notificationCategoryFields = [])
    {
        return new NotificationCategory($this->fakeNotificationCategoryData($notificationCategoryFields));
    }

    /**
     * Get fake data of NotificationCategory
     *
     * @param array $notificationCategoryFields
     * @return array
     */
    public function fakeNotificationCategoryData($notificationCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'notification_id' => $fake->word,
            'restaurant_category_id' => $fake->word
        ], $notificationCategoryFields);
    }
}
