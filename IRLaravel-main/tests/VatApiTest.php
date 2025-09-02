<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VatApiTest extends TestCase
{
    use MakeVatTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateVat()
    {
        $vat = $this->fakeVatData();
        $this->json('POST', '/api/v1/vats', $vat);

        $this->assertApiResponse($vat);
    }

    /**
     * @test
     */
    public function testReadVat()
    {
        $vat = $this->makeVat();
        $this->json('GET', '/api/v1/vats/'.$vat->id);

        $this->assertApiResponse($vat->toArray());
    }

    /**
     * @test
     */
    public function testUpdateVat()
    {
        $vat = $this->makeVat();
        $editedVat = $this->fakeVatData();

        $this->json('PUT', '/api/v1/vats/'.$vat->id, $editedVat);

        $this->assertApiResponse($editedVat);
    }

    /**
     * @test
     */
    public function testDeleteVat()
    {
        $vat = $this->makeVat();
        $this->json('DELETE', '/api/v1/vats/'.$vat->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/vats/'.$vat->id);

        $this->assertStatus(404);
    }
}
