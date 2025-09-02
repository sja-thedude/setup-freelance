<?php

use Illuminate\Database\Seeder;

class ChangeLanguageWorkspaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('workspaces')
            ->update([
                'language' => 'nl'
            ]);
        DB::table('users')
            ->update([
                'locale' => 'nl'
            ]);
    }
}
