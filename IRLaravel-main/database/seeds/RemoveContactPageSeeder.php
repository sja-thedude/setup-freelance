<?php

use Illuminate\Database\Seeder;

class RemoveContactPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return \App\Modules\ContentManager\Models\Articles::whereTranslation("post_name", 'contact')->where("post_type", "page")->delete();
    }
}
