<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingDeliveryConditionsApiTest extends TestCase
{
    use MakeSettingDeliveryConditionsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSettingDeliveryConditions()
    {
        $settingDeliveryConditions = $this->fakeSettingDeliveryConditionsData();
        $this->json('POST', '/api/v1/settingDeliveryConditions', $settingDeliveryConditions);

        $this->assertApiResponse($settingDeliveryConditions);
    }

    /**
     * @test
     */
    public function testReadSettingDeliveryConditions()
    {
        $settingDeliveryConditions = $this->makeSettingDeliveryConditions();
        $this->json('GET', '/api/v1/settingDeliveryConditions/'.$settingDeliveryConditions->id);

        $this->assertApiResponse($settingDeliveryConditions->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSettingDeliveryConditions()
    {
        $settingDeliveryConditions = $this->makeSettingDeliveryConditions();
        $editedSettingDeliveryConditions = $this->fakeSettingDeliveryConditionsData();

        $this->json('PUT', '/api/v1/settingDeliveryConditions/'.$settingDeliveryConditions->id, $editedSettingDeliveryConditions);

        $this->assertApiResponse($editedSettingDeliveryConditions);
    }

    /**
     * @test
     */
    public function testDeleteSettingDeliveryConditions()
    {
        $settingDeliveryConditions = $this->makeSettingDeliveryConditions();
        $this->json('DELETE', '/api/v1/settingDeliveryConditions/'.$settingDeliveryConditions->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/settingDeliveryConditions/'.$settingDeliveryConditions->id);

        $this->assertStatus(404);
    }
}
