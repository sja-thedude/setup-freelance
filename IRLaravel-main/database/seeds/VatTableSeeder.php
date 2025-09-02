<?php

use Illuminate\Database\Seeder;
use App\Models\Vat;
use App\Models\Country;
use Carbon\Carbon;

class VatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('vats')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();
        $countries = Country::all();
        $data = [];
        $locale = config('app.locale');
        $defaultItems = trans('vat.default_items', [], $locale);

        foreach ($countries as $country) {
            $data[] = [
                'key' => array_get($defaultItems, 'prepared_dishes.key'),
                'name' => array_get($defaultItems, 'prepared_dishes.name'),
                'take_out' => 6,
                'delivery' => 6,
                'in_house' => 12,
                'country_id' => $country->id,
                'created_at' => $now,
                'updated_at' => $now
            ];

            $data[] = [
                'key' => array_get($defaultItems, 'dishes_without_preparation.key'),
                'name' => array_get($defaultItems, 'dishes_without_preparation.name'),
                'take_out' => 6,
                'delivery' => 6,
                'in_house' => 21,
                'country_id' => $country->id,
                'created_at' => $now,
                'updated_at' => $now
            ];

            $data[] = [
                'key' => array_get($defaultItems, 'alcoholic_beverages.key'),
                'name' => array_get($defaultItems, 'alcoholic_beverages.name'),
                'take_out' => 21,
                'delivery' => 21,
                'in_house' => 21,
                'country_id' => $country->id,
                'created_at' => $now,
                'updated_at' => $now
            ];

            $data[] = [
                'key' => array_get($defaultItems, 'non_alcoholic_beverages.key'),
                'name' => array_get($defaultItems, 'non_alcoholic_beverages.name'),
                'take_out' => 6,
                'delivery' => 6,
                'in_house' => 21,
                'country_id' => $country->id,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        Vat::insert($data);
    }
}
