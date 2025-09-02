<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoyaltyApiTest extends TestCase
{
    use MakeLoyaltyTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateLoyalty()
    {
        $loyalty = $this->fakeLoyaltyData();
        $this->json('POST', '/api/v1/loyalties', $loyalty);

        $this->assertApiResponse($loyalty);
    }

    /**
     * @test
     */
    public function testReadLoyalty()
    {
        $loyalty = $this->makeLoyalty();
        $this->json('GET', '/api/v1/loyalties/'.$loyalty->id);

        $this->assertApiResponse($loyalty->toArray());
    }

    /**
     * @test
     */
    public function testUpdateLoyalty()
    {
        $loyalty = $this->makeLoyalty();
        $editedLoyalty = $this->fakeLoyaltyData();

        $this->json('PUT', '/api/v1/loyalties/'.$loyalty->id, $editedLoyalty);

        $this->assertApiResponse($editedLoyalty);
    }

    /**
     * @test
     */
    public function testDeleteLoyalty()
    {
        $loyalty = $this->makeLoyalty();
        $this->json('DELETE', '/api/v1/loyalties/'.$loyalty->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/loyalties/'.$loyalty->id);

        $this->assertStatus(404);
    }
}
