<?php

use Faker\Factory as Faker;
use App\Models\SettingPreference;
use App\Repositories\SettingPreferenceRepository;

trait MakeSettingPreferenceTrait
{
    /**
     * Create fake instance of SettingPreference and save it in database
     *
     * @param array $settingPreferenceFields
     * @return SettingPreference
     */
    public function makeSettingPreference($settingPreferenceFields = [])
    {
        /** @var SettingPreferenceRepository $settingPreferenceRepo */
        $settingPreferenceRepo = App::make(SettingPreferenceRepository::class);
        $theme = $this->fakeSettingPreferenceData($settingPreferenceFields);
        return $settingPreferenceRepo->create($theme);
    }

    /**
     * Get fake instance of SettingPreference
     *
     * @param array $settingPreferenceFields
     * @return SettingPreference
     */
    public function fakeSettingPreference($settingPreferenceFields = [])
    {
        return new SettingPreference($this->fakeSettingPreferenceData($settingPreferenceFields));
    }

    /**
     * Get fake data of SettingPreference
     *
     * @param array $settingPreferenceFields
     * @return array
     */
    public function fakeSettingPreferenceData($settingPreferenceFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'workspace_id' => $fake->word,
            'takeout_min_time' => $fake->randomDigitNotNull,
            'takeout_day_order' => $fake->randomDigitNotNull,
            'delivery_min_time' => $fake->randomDigitNotNull,
            'delivery_day_order' => $fake->randomDigitNotNull,
            'mins_before_notify' => $fake->randomDigitNotNull,
            'use_sms_whatsapp' => $fake->word,
            'use_email' => $fake->word,
            'receive_notify' => $fake->word,
            'sound_notify' => $fake->word,
            'opties_id' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $settingPreferenceFields);
    }
}
