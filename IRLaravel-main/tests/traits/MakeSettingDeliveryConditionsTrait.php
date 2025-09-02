<?php

use Faker\Factory as Faker;
use App\Models\SettingDeliveryConditions;
use App\Repositories\SettingDeliveryConditionsRepository;

trait MakeSettingDeliveryConditionsTrait
{
    /**
     * Create fake instance of SettingDeliveryConditions and save it in database
     *
     * @param array $settingDeliveryConditionsFields
     * @return SettingDeliveryConditions
     */
    public function makeSettingDeliveryConditions($settingDeliveryConditionsFields = [])
    {
        /** @var SettingDeliveryConditionsRepository $settingDeliveryConditionsRepo */
        $settingDeliveryConditionsRepo = App::make(SettingDeliveryConditionsRepository::class);
        $theme = $this->fakeSettingDeliveryConditionsData($settingDeliveryConditionsFields);
        return $settingDeliveryConditionsRepo->create($theme);
    }

    /**
     * Get fake instance of SettingDeliveryConditions
     *
     * @param array $settingDeliveryConditionsFields
     * @return SettingDeliveryConditions
     */
    public function fakeSettingDeliveryConditions($settingDeliveryConditionsFields = [])
    {
        return new SettingDeliveryConditions($this->fakeSettingDeliveryConditionsData($settingDeliveryConditionsFields));
    }

    /**
     * Get fake data of SettingDeliveryConditions
     *
     * @param array $settingDeliveryConditionsFields
     * @return array
     */
    public function fakeSettingDeliveryConditionsData($settingDeliveryConditionsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'area_start' => $fake->randomDigitNotNull,
            'area_end' => $fake->randomDigitNotNull,
            'price' => $fake->word,
            'free' => $fake->word,
            'workspace_id' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $settingDeliveryConditionsFields);
    }
}
