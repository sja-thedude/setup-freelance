<?php

use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
//            [
//                'post_author' => 1,
//                'post_type' => 'page',
//                'post_title' => 'Contact',
//                'post_name' => str_slug('Contact',"-")
//            ],
            [
                'post_author' => 1,
                'post_type' => 'page',
                'post_title' => 'About us',
                'post_name' => str_slug('About us',"-")
            ],
            [
                'post_author' => 1,
                'post_type' => 'page',
                'post_title' => 'Terms and conditions',
                'post_name' => str_slug('Terms and conditions',"-")
            ],
            [
                'post_author' => 1,
                'post_type' => 'page',
                'post_title' => 'Privacy policy',
                'post_name' => str_slug('Privacy policy',"-")
            ],
            [
                'post_author' => 1,
                'post_type' => 'page',
                'post_title' => 'Cookie policy',
                'post_name' => str_slug('Cookie policy',"-")
            ],
            [
                'post_author' => 1,
                'post_type' => 'page',
                'post_title' => 'FAQs',
                'post_name' => str_slug('FAQs',"-")
            ],
        ];
        $supportLanguages = \App\Helpers\Helper::getActiveLanguages();

        //Truncate table
//        DB::statement('SET FOREIGN_KEY_CHECKS=0');
//        DB::table('posts')->truncate();
//        DB::table('post_translations')->truncate();
        
        foreach ($datas as $data) {
            // A language with template
            $item = [];
            $item['post_author'] = $data['post_author'];
            $item['post_type'] = $data['post_type'];

            $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $data['post_title']);
            foreach ($supportLanguages as $locale => $language) {
                $item[$locale] = [
                    'post_title' => $data['post_title'],
                    'post_name' => $data['post_name'],
                    'post_content' => $content,
                ];
            }
            \App\Modules\ContentManager\Models\Articles::create($item);
        }
    }
}
