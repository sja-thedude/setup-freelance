<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSocialEnabledFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workspaces', function (Blueprint $table) {
            // facebook
            $table->tinyInteger('facebook_enabled')->default(0)->after('firebase_project');
            // google
            $table->tinyInteger('google_enabled')->default(0)->after('facebook_key');
            // apple
            $table->tinyInteger('apple_enabled')->default(0)->after('google_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_enabled',
                'google_enabled',
                'apple_enabled',
            ]);
        });
    }
}
