<?php

use Faker\Factory as Faker;
use App\Models\SettingOpenHour;
use App\Repositories\SettingOpenHourRepository;

trait MakeSettingOpenHourTrait
{
    /**
     * Create fake instance of SettingOpenHour and save it in database
     *
     * @param array $settingOpenHourFields
     * @return SettingOpenHour
     */
    public function makeSettingOpenHour($settingOpenHourFields = [])
    {
        /** @var SettingOpenHourRepository $settingOpenHourRepo */
        $settingOpenHourRepo = App::make(SettingOpenHourRepository::class);
        $theme = $this->fakeSettingOpenHourData($settingOpenHourFields);
        return $settingOpenHourRepo->create($theme);
    }

    /**
     * Get fake instance of SettingOpenHour
     *
     * @param array $settingOpenHourFields
     * @return SettingOpenHour
     */
    public function fakeSettingOpenHour($settingOpenHourFields = [])
    {
        return new SettingOpenHour($this->fakeSettingOpenHourData($settingOpenHourFields));
    }

    /**
     * Get fake data of SettingOpenHour
     *
     * @param array $settingOpenHourFields
     * @return array
     */
    public function fakeSettingOpenHourData($settingOpenHourFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'type' => $fake->word,
            'active' => $fake->word,
            'workspace_id' => $fake->word
        ], $settingOpenHourFields);
    }
}
