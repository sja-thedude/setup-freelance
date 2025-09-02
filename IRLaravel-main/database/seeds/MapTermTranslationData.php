<?php

use App\Modules\ContentManager\Models\Terms;
use Illuminate\Database\Seeder;

/**
 * Class MapTermTranslationData
 */
class MapTermTranslationData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locale = config('translatable.fallback_locale');
        $termInstance = new Terms();
        $originFields = [];

        foreach ($termInstance->translatedAttributes as $field) {
            $originFields[$field] = "terms.{$field} AS origin__{$field}";
        }

        /** @var \Illuminate\Support\Collection $missingTerms */
        $missingTerms = Terms::select('terms.term_id')
            ->addSelect($originFields)
            ->leftJoin('term_translations', function ($join) use ($locale) {
                $join->on('terms.term_id', '=', 'term_translations.term_id');
                $join->where('term_translations.locale', '=', $locale);
            })
            ->whereNull('term_translations.id')
            ->get();

        /** @var \App\Modules\ContentManager\Models\Terms $term */
        foreach ($missingTerms as $term) {
            $data = [];

            // Push translate content to attribute
            foreach ($originFields as $field => $alias) {
                $data[$field] = $term->getAttribute("origin__{$field}");
            }

            // Fill locale data
            $term->fill([
                $locale => $data
            ]);
            $term->save();
        }

    }
}
