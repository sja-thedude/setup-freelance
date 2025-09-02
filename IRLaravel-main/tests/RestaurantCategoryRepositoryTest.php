<?php

use App\RestaurantCategory;
use App\Repositories\RestaurantCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RestaurantCategoryRepositoryTest extends TestCase
{
    use MakeRestaurantCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var RestaurantCategoryRepository
     */
    protected $restaurantCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->restaurantCategoryRepo = App::make(RestaurantCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateRestaurantCategory()
    {
        $restaurantCategory = $this->fakeRestaurantCategoryData();
        $createdRestaurantCategory = $this->restaurantCategoryRepo->create($restaurantCategory);
        $createdRestaurantCategory = $createdRestaurantCategory->toArray();
        $this->assertArrayHasKey('id', $createdRestaurantCategory);
        $this->assertNotNull($createdRestaurantCategory['id'], 'Created RestaurantCategory must have id specified');
        $this->assertNotNull(RestaurantCategory::find($createdRestaurantCategory['id']), 'RestaurantCategory with given id must be in DB');
        $this->assertModelData($restaurantCategory, $createdRestaurantCategory);
    }

    /**
     * @test read
     */
    public function testReadRestaurantCategory()
    {
        $restaurantCategory = $this->makeRestaurantCategory();
        $dbRestaurantCategory = $this->restaurantCategoryRepo->find($restaurantCategory->id);
        $dbRestaurantCategory = $dbRestaurantCategory->toArray();
        $this->assertModelData($restaurantCategory->toArray(), $dbRestaurantCategory);
    }

    /**
     * @test update
     */
    public function testUpdateRestaurantCategory()
    {
        $restaurantCategory = $this->makeRestaurantCategory();
        $fakeRestaurantCategory = $this->fakeRestaurantCategoryData();
        $updatedRestaurantCategory = $this->restaurantCategoryRepo->update($fakeRestaurantCategory, $restaurantCategory->id);
        $this->assertModelData($fakeRestaurantCategory, $updatedRestaurantCategory->toArray());
        $dbRestaurantCategory = $this->restaurantCategoryRepo->find($restaurantCategory->id);
        $this->assertModelData($fakeRestaurantCategory, $dbRestaurantCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteRestaurantCategory()
    {
        $restaurantCategory = $this->makeRestaurantCategory();
        $resp = $this->restaurantCategoryRepo->delete($restaurantCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(RestaurantCategory::find($restaurantCategory->id), 'RestaurantCategory should not exist in DB');
    }
}
