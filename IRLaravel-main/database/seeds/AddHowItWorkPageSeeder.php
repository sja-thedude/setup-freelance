<?php

use Illuminate\Database\Seeder;

class AddHowItWorkPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'post_author' => 1,
                'post_type'   => 'page',
                'post_title'  => 'How does it work',
                'post_name'   => str_slug('How does it work', "-")
            ]
        ];

        $supportLanguages = \App\Helpers\Helper::getActiveLanguages();
        foreach ($datas as $data) {
            // A language with template
            $item = [];
            $item['post_author'] = $data['post_author'];
            $item['post_type'] = $data['post_type'];

            $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $data['post_title']);
            foreach ($supportLanguages as $locale => $language) {
                $item[$locale] = [
                    'post_title'   => $data['post_title'],
                    'post_name'    => $data['post_name'],
                    'post_content' => $content,
                ];
            }
            \App\Modules\ContentManager\Models\Articles::create($item);
        }
    }
}
