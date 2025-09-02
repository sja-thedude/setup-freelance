<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingTimeslotDetailApiTest extends TestCase
{
    use MakeSettingTimeslotDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSettingTimeslotDetail()
    {
        $settingTimeslotDetail = $this->fakeSettingTimeslotDetailData();
        $this->json('POST', '/api/v1/settingTimeslotDetails', $settingTimeslotDetail);

        $this->assertApiResponse($settingTimeslotDetail);
    }

    /**
     * @test
     */
    public function testReadSettingTimeslotDetail()
    {
        $settingTimeslotDetail = $this->makeSettingTimeslotDetail();
        $this->json('GET', '/api/v1/settingTimeslotDetails/'.$settingTimeslotDetail->id);

        $this->assertApiResponse($settingTimeslotDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSettingTimeslotDetail()
    {
        $settingTimeslotDetail = $this->makeSettingTimeslotDetail();
        $editedSettingTimeslotDetail = $this->fakeSettingTimeslotDetailData();

        $this->json('PUT', '/api/v1/settingTimeslotDetails/'.$settingTimeslotDetail->id, $editedSettingTimeslotDetail);

        $this->assertApiResponse($editedSettingTimeslotDetail);
    }

    /**
     * @test
     */
    public function testDeleteSettingTimeslotDetail()
    {
        $settingTimeslotDetail = $this->makeSettingTimeslotDetail();
        $this->json('DELETE', '/api/v1/settingTimeslotDetails/'.$settingTimeslotDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/settingTimeslotDetails/'.$settingTimeslotDetail->id);

        $this->assertStatus(404);
    }
}
