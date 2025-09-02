<?php

use Illuminate\Database\Seeder;

/**
 * Class FakeRestaurantCategory
 */
class FakeRestaurantCategory extends Seeder
{
    protected $limit = [
        'country' => 10,
        'category' => 10,
        'workspace' => 10,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create countries and save them to the database
        $countries = factory(App\Models\Country::class, array_get($this->limit, 'country', 10))->create();

        // Create restaurant categories and save them to the database
        $categories = factory(App\Models\RestaurantCategory::class, array_get($this->limit, 'category', 10))->create();

        // Create workspaces and save them to the database
        $workspaces = factory(App\Models\Workspace::class, array_get($this->limit, 'workspace', 10))->create()
            ->each(function ($workspace) {
                /** @var \App\Models\Workspace $workspace */
                $randomCategories = App\Models\RestaurantCategory::inRandomOrder()->limit(3)->get();
                $workspace->workspaceCategories()->sync($randomCategories);
            });
    }
}
