<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingTimeslotApiTest extends TestCase
{
    use MakeSettingTimeslotTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSettingTimeslot()
    {
        $settingTimeslot = $this->fakeSettingTimeslotData();
        $this->json('POST', '/api/v1/settingTimeslots', $settingTimeslot);

        $this->assertApiResponse($settingTimeslot);
    }

    /**
     * @test
     */
    public function testReadSettingTimeslot()
    {
        $settingTimeslot = $this->makeSettingTimeslot();
        $this->json('GET', '/api/v1/settingTimeslots/'.$settingTimeslot->id);

        $this->assertApiResponse($settingTimeslot->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSettingTimeslot()
    {
        $settingTimeslot = $this->makeSettingTimeslot();
        $editedSettingTimeslot = $this->fakeSettingTimeslotData();

        $this->json('PUT', '/api/v1/settingTimeslots/'.$settingTimeslot->id, $editedSettingTimeslot);

        $this->assertApiResponse($editedSettingTimeslot);
    }

    /**
     * @test
     */
    public function testDeleteSettingTimeslot()
    {
        $settingTimeslot = $this->makeSettingTimeslot();
        $this->json('DELETE', '/api/v1/settingTimeslots/'.$settingTimeslot->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/settingTimeslots/'.$settingTimeslot->id);

        $this->assertStatus(404);
    }
}
