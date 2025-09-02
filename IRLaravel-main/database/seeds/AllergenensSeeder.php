<?php

use App\Models\Allergenen;
use Illuminate\Database\Seeder;

class AllergenensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();
        $allergenens = [
            [
                'id' => 1,
                'icon' => '/assets/images/icons/allergenen/gray/ei.png',
                'type' => Allergenen::TYPE_EI,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 2,
                'icon' => '/assets/images/icons/allergenen/gray/gluten.png',
                'type' => Allergenen::TYPE_GLUTEN,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 3,
                'icon' => '/assets/images/icons/allergenen/gray/lupine.png',
                'type' => Allergenen::TYPE_LUPINE,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 4,
                'icon' => '/assets/images/icons/allergenen/gray/melk.png',
                'type' => Allergenen::TYPE_MELK,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 5,
                'icon' => '/assets/images/icons/allergenen/gray/mosterd.png',
                'type' => Allergenen::TYPE_MOSTERD,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 6,
                'icon' => '/assets/images/icons/allergenen/gray/noten.png',
                'type' => Allergenen::TYPE_NOTEN,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 7,
                'icon' => '/assets/images/icons/allergenen/gray/pindas.png',
                'type' => Allergenen::TYPE_PINDAS,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 8,
                'icon' => '/assets/images/icons/allergenen/gray/schaald.png',
                'type' => Allergenen::TYPE_SCHAALD,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 9,
                'icon' => '/assets/images/icons/allergenen/gray/selderij.png',
                'type' => Allergenen::TYPE_SELDERIJ,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 10,
                'icon' => '/assets/images/icons/allergenen/gray/sesamzaad.png',
                'type' => Allergenen::TYPE_SESAMZAAD,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 11,
                'icon' => '/assets/images/icons/allergenen/gray/soja.png',
                'type' => Allergenen::TYPE_SOJA,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 12,
                'icon' => '/assets/images/icons/allergenen/gray/vis.png',
                'type' => Allergenen::TYPE_VIS,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 13,
                'icon' => '/assets/images/icons/allergenen/gray/weekdieren.png',
                'type' => Allergenen::TYPE_WEEKDIEREN,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 14,
                'icon' => '/assets/images/icons/allergenen/gray/zwavel.png',
                'type' => Allergenen::TYPE_ZWAVEL,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ];

        // Disable FOREIGN_KEY_CHECKS
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear allergenens table
        \DB::table('allergenens')->truncate();

        // Insert new data to the table
        DB::table('allergenens')->insert($allergenens);

        // Enable FOREIGN_KEY_CHECKS again
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
