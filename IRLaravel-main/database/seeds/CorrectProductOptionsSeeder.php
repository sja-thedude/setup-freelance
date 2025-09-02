<?php

use Illuminate\Database\Seeder;

class CorrectProductOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = \App\Models\Product::where(['use_category_option' => 0])->get();

        foreach ($products as $product) {
            $productOptions = $product->productOptions->where('is_checked', 1);
            $category = $product->category;
            if (empty($category)) {
                continue;
            }

            $categoryOptions = $category->categoryOptions->where('is_checked', 1);
            if (!$this->hasSameOptions($productOptions, $categoryOptions)) {
                $product->use_category_option = 1;
                $product->save();
            }
        }
    }

    /**
     * Check if product and category has the same options
     *
     * @param $productOptions
     * @param $categoryOptions
     * @return bool
     */
    protected function hasSameOptions($productOptions, $categoryOptions)
    {
        if ($productOptions->count() != $categoryOptions->count()) {
            return false;
        }

        $countOption = 0;
        foreach ($productOptions as $productOption) {
            $optieId = $productOption->optie_id;
            $isExist = $categoryOptions->where('optie_id', $optieId)->count();
            if ($isExist > 0) {
                $countOption++;
            }
        }

        if ($countOption == $categoryOptions->count()) {
            return true;
        }

        return false;
    }
}
