<?php

use App\Vat;
use App\Repositories\VatRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VatRepositoryTest extends TestCase
{
    use MakeVatTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatRepository
     */
    protected $vatRepo;

    public function setUp()
    {
        parent::setUp();
        $this->vatRepo = App::make(VatRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateVat()
    {
        $vat = $this->fakeVatData();
        $createdVat = $this->vatRepo->create($vat);
        $createdVat = $createdVat->toArray();
        $this->assertArrayHasKey('id', $createdVat);
        $this->assertNotNull($createdVat['id'], 'Created Vat must have id specified');
        $this->assertNotNull(Vat::find($createdVat['id']), 'Vat with given id must be in DB');
        $this->assertModelData($vat, $createdVat);
    }

    /**
     * @test read
     */
    public function testReadVat()
    {
        $vat = $this->makeVat();
        $dbVat = $this->vatRepo->find($vat->id);
        $dbVat = $dbVat->toArray();
        $this->assertModelData($vat->toArray(), $dbVat);
    }

    /**
     * @test update
     */
    public function testUpdateVat()
    {
        $vat = $this->makeVat();
        $fakeVat = $this->fakeVatData();
        $updatedVat = $this->vatRepo->update($fakeVat, $vat->id);
        $this->assertModelData($fakeVat, $updatedVat->toArray());
        $dbVat = $this->vatRepo->find($vat->id);
        $this->assertModelData($fakeVat, $dbVat->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteVat()
    {
        $vat = $this->makeVat();
        $resp = $this->vatRepo->delete($vat->id);
        $this->assertTrue($resp);
        $this->assertNull(Vat::find($vat->id), 'Vat should not exist in DB');
    }
}
