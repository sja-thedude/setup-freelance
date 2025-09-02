<?php

use App\SettingTimeslotDetail;
use App\Repositories\SettingTimeslotDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingTimeslotDetailRepositoryTest extends TestCase
{
    use MakeSettingTimeslotDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SettingTimeslotDetailRepository
     */
    protected $settingTimeslotDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->settingTimeslotDetailRepo = App::make(SettingTimeslotDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSettingTimeslotDetail()
    {
        $settingTimeslotDetail = $this->fakeSettingTimeslotDetailData();
        $createdSettingTimeslotDetail = $this->settingTimeslotDetailRepo->create($settingTimeslotDetail);
        $createdSettingTimeslotDetail = $createdSettingTimeslotDetail->toArray();
        $this->assertArrayHasKey('id', $createdSettingTimeslotDetail);
        $this->assertNotNull($createdSettingTimeslotDetail['id'], 'Created SettingTimeslotDetail must have id specified');
        $this->assertNotNull(SettingTimeslotDetail::find($createdSettingTimeslotDetail['id']), 'SettingTimeslotDetail with given id must be in DB');
        $this->assertModelData($settingTimeslotDetail, $createdSettingTimeslotDetail);
    }

    /**
     * @test read
     */
    public function testReadSettingTimeslotDetail()
    {
        $settingTimeslotDetail = $this->makeSettingTimeslotDetail();
        $dbSettingTimeslotDetail = $this->settingTimeslotDetailRepo->find($settingTimeslotDetail->id);
        $dbSettingTimeslotDetail = $dbSettingTimeslotDetail->toArray();
        $this->assertModelData($settingTimeslotDetail->toArray(), $dbSettingTimeslotDetail);
    }

    /**
     * @test update
     */
    public function testUpdateSettingTimeslotDetail()
    {
        $settingTimeslotDetail = $this->makeSettingTimeslotDetail();
        $fakeSettingTimeslotDetail = $this->fakeSettingTimeslotDetailData();
        $updatedSettingTimeslotDetail = $this->settingTimeslotDetailRepo->update($fakeSettingTimeslotDetail, $settingTimeslotDetail->id);
        $this->assertModelData($fakeSettingTimeslotDetail, $updatedSettingTimeslotDetail->toArray());
        $dbSettingTimeslotDetail = $this->settingTimeslotDetailRepo->find($settingTimeslotDetail->id);
        $this->assertModelData($fakeSettingTimeslotDetail, $dbSettingTimeslotDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSettingTimeslotDetail()
    {
        $settingTimeslotDetail = $this->makeSettingTimeslotDetail();
        $resp = $this->settingTimeslotDetailRepo->delete($settingTimeslotDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(SettingTimeslotDetail::find($settingTimeslotDetail->id), 'SettingTimeslotDetail should not exist in DB');
    }
}
