<?php

use App\Models\Post;
use Illuminate\Database\Seeder;

class UpdateSlugPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $post_terms = Post::find(16);
        if ($post_terms) {
            $post_terms->update(['post_mime_type' => 'terms-and-conditions']);
        }

        $post_privacy = Post::find(17);
        if ($post_privacy) {
            $post_privacy->update(['post_mime_type' => 'privacy-policy']);
        }

        $post_cookie = Post::find(18);
        if ($post_cookie) {
            $post_cookie->update(['post_mime_type' => 'cookie-policy']);
        }
    }
}
