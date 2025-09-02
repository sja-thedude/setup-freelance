<?php

use Illuminate\Database\Seeder;

class SettingExceptHourTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supportLanguages = \App\Helpers\Helper::getActiveLanguages();
        $records = DB::table('setting_except_hours')->get();

        foreach ($records as $record) {
            foreach ($supportLanguages as $locale => $language) {
                DB::table('setting_except_hour_translations')->insert([
                    'setting_except_hour_id' => $record->id,
                    'locale'                 => $locale,
                    'description'            => $record->description,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);
            }
        }
    }
}
