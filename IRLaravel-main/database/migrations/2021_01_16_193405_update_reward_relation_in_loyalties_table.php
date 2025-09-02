<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRewardRelationInLoyaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loyalties', function (Blueprint $table) {
            $table->dropForeign(['reward_level_id']);

            // Change onDelete action
            $table->foreign('reward_level_id')->references('id')->on('reward_levels')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loyalties', function (Blueprint $table) {
            $table->dropForeign(['reward_level_id']);

            // Foreign keys
            $table->foreign('reward_level_id')->references('id')->on('reward_levels')
                ->onDelete('cascade');
        });
    }
}
