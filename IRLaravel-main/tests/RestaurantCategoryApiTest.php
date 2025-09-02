<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantCategoryApiTest extends TestCase
{
    use MakeRestaurantCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateRestaurantCategory()
    {
        $restaurantCategory = $this->fakeRestaurantCategoryData();
        $this->json('POST', '/api/v1/restaurantCategories', $restaurantCategory);

        $this->assertApiResponse($restaurantCategory);
    }

    /**
     * @test
     */
    public function testReadRestaurantCategory()
    {
        $restaurantCategory = $this->makeRestaurantCategory();
        $this->json('GET', '/api/v1/restaurantCategories/'.$restaurantCategory->id);

        $this->assertApiResponse($restaurantCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateRestaurantCategory()
    {
        $restaurantCategory = $this->makeRestaurantCategory();
        $editedRestaurantCategory = $this->fakeRestaurantCategoryData();

        $this->json('PUT', '/api/v1/restaurantCategories/'.$restaurantCategory->id, $editedRestaurantCategory);

        $this->assertApiResponse($editedRestaurantCategory);
    }

    /**
     * @test
     */
    public function testDeleteRestaurantCategory()
    {
        $restaurantCategory = $this->makeRestaurantCategory();
        $this->json('DELETE', '/api/v1/restaurantCategories/'.$restaurantCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/restaurantCategories/'.$restaurantCategory->id);

        $this->assertStatus(404);
    }
}
