<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingPrintApiTest extends TestCase
{
    use MakeSettingPrintTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSettingPrint()
    {
        $settingPrint = $this->fakeSettingPrintData();
        $this->json('POST', '/api/v1/settingPrints', $settingPrint);

        $this->assertApiResponse($settingPrint);
    }

    /**
     * @test
     */
    public function testReadSettingPrint()
    {
        $settingPrint = $this->makeSettingPrint();
        $this->json('GET', '/api/v1/settingPrints/'.$settingPrint->id);

        $this->assertApiResponse($settingPrint->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSettingPrint()
    {
        $settingPrint = $this->makeSettingPrint();
        $editedSettingPrint = $this->fakeSettingPrintData();

        $this->json('PUT', '/api/v1/settingPrints/'.$settingPrint->id, $editedSettingPrint);

        $this->assertApiResponse($editedSettingPrint);
    }

    /**
     * @test
     */
    public function testDeleteSettingPrint()
    {
        $settingPrint = $this->makeSettingPrint();
        $this->json('DELETE', '/api/v1/settingPrints/'.$settingPrint->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/settingPrints/'.$settingPrint->id);

        $this->assertStatus(404);
    }
}
