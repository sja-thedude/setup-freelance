<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsSocialsToWorkspacesTable extends Migration
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
            $table->text('facebook_id')->nullable()->default(null);
            $table->text('facebook_key')->nullable()->default(null);
            // google
            $table->text('google_id')->nullable()->default(null);
            $table->text('google_key')->nullable()->default(null);
            // apple
            $table->text('apple_id')->nullable()->default(null);
            $table->text('apple_key')->nullable()->default(null);
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
                'facebook_id',
                'facebook_key',
                'google_id',
                'google_key',
                'apple_id',
                'apple_key'
            ]);
        });
    }
}
