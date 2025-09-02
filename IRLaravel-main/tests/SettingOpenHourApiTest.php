<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingOpenHourApiTest extends TestCase
{
    use MakeSettingOpenHourTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSettingOpenHour()
    {
        $settingOpenHour = $this->fakeSettingOpenHourData();
        $this->json('POST', '/api/v1/settingOpenHours', $settingOpenHour);

        $this->assertApiResponse($settingOpenHour);
    }

    /**
     * @test
     */
    public function testReadSettingOpenHour()
    {
        $settingOpenHour = $this->makeSettingOpenHour();
        $this->json('GET', '/api/v1/settingOpenHours/'.$settingOpenHour->id);

        $this->assertApiResponse($settingOpenHour->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSettingOpenHour()
    {
        $settingOpenHour = $this->makeSettingOpenHour();
        $editedSettingOpenHour = $this->fakeSettingOpenHourData();

        $this->json('PUT', '/api/v1/settingOpenHours/'.$settingOpenHour->id, $editedSettingOpenHour);

        $this->assertApiResponse($editedSettingOpenHour);
    }

    /**
     * @test
     */
    public function testDeleteSettingOpenHour()
    {
        $settingOpenHour = $this->makeSettingOpenHour();
        $this->json('DELETE', '/api/v1/settingOpenHours/'.$settingOpenHour->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/settingOpenHours/'.$settingOpenHour->id);

        $this->assertStatus(404);
    }
}
