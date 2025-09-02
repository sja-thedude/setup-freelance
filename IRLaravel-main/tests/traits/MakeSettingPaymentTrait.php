<?php

use Faker\Factory as Faker;
use App\Models\SettingPayment;
use App\Repositories\SettingPaymentRepository;

trait MakeSettingPaymentTrait
{
    /**
     * Create fake instance of SettingPayment and save it in database
     *
     * @param array $settingPaymentFields
     * @return SettingPayment
     */
    public function makeSettingPayment($settingPaymentFields = [])
    {
        /** @var SettingPaymentRepository $settingPaymentRepo */
        $settingPaymentRepo = App::make(SettingPaymentRepository::class);
        $theme = $this->fakeSettingPaymentData($settingPaymentFields);
        return $settingPaymentRepo->create($theme);
    }

    /**
     * Get fake instance of SettingPayment
     *
     * @param array $settingPaymentFields
     * @return SettingPayment
     */
    public function fakeSettingPayment($settingPaymentFields = [])
    {
        return new SettingPayment($this->fakeSettingPaymentData($settingPaymentFields));
    }

    /**
     * Get fake data of SettingPayment
     *
     * @param array $settingPaymentFields
     * @return array
     */
    public function fakeSettingPaymentData($settingPaymentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'workspace_id' => $fake->word,
            'type' => $fake->word,
            'api_token' => $fake->word,
            'takeout' => $fake->word,
            'delivery' => $fake->word,
            'in_house' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $settingPaymentFields);
    }
}
