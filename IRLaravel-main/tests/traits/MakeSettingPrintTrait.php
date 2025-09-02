<?php

use Faker\Factory as Faker;
use App\Models\SettingPrint;
use App\Repositories\SettingPrintRepository;

trait MakeSettingPrintTrait
{
    /**
     * Create fake instance of SettingPrint and save it in database
     *
     * @param array $settingPrintFields
     * @return SettingPrint
     */
    public function makeSettingPrint($settingPrintFields = [])
    {
        /** @var SettingPrintRepository $settingPrintRepo */
        $settingPrintRepo = App::make(SettingPrintRepository::class);
        $theme = $this->fakeSettingPrintData($settingPrintFields);
        return $settingPrintRepo->create($theme);
    }

    /**
     * Get fake instance of SettingPrint
     *
     * @param array $settingPrintFields
     * @return SettingPrint
     */
    public function fakeSettingPrint($settingPrintFields = [])
    {
        return new SettingPrint($this->fakeSettingPrintData($settingPrintFields));
    }

    /**
     * Get fake data of SettingPrint
     *
     * @param array $settingPrintFields
     * @return array
     */
    public function fakeSettingPrintData($settingPrintFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'workspace_id' => $fake->word,
            'type' => $fake->word,
            'mac' => $fake->word,
            'copy' => $fake->randomDigitNotNull,
            'auto' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $settingPrintFields);
    }
}
