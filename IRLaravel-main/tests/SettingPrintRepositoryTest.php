<?php

use App\SettingPrint;
use App\Repositories\SettingPrintRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingPrintRepositoryTest extends TestCase
{
    use MakeSettingPrintTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SettingPrintRepository
     */
    protected $settingPrintRepo;

    public function setUp()
    {
        parent::setUp();
        $this->settingPrintRepo = App::make(SettingPrintRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSettingPrint()
    {
        $settingPrint = $this->fakeSettingPrintData();
        $createdSettingPrint = $this->settingPrintRepo->create($settingPrint);
        $createdSettingPrint = $createdSettingPrint->toArray();
        $this->assertArrayHasKey('id', $createdSettingPrint);
        $this->assertNotNull($createdSettingPrint['id'], 'Created SettingPrint must have id specified');
        $this->assertNotNull(SettingPrint::find($createdSettingPrint['id']), 'SettingPrint with given id must be in DB');
        $this->assertModelData($settingPrint, $createdSettingPrint);
    }

    /**
     * @test read
     */
    public function testReadSettingPrint()
    {
        $settingPrint = $this->makeSettingPrint();
        $dbSettingPrint = $this->settingPrintRepo->find($settingPrint->id);
        $dbSettingPrint = $dbSettingPrint->toArray();
        $this->assertModelData($settingPrint->toArray(), $dbSettingPrint);
    }

    /**
     * @test update
     */
    public function testUpdateSettingPrint()
    {
        $settingPrint = $this->makeSettingPrint();
        $fakeSettingPrint = $this->fakeSettingPrintData();
        $updatedSettingPrint = $this->settingPrintRepo->update($fakeSettingPrint, $settingPrint->id);
        $this->assertModelData($fakeSettingPrint, $updatedSettingPrint->toArray());
        $dbSettingPrint = $this->settingPrintRepo->find($settingPrint->id);
        $this->assertModelData($fakeSettingPrint, $dbSettingPrint->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSettingPrint()
    {
        $settingPrint = $this->makeSettingPrint();
        $resp = $this->settingPrintRepo->delete($settingPrint->id);
        $this->assertTrue($resp);
        $this->assertNull(SettingPrint::find($settingPrint->id), 'SettingPrint should not exist in DB');
    }
}
