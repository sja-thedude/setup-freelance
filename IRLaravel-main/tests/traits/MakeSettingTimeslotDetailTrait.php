<?php

use Faker\Factory as Faker;
use App\Models\SettingTimeslotDetail;
use App\Repositories\SettingTimeslotDetailRepository;

trait MakeSettingTimeslotDetailTrait
{
    /**
     * Create fake instance of SettingTimeslotDetail and save it in database
     *
     * @param array $settingTimeslotDetailFields
     * @return SettingTimeslotDetail
     */
    public function makeSettingTimeslotDetail($settingTimeslotDetailFields = [])
    {
        /** @var SettingTimeslotDetailRepository $settingTimeslotDetailRepo */
        $settingTimeslotDetailRepo = App::make(SettingTimeslotDetailRepository::class);
        $theme = $this->fakeSettingTimeslotDetailData($settingTimeslotDetailFields);
        return $settingTimeslotDetailRepo->create($theme);
    }

    /**
     * Get fake instance of SettingTimeslotDetail
     *
     * @param array $settingTimeslotDetailFields
     * @return SettingTimeslotDetail
     */
    public function fakeSettingTimeslotDetail($settingTimeslotDetailFields = [])
    {
        return new SettingTimeslotDetail($this->fakeSettingTimeslotDetailData($settingTimeslotDetailFields));
    }

    /**
     * Get fake data of SettingTimeslotDetail
     *
     * @param array $settingTimeslotDetailFields
     * @return array
     */
    public function fakeSettingTimeslotDetailData($settingTimeslotDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'workspace_id' => $fake->word,
            'setting_timeslot_id' => $fake->word,
            'type' => $fake->word,
            'active' => $fake->word,
            'time' => $fake->word,
            'max' => $fake->randomDigitNotNull,
            'date' => $fake->word,
            'repeat' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $settingTimeslotDetailFields);
    }
}
