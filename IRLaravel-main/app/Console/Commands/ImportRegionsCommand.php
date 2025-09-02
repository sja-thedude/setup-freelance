<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use App\Models\Country;
use App\Models\Address;

class ImportRegionsCommand extends Command
{
    protected $signature = 'import:regions';

    protected $description = 'Command description';

    public function handle()
    {
        $this->warn('Importing regions...');

        $countryCode = 'be';
        $countryName = 'Belgium';

        // Find country by code
        $country = Country::where('code', $countryCode)
            ->first();
            
        if (empty($country)) {
            // Create new country if not exist
            $country = Country::create([
                'code' => $countryCode,
                'name' => $countryName,
            ]);
        }

        $countryId = $country->id;

        // Import data from .csv file
        $filePath = storage_path('regions/be/AN2012-1-NL23.csv');
        $file = fopen($filePath, 'r');

        if ($file) {
            $this->warn('Processing .csv file...');

            $firstRound = true;
            $index = 0;

            while (($data = fgetcsv($file)) !== false) {
                // Process each row of data
                $this->info((++$index) . "\t-> " . implode(' | ', $data));

                if ($firstRound) {
                    $firstRound = false;
                    continue;
                }
                
                // Columns: 0 = postcode, 1 = address, 2 = city.
                $postcode = $data[0];
                $address = $data[1];
                $city = $data[2];

                // Create new city if not exist
                $city = City::firstOrCreate([
                    'country_id' => $countryId,
                    'name' => $city,
                ]);

                $this->info("--> City: #{$city->id} - {$city->name}");

                // Create new address if not exist
                /** @var Address $address */
                $address = Address::updateOrCreate([
                    'city_id' => $city->id,
                    'postcode' => $postcode,
                    'address' => $address,
                ], []);

                $this->info("--> Address: #{$address->id} - ({$address->postcode}) {$address->address}");
            }

            fclose($file);
        } else {
            $this->error('Failed to open the .csv file');
        }

        $this->info('Importing regions completed');
    }
}
