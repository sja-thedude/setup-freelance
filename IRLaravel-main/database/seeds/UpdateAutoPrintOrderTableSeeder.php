<?php

use Illuminate\Database\Seeder;
use App\Models\Order;

class UpdateAutoPrintOrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::where('auto_print', true)->update([
            'auto_print_sticker' => true,
            'auto_print_werkbon' => true,
            'auto_print_kassabon' => true
        ]);
    }
}
