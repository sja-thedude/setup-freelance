<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingPreferenceApiTest extends TestCase
{
    use MakeSettingPreferenceTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSettingPreference()
    {
        $settingPreference = $this->fakeSettingPreferenceData();
        $this->json('POST', '/api/v1/settingPreferences', $settingPreference);

        $this->assertApiResponse($settingPreference);
    }

    /**
     * @test
     */
    public function testReadSettingPreference()
    {
        $settingPreference = $this->makeSettingPreference();
        $this->json('GET', '/api/v1/settingPreferences/'.$settingPreference->id);

        $this->assertApiResponse($settingPreference->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSettingPreference()
    {
        $settingPreference = $this->makeSettingPreference();
        $editedSettingPreference = $this->fakeSettingPreferenceData();

        $this->json('PUT', '/api/v1/settingPreferences/'.$settingPreference->id, $editedSettingPreference);

        $this->assertApiResponse($editedSettingPreference);
    }

    /**
     * @test
     */
    public function testDeleteSettingPreference()
    {
        $settingPreference = $this->makeSettingPreference();
        $this->json('DELETE', '/api/v1/settingPreferences/'.$settingPreference->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/settingPreferences/'.$settingPreference->id);

        $this->assertStatus(404);
    }
}
