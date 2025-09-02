<?php

use App\Helpers\Helper;
use Illuminate\Database\Seeder;
use App\Repositories\CountryRepository;

class CountriesLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = Helper::getActiveLanguages();
        $countryRepository = new CountryRepository(app());
        $countries = DB::table('countries')->get();
        
        if (!$countries->isEmpty()) {
            foreach($countries as $country) {
                //Set default language
                if (!empty($languages)) {
                    $input = [];
                    foreach ($languages as $lang => $language) {
                        $input[$lang] = [
                            'name' => $country->name
                        ];
                        
                        if ($lang == 'nl') {
                            if ($country->name == 'Belgium') {
                                $input['nl'] = [
                                    'name' => 'Belgie'
                                ];
                            } elseif ($country->name == 'Netherlands') {
                                $input['nl'] = [
                                    'name' => 'Nederland'
                                ];
                            } elseif ($country->name == 'Germany') {
                                $input['nl'] = [
                                    'name' => 'Duitsland'
                                ];
                            } elseif ($country->name == 'France') {
                                $input['nl'] = [
                                    'name' => 'Frankrijk'
                                ];
                            } elseif ($country->name == 'Spain') {
                                $input['nl'] = [
                                    'name' => 'Spanje'
                                ];
                            } else {
                                $input['nl'] = [
                                    'name' => $country->name
                                ];
                            }
                        }
                    }
                    
                    if (!empty($input)) {
                        $countryRepository->updateOrCreate(
                            [
                                'id' => $country->id
                            ],
                            $input
                        );
                    }
                }
            }
        }
    }
}
