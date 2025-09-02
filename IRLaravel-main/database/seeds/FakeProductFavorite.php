<?php

use Illuminate\Database\Seeder;

class FakeProductFavorite extends Seeder
{
    protected $limit = [
        'category' => 10,
        'product' => 30,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create restaurant categories and save them to the database
        $categories = factory(App\Models\Category::class, array_get($this->limit, 'category', 10))->create();

        // Create workspaces and save them to the database
        $products = factory(App\Models\Product::class, array_get($this->limit, 'product', 10))->create()
            ->each(function ($product) {
                /** @var \App\Models\Product $product */
                $randomUsers = App\Models\User::inRandomOrder()->limit(5)->get();
                $product->productFavorites()->sync($randomUsers);
            });
    }
}
