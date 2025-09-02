<?php

use App\Loyalty;
use App\Repositories\LoyaltyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoyaltyRepositoryTest extends TestCase
{
    use MakeLoyaltyTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LoyaltyRepository
     */
    protected $loyaltyRepo;

    public function setUp()
    {
        parent::setUp();
        $this->loyaltyRepo = App::make(LoyaltyRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateLoyalty()
    {
        $loyalty = $this->fakeLoyaltyData();
        $createdLoyalty = $this->loyaltyRepo->create($loyalty);
        $createdLoyalty = $createdLoyalty->toArray();
        $this->assertArrayHasKey('id', $createdLoyalty);
        $this->assertNotNull($createdLoyalty['id'], 'Created Loyalty must have id specified');
        $this->assertNotNull(Loyalty::find($createdLoyalty['id']), 'Loyalty with given id must be in DB');
        $this->assertModelData($loyalty, $createdLoyalty);
    }

    /**
     * @test read
     */
    public function testReadLoyalty()
    {
        $loyalty = $this->makeLoyalty();
        $dbLoyalty = $this->loyaltyRepo->find($loyalty->id);
        $dbLoyalty = $dbLoyalty->toArray();
        $this->assertModelData($loyalty->toArray(), $dbLoyalty);
    }

    /**
     * @test update
     */
    public function testUpdateLoyalty()
    {
        $loyalty = $this->makeLoyalty();
        $fakeLoyalty = $this->fakeLoyaltyData();
        $updatedLoyalty = $this->loyaltyRepo->update($fakeLoyalty, $loyalty->id);
        $this->assertModelData($fakeLoyalty, $updatedLoyalty->toArray());
        $dbLoyalty = $this->loyaltyRepo->find($loyalty->id);
        $this->assertModelData($fakeLoyalty, $dbLoyalty->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteLoyalty()
    {
        $loyalty = $this->makeLoyalty();
        $resp = $this->loyaltyRepo->delete($loyalty->id);
        $this->assertTrue($resp);
        $this->assertNull(Loyalty::find($loyalty->id), 'Loyalty should not exist in DB');
    }
}
