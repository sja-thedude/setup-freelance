<?php

use Illuminate\Database\Seeder;

class FakeCoupons extends Seeder
{
    protected $limit = [
        'coupon' => 30,
        'coupon_category' => 3,
        'coupon_product' => 3,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create coupons and save them to the database
        $limit = array_get($this->limit, 'coupon', 10);
        $coupons = factory(App\Models\Coupon::class, $limit)->create();

        $coupons->each(function ($coupon) {
            /** @var \App\Models\Coupon $coupon */

            // Get random boolean true/false
            $refCategory = rand(0, 1) == 1;

            if ($refCategory) {
                // For max 3 categories
                $limit = array_get($this->limit, 'coupon_category', 3);
                $categories = \App\Models\Category::limit($limit)->pluck('id')->toArray();

                if (count($categories) > 0) {
                    $coupon->categories()->sync($categories);
                }
            }

            // Get random boolean true/false
            $refProduct = rand(0, 1) == 1;

            if ($refProduct) {
                // For max 3 products
                $limit = array_get($this->limit, 'coupon_product', 3);
                $products = \App\Models\Product::limit($limit)->pluck('id')->toArray();

                if (count($products) > 0) {
                    $coupon->products()->sync($products);
                }
            }
        });
    }
}
