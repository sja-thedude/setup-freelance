<?php

use App\Models\Allergenen;
use Illuminate\Database\Seeder;

class TruncateCartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable FOREIGN_KEY_CHECKS
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \DB::table('cart_option_items')->truncate();
        \DB::table('cart_items')->truncate();
        \DB::table('carts')->truncate();

        // Enable FOREIGN_KEY_CHECKS again
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
