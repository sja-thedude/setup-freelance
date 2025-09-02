<?php

use Faker\Factory as Faker;
use App\Models\RestaurantCategory;
use App\Repositories\RestaurantCategoryRepository;

trait MakeRestaurantCategoryTrait
{
    /**
     * Create fake instance of RestaurantCategory and save it in database
     *
     * @param array $restaurantCategoryFields
     * @return RestaurantCategory
     */
    public function makeRestaurantCategory($restaurantCategoryFields = [])
    {
        /** @var RestaurantCategoryRepository $restaurantCategoryRepo */
        $restaurantCategoryRepo = App::make(RestaurantCategoryRepository::class);
        $theme = $this->fakeRestaurantCategoryData($restaurantCategoryFields);
        return $restaurantCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of RestaurantCategory
     *
     * @param array $restaurantCategoryFields
     * @return RestaurantCategory
     */
    public function fakeRestaurantCategory($restaurantCategoryFields = [])
    {
        return new RestaurantCategory($this->fakeRestaurantCategoryData($restaurantCategoryFields));
    }

    /**
     * Get fake data of RestaurantCategory
     *
     * @param array $restaurantCategoryFields
     * @return array
     */
    public function fakeRestaurantCategoryData($restaurantCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $restaurantCategoryFields);
    }
}
