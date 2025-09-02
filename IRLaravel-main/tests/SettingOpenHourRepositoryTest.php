<?php

use App\SettingOpenHour;
use App\Repositories\SettingOpenHourRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingOpenHourRepositoryTest extends TestCase
{
    use MakeSettingOpenHourTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SettingOpenHourRepository
     */
    protected $settingOpenHourRepo;

    public function setUp()
    {
        parent::setUp();
        $this->settingOpenHourRepo = App::make(SettingOpenHourRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSettingOpenHour()
    {
        $settingOpenHour = $this->fakeSettingOpenHourData();
        $createdSettingOpenHour = $this->settingOpenHourRepo->create($settingOpenHour);
        $createdSettingOpenHour = $createdSettingOpenHour->toArray();
        $this->assertArrayHasKey('id', $createdSettingOpenHour);
        $this->assertNotNull($createdSettingOpenHour['id'], 'Created SettingOpenHour must have id specified');
        $this->assertNotNull(SettingOpenHour::find($createdSettingOpenHour['id']), 'SettingOpenHour with given id must be in DB');
        $this->assertModelData($settingOpenHour, $createdSettingOpenHour);
    }

    /**
     * @test read
     */
    public function testReadSettingOpenHour()
    {
        $settingOpenHour = $this->makeSettingOpenHour();
        $dbSettingOpenHour = $this->settingOpenHourRepo->find($settingOpenHour->id);
        $dbSettingOpenHour = $dbSettingOpenHour->toArray();
        $this->assertModelData($settingOpenHour->toArray(), $dbSettingOpenHour);
    }

    /**
     * @test update
     */
    public function testUpdateSettingOpenHour()
    {
        $settingOpenHour = $this->makeSettingOpenHour();
        $fakeSettingOpenHour = $this->fakeSettingOpenHourData();
        $updatedSettingOpenHour = $this->settingOpenHourRepo->update($fakeSettingOpenHour, $settingOpenHour->id);
        $this->assertModelData($fakeSettingOpenHour, $updatedSettingOpenHour->toArray());
        $dbSettingOpenHour = $this->settingOpenHourRepo->find($settingOpenHour->id);
        $this->assertModelData($fakeSettingOpenHour, $dbSettingOpenHour->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSettingOpenHour()
    {
        $settingOpenHour = $this->makeSettingOpenHour();
        $resp = $this->settingOpenHourRepo->delete($settingOpenHour->id);
        $this->assertTrue($resp);
        $this->assertNull(SettingOpenHour::find($settingOpenHour->id), 'SettingOpenHour should not exist in DB');
    }
}
