<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CityApiTest extends TestCase
{
    use MakeCityTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCity()
    {
        $city = $this->fakeCityData();
        $this->json('POST', '/api/v1/cities', $city);

        $this->assertApiResponse($city);
    }

    /**
     * @test
     */
    public function testReadCity()
    {
        $city = $this->makeCity();
        $this->json('GET', '/api/v1/cities/'.$city->id);

        $this->assertApiResponse($city->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCity()
    {
        $city = $this->makeCity();
        $editedCity = $this->fakeCityData();

        $this->json('PUT', '/api/v1/cities/'.$city->id, $editedCity);

        $this->assertApiResponse($editedCity);
    }

    /**
     * @test
     */
    public function testDeleteCity()
    {
        $city = $this->makeCity();
        $this->json('DELETE', '/api/v1/cities/'.$city->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/cities/'.$city->id);

        $this->assertStatus(404);
    }
}
