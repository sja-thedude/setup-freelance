<?php

use App\SettingGeneral;
use App\Repositories\SettingGeneralRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingGeneralRepositoryTest extends TestCase
{
    use MakeSettingGeneralTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SettingGeneralRepository
     */
    protected $settingGeneralRepo;

    public function setUp()
    {
        parent::setUp();
        $this->settingGeneralRepo = App::make(SettingGeneralRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSettingGeneral()
    {
        $settingGeneral = $this->fakeSettingGeneralData();
        $createdSettingGeneral = $this->settingGeneralRepo->create($settingGeneral);
        $createdSettingGeneral = $createdSettingGeneral->toArray();
        $this->assertArrayHasKey('id', $createdSettingGeneral);
        $this->assertNotNull($createdSettingGeneral['id'], 'Created SettingGeneral must have id specified');
        $this->assertNotNull(SettingGeneral::find($createdSettingGeneral['id']), 'SettingGeneral with given id must be in DB');
        $this->assertModelData($settingGeneral, $createdSettingGeneral);
    }

    /**
     * @test read
     */
    public function testReadSettingGeneral()
    {
        $settingGeneral = $this->makeSettingGeneral();
        $dbSettingGeneral = $this->settingGeneralRepo->find($settingGeneral->id);
        $dbSettingGeneral = $dbSettingGeneral->toArray();
        $this->assertModelData($settingGeneral->toArray(), $dbSettingGeneral);
    }

    /**
     * @test update
     */
    public function testUpdateSettingGeneral()
    {
        $settingGeneral = $this->makeSettingGeneral();
        $fakeSettingGeneral = $this->fakeSettingGeneralData();
        $updatedSettingGeneral = $this->settingGeneralRepo->update($fakeSettingGeneral, $settingGeneral->id);
        $this->assertModelData($fakeSettingGeneral, $updatedSettingGeneral->toArray());
        $dbSettingGeneral = $this->settingGeneralRepo->find($settingGeneral->id);
        $this->assertModelData($fakeSettingGeneral, $dbSettingGeneral->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSettingGeneral()
    {
        $settingGeneral = $this->makeSettingGeneral();
        $resp = $this->settingGeneralRepo->delete($settingGeneral->id);
        $this->assertTrue($resp);
        $this->assertNull(SettingGeneral::find($settingGeneral->id), 'SettingGeneral should not exist in DB');
    }
}
