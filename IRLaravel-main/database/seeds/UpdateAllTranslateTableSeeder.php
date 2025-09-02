<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\CategoryTranslation;
use App\Models\ProductTranslation;
use App\Models\OptionTranslation;
use App\Models\CouponTranslation;

class UpdateAllTranslateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = array_keys(config('languages'));
        $countLang = count($languages);
        $defaultLocale = config('translatable.fallback_locale');
        $modules = [
            [
                'model' => new CategoryTranslation(),
                'foreign_id' => 'category_id'
            ], [
                'model' => new ProductTranslation(),
                'foreign_id' => 'product_id'
            ], [
                'model' => new OptionTranslation(),
                'foreign_id' => 'opties_id'
            ], [
                'model' => new CouponTranslation(),
                'foreign_id' => 'coupon_id'
            ]
        ];

        foreach($modules as $module) {
            $moduleData = $this->process($module['model'], $module['foreign_id'], $countLang, $defaultLocale, $languages);

            if(!empty($moduleData)) {
                ($module['model'])->insert($moduleData);
            }
        }
    }

    public function process($modelTrans, $foreignId, $countLang, $defaultLocale, $languages) {
        $insertData = [];
        $trans = $modelTrans
            ->select([$foreignId, DB::raw('COUNT(locale) as count')])
            ->groupBy($foreignId)
            ->having('count', '<', $countLang)
            ->get();

        if(!$trans->isEmpty()) {
            $foreignIds = $trans->pluck($foreignId);
            $allTrans = $modelTrans->whereIn($foreignId, $foreignIds)->get();

            if(!$allTrans->isEmpty()) {
                $allTrans = $allTrans->groupBy(function($item) use ($foreignId) {
                    return $item->$foreignId;
                });

                foreach($allTrans as $tranGroup) {
                    $defaultTrans = $tranGroup->where('locale', $defaultLocale)->first();

                    if(empty($defaultTrans)) {
                        $defaultTrans = $tranGroup->first();
                    }
                    
                    $existLocales = $tranGroup->pluck('locale')->toArray();
                    $needMigrates = array_diff($languages, $existLocales);

                    if(!empty($needMigrates)) {
                        $item = $defaultTrans->toArray();
                        unset($item['id']);

                        foreach($needMigrates as $needMigrate) {
                            $item['locale'] = $needMigrate;
                            $insertData[] = $item;
                        }
                    }
                }
            }
        }

        return $insertData;
    }
}
