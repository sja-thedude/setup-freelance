<?php

use Illuminate\Database\Seeder;

class ThemeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('themes')->insert([
        	[
	            'name' => 'smallpine',
	            'version' => '1.0',
                'author' => 'Priceless-IT',
                'author_url' => 'https://www.priceless-it.be',
	            'description' => 'Default Theme',
	            'image_preview' => 'Screenshot.png',
                'status' => true,
            ]            
        ]);
    }
}
