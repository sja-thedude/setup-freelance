<?php

use Faker\Factory as Faker;
use App\Models\NotificationPlan;
use App\Repositories\NotificationPlanRepository;

trait MakeNotificationPlanTrait
{
    /**
     * Create fake instance of NotificationPlan and save it in database
     *
     * @param array $notificationPlanFields
     * @return NotificationPlan
     */
    public function makeNotificationPlan($notificationPlanFields = [])
    {
        /** @var NotificationPlanRepository $notificationPlanRepo */
        $notificationPlanRepo = App::make(NotificationPlanRepository::class);
        $theme = $this->fakeNotificationPlanData($notificationPlanFields);
        return $notificationPlanRepo->create($theme);
    }

    /**
     * Get fake instance of NotificationPlan
     *
     * @param array $notificationPlanFields
     * @return NotificationPlan
     */
    public function fakeNotificationPlan($notificationPlanFields = [])
    {
        return new NotificationPlan($this->fakeNotificationPlanData($notificationPlanFields));
    }

    /**
     * Get fake data of NotificationPlan
     *
     * @param array $notificationPlanFields
     * @return array
     */
    public function fakeNotificationPlanData($notificationPlanFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'id' => $fake->word,
            'workspace_id' => $fake->word,
            'platform' => $fake->word,
            'title' => $fake->word,
            'description' => $fake->text,
            'is_send_everyone' => $fake->word,
            'location' => $fake->text,
            'location_lat' => $fake->word,
            'location_long' => $fake->word,
            'location_radius' => $fake->randomDigitNotNull,
            'send_now' => $fake->randomDigitNotNull,
            'send_datetime' => $fake->date('Y-m-d H:i:s'),
            'gender_dest_male' => $fake->word,
            'start_age_dest' => $fake->randomDigitNotNull,
            'end_age_dest' => $fake->randomDigitNotNull,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $notificationPlanFields);
    }
}
