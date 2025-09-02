<?php

use App\Modules\ContentManager\Models\Articles;
use Illuminate\Database\Seeder;

/**
 * Class MapPostTranslationData
 */
class MapPostTranslationData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locale = config('translatable.fallback_locale');
        $postInstance = new Articles();
        $originFields = [];

        foreach ($postInstance->translatedAttributes as $field) {
            $originFields[$field] = "posts.{$field} AS origin__{$field}";
        }

        /** @var \Illuminate\Support\Collection $missingPosts */
        $missingPosts = Articles::select('posts.id')
            ->addSelect($originFields)
            ->leftJoin('post_translations', function ($join) use ($locale) {
                $join->on('posts.id', '=', 'post_translations.post_id');
                $join->where('post_translations.locale', '=', $locale);
            })
            ->whereNull('post_translations.id')
            ->get();

        /** @var \App\Modules\ContentManager\Models\Articles $post */
        foreach ($missingPosts as $post) {
            $data = [];

            // Push translate content to attribute
            foreach ($originFields as $field => $alias) {
                $data[$field] = $post->getAttribute("origin__{$field}");
            }

            // Fill locale data
            $post->fill([
                $locale => $data
            ]);
            $post->save();
        }

    }
}
