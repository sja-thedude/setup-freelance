<?php

use Illuminate\Database\Seeder;

class RewardLevelTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supportLanguages = \App\Helpers\Helper::getActiveLanguages();
        $rewardLevels = DB::table('reward_levels')->get();

        foreach ($rewardLevels as $reward) {
            foreach ($supportLanguages as $locale => $language) {
                DB::table('reward_level_translations')->insert([
                    'reward_level_id' => $reward->id,
                    'locale'          => $locale,
                    'title'           => $reward->title,
                    'description'     => $reward->description,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}
