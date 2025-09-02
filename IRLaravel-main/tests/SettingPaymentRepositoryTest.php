<?php

use App\SettingPayment;
use App\Repositories\SettingPaymentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingPaymentRepositoryTest extends TestCase
{
    use MakeSettingPaymentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SettingPaymentRepository
     */
    protected $settingPaymentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->settingPaymentRepo = App::make(SettingPaymentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSettingPayment()
    {
        $settingPayment = $this->fakeSettingPaymentData();
        $createdSettingPayment = $this->settingPaymentRepo->create($settingPayment);
        $createdSettingPayment = $createdSettingPayment->toArray();
        $this->assertArrayHasKey('id', $createdSettingPayment);
        $this->assertNotNull($createdSettingPayment['id'], 'Created SettingPayment must have id specified');
        $this->assertNotNull(SettingPayment::find($createdSettingPayment['id']), 'SettingPayment with given id must be in DB');
        $this->assertModelData($settingPayment, $createdSettingPayment);
    }

    /**
     * @test read
     */
    public function testReadSettingPayment()
    {
        $settingPayment = $this->makeSettingPayment();
        $dbSettingPayment = $this->settingPaymentRepo->find($settingPayment->id);
        $dbSettingPayment = $dbSettingPayment->toArray();
        $this->assertModelData($settingPayment->toArray(), $dbSettingPayment);
    }

    /**
     * @test update
     */
    public function testUpdateSettingPayment()
    {
        $settingPayment = $this->makeSettingPayment();
        $fakeSettingPayment = $this->fakeSettingPaymentData();
        $updatedSettingPayment = $this->settingPaymentRepo->update($fakeSettingPayment, $settingPayment->id);
        $this->assertModelData($fakeSettingPayment, $updatedSettingPayment->toArray());
        $dbSettingPayment = $this->settingPaymentRepo->find($settingPayment->id);
        $this->assertModelData($fakeSettingPayment, $dbSettingPayment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSettingPayment()
    {
        $settingPayment = $this->makeSettingPayment();
        $resp = $this->settingPaymentRepo->delete($settingPayment->id);
        $this->assertTrue($resp);
        $this->assertNull(SettingPayment::find($settingPayment->id), 'SettingPayment should not exist in DB');
    }
}
