<?php

use Faker\Factory as Faker;
use App\Models\SettingGeneral;
use App\Repositories\SettingGeneralRepository;

trait MakeSettingGeneralTrait
{
    /**
     * Create fake instance of SettingGeneral and save it in database
     *
     * @param array $settingGeneralFields
     * @return SettingGeneral
     */
    public function makeSettingGeneral($settingGeneralFields = [])
    {
        /** @var SettingGeneralRepository $settingGeneralRepo */
        $settingGeneralRepo = App::make(SettingGeneralRepository::class);
        $theme = $this->fakeSettingGeneralData($settingGeneralFields);
        return $settingGeneralRepo->create($theme);
    }

    /**
     * Get fake instance of SettingGeneral
     *
     * @param array $settingGeneralFields
     * @return SettingGeneral
     */
    public function fakeSettingGeneral($settingGeneralFields = [])
    {
        return new SettingGeneral($this->fakeSettingGeneralData($settingGeneralFields));
    }

    /**
     * Get fake data of SettingGeneral
     *
     * @param array $settingGeneralFields
     * @return array
     */
    public function fakeSettingGeneralData($settingGeneralFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'workspace_id' => $fake->word,
            'primary_color' => $fake->word,
            'second_color' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $settingGeneralFields);
    }
}
