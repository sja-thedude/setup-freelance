<?php

use Faker\Factory as Faker;
use App\Models\SettingTimeslot;
use App\Repositories\SettingTimeslotRepository;

trait MakeSettingTimeslotTrait
{
    /**
     * Create fake instance of SettingTimeslot and save it in database
     *
     * @param array $settingTimeslotFields
     * @return SettingTimeslot
     */
    public function makeSettingTimeslot($settingTimeslotFields = [])
    {
        /** @var SettingTimeslotRepository $settingTimeslotRepo */
        $settingTimeslotRepo = App::make(SettingTimeslotRepository::class);
        $theme = $this->fakeSettingTimeslotData($settingTimeslotFields);
        return $settingTimeslotRepo->create($theme);
    }

    /**
     * Get fake instance of SettingTimeslot
     *
     * @param array $settingTimeslotFields
     * @return SettingTimeslot
     */
    public function fakeSettingTimeslot($settingTimeslotFields = [])
    {
        return new SettingTimeslot($this->fakeSettingTimeslotData($settingTimeslotFields));
    }

    /**
     * Get fake data of SettingTimeslot
     *
     * @param array $settingTimeslotFields
     * @return array
     */
    public function fakeSettingTimeslotData($settingTimeslotFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'workspace_id' => $fake->word,
            'limit_order' => $fake->randomDigitNotNull,
            'max_order' => $fake->randomDigitNotNull,
            'interval_time' => $fake->randomDigitNotNull,
            'type' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $settingTimeslotFields);
    }
}
