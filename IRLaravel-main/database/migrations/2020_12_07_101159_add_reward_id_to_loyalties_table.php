<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRewardIdToLoyaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loyalties', function (Blueprint $table) {
            $table->unsignedBigInteger('reward_level_id')->nullable()
                ->comment('Current redeems the reward');

            // Foreign keys
            $table->foreign('reward_level_id')->references('id')->on('reward_levels')
                ->onDelete('cascade');
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
            $table->dropColumn(['reward_level_id']);
        });
    }
}
