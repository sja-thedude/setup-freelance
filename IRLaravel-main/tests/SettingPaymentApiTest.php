<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SettingPaymentApiTest extends TestCase
{
    use MakeSettingPaymentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSettingPayment()
    {
        $settingPayment = $this->fakeSettingPaymentData();
        $this->json('POST', '/api/v1/settingPayments', $settingPayment);

        $this->assertApiResponse($settingPayment);
    }

    /**
     * @test
     */
    public function testReadSettingPayment()
    {
        $settingPayment = $this->makeSettingPayment();
        $this->json('GET', '/api/v1/settingPayments/'.$settingPayment->id);

        $this->assertApiResponse($settingPayment->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSettingPayment()
    {
        $settingPayment = $this->makeSettingPayment();
        $editedSettingPayment = $this->fakeSettingPaymentData();

        $this->json('PUT', '/api/v1/settingPayments/'.$settingPayment->id, $editedSettingPayment);

        $this->assertApiResponse($editedSettingPayment);
    }

    /**
     * @test
     */
    public function testDeleteSettingPayment()
    {
        $settingPayment = $this->makeSettingPayment();
        $this->json('DELETE', '/api/v1/settingPayments/'.$settingPayment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/settingPayments/'.$settingPayment->id);

        $this->assertStatus(404);
    }
}
