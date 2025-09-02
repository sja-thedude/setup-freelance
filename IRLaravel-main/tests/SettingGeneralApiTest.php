<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingGeneralApiTest extends TestCase
{
    use MakeSettingGeneralTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSettingGeneral()
    {
        $settingGeneral = $this->fakeSettingGeneralData();
        $this->json('POST', '/api/v1/settingGenerals', $settingGeneral);

        $this->assertApiResponse($settingGeneral);
    }

    /**
     * @test
     */
    public function testReadSettingGeneral()
    {
        $settingGeneral = $this->makeSettingGeneral();
        $this->json('GET', '/api/v1/settingGenerals/'.$settingGeneral->id);

        $this->assertApiResponse($settingGeneral->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSettingGeneral()
    {
        $settingGeneral = $this->makeSettingGeneral();
        $editedSettingGeneral = $this->fakeSettingGeneralData();

        $this->json('PUT', '/api/v1/settingGenerals/'.$settingGeneral->id, $editedSettingGeneral);

        $this->assertApiResponse($editedSettingGeneral);
    }

    /**
     * @test
     */
    public function testDeleteSettingGeneral()
    {
        $settingGeneral = $this->makeSettingGeneral();
        $this->json('DELETE', '/api/v1/settingGenerals/'.$settingGeneral->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/settingGenerals/'.$settingGeneral->id);

        $this->assertStatus(404);
    }
}
