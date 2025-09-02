<?php

use App\SettingDeliveryConditions;
use App\Repositories\SettingDeliveryConditionsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingDeliveryConditionsRepositoryTest extends TestCase
{
    use MakeSettingDeliveryConditionsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SettingDeliveryConditionsRepository
     */
    protected $settingDeliveryConditionsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->settingDeliveryConditionsRepo = App::make(SettingDeliveryConditionsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSettingDeliveryConditions()
    {
        $settingDeliveryConditions = $this->fakeSettingDeliveryConditionsData();
        $createdSettingDeliveryConditions = $this->settingDeliveryConditionsRepo->create($settingDeliveryConditions);
        $createdSettingDeliveryConditions = $createdSettingDeliveryConditions->toArray();
        $this->assertArrayHasKey('id', $createdSettingDeliveryConditions);
        $this->assertNotNull($createdSettingDeliveryConditions['id'], 'Created SettingDeliveryConditions must have id specified');
        $this->assertNotNull(SettingDeliveryConditions::find($createdSettingDeliveryConditions['id']), 'SettingDeliveryConditions with given id must be in DB');
        $this->assertModelData($settingDeliveryConditions, $createdSettingDeliveryConditions);
    }

    /**
     * @test read
     */
    public function testReadSettingDeliveryConditions()
    {
        $settingDeliveryConditions = $this->makeSettingDeliveryConditions();
        $dbSettingDeliveryConditions = $this->settingDeliveryConditionsRepo->find($settingDeliveryConditions->id);
        $dbSettingDeliveryConditions = $dbSettingDeliveryConditions->toArray();
        $this->assertModelData($settingDeliveryConditions->toArray(), $dbSettingDeliveryConditions);
    }

    /**
     * @test update
     */
    public function testUpdateSettingDeliveryConditions()
    {
        $settingDeliveryConditions = $this->makeSettingDeliveryConditions();
        $fakeSettingDeliveryConditions = $this->fakeSettingDeliveryConditionsData();
        $updatedSettingDeliveryConditions = $this->settingDeliveryConditionsRepo->update($fakeSettingDeliveryConditions, $settingDeliveryConditions->id);
        $this->assertModelData($fakeSettingDeliveryConditions, $updatedSettingDeliveryConditions->toArray());
        $dbSettingDeliveryConditions = $this->settingDeliveryConditionsRepo->find($settingDeliveryConditions->id);
        $this->assertModelData($fakeSettingDeliveryConditions, $dbSettingDeliveryConditions->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSettingDeliveryConditions()
    {
        $settingDeliveryConditions = $this->makeSettingDeliveryConditions();
        $resp = $this->settingDeliveryConditionsRepo->delete($settingDeliveryConditions->id);
        $this->assertTrue($resp);
        $this->assertNull(SettingDeliveryConditions::find($settingDeliveryConditions->id), 'SettingDeliveryConditions should not exist in DB');
    }
}
