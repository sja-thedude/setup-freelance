<?php

use App\SettingTimeslot;
use App\Repositories\SettingTimeslotRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingTimeslotRepositoryTest extends TestCase
{
    use MakeSettingTimeslotTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SettingTimeslotRepository
     */
    protected $settingTimeslotRepo;

    public function setUp()
    {
        parent::setUp();
        $this->settingTimeslotRepo = App::make(SettingTimeslotRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSettingTimeslot()
    {
        $settingTimeslot = $this->fakeSettingTimeslotData();
        $createdSettingTimeslot = $this->settingTimeslotRepo->create($settingTimeslot);
        $createdSettingTimeslot = $createdSettingTimeslot->toArray();
        $this->assertArrayHasKey('id', $createdSettingTimeslot);
        $this->assertNotNull($createdSettingTimeslot['id'], 'Created SettingTimeslot must have id specified');
        $this->assertNotNull(SettingTimeslot::find($createdSettingTimeslot['id']), 'SettingTimeslot with given id must be in DB');
        $this->assertModelData($settingTimeslot, $createdSettingTimeslot);
    }

    /**
     * @test read
     */
    public function testReadSettingTimeslot()
    {
        $settingTimeslot = $this->makeSettingTimeslot();
        $dbSettingTimeslot = $this->settingTimeslotRepo->find($settingTimeslot->id);
        $dbSettingTimeslot = $dbSettingTimeslot->toArray();
        $this->assertModelData($settingTimeslot->toArray(), $dbSettingTimeslot);
    }

    /**
     * @test update
     */
    public function testUpdateSettingTimeslot()
    {
        $settingTimeslot = $this->makeSettingTimeslot();
        $fakeSettingTimeslot = $this->fakeSettingTimeslotData();
        $updatedSettingTimeslot = $this->settingTimeslotRepo->update($fakeSettingTimeslot, $settingTimeslot->id);
        $this->assertModelData($fakeSettingTimeslot, $updatedSettingTimeslot->toArray());
        $dbSettingTimeslot = $this->settingTimeslotRepo->find($settingTimeslot->id);
        $this->assertModelData($fakeSettingTimeslot, $dbSettingTimeslot->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSettingTimeslot()
    {
        $settingTimeslot = $this->makeSettingTimeslot();
        $resp = $this->settingTimeslotRepo->delete($settingTimeslot->id);
        $this->assertTrue($resp);
        $this->assertNull(SettingTimeslot::find($settingTimeslot->id), 'SettingTimeslot should not exist in DB');
    }
}
