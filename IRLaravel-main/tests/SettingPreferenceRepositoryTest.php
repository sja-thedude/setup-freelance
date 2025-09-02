<?php

use App\SettingPreference;
use App\Repositories\SettingPreferenceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingPreferenceRepositoryTest extends TestCase
{
    use MakeSettingPreferenceTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SettingPreferenceRepository
     */
    protected $settingPreferenceRepo;

    public function setUp()
    {
        parent::setUp();
        $this->settingPreferenceRepo = App::make(SettingPreferenceRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSettingPreference()
    {
        $settingPreference = $this->fakeSettingPreferenceData();
        $createdSettingPreference = $this->settingPreferenceRepo->create($settingPreference);
        $createdSettingPreference = $createdSettingPreference->toArray();
        $this->assertArrayHasKey('id', $createdSettingPreference);
        $this->assertNotNull($createdSettingPreference['id'], 'Created SettingPreference must have id specified');
        $this->assertNotNull(SettingPreference::find($createdSettingPreference['id']), 'SettingPreference with given id must be in DB');
        $this->assertModelData($settingPreference, $createdSettingPreference);
    }

    /**
     * @test read
     */
    public function testReadSettingPreference()
    {
        $settingPreference = $this->makeSettingPreference();
        $dbSettingPreference = $this->settingPreferenceRepo->find($settingPreference->id);
        $dbSettingPreference = $dbSettingPreference->toArray();
        $this->assertModelData($settingPreference->toArray(), $dbSettingPreference);
    }

    /**
     * @test update
     */
    public function testUpdateSettingPreference()
    {
        $settingPreference = $this->makeSettingPreference();
        $fakeSettingPreference = $this->fakeSettingPreferenceData();
        $updatedSettingPreference = $this->settingPreferenceRepo->update($fakeSettingPreference, $settingPreference->id);
        $this->assertModelData($fakeSettingPreference, $updatedSettingPreference->toArray());
        $dbSettingPreference = $this->settingPreferenceRepo->find($settingPreference->id);
        $this->assertModelData($fakeSettingPreference, $dbSettingPreference->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSettingPreference()
    {
        $settingPreference = $this->makeSettingPreference();
        $resp = $this->settingPreferenceRepo->delete($settingPreference->id);
        $this->assertTrue($resp);
        $this->assertNull(SettingPreference::find($settingPreference->id), 'SettingPreference should not exist in DB');
    }
}
