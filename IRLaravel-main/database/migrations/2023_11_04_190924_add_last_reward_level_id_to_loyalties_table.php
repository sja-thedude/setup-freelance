<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastRewardLevelIdToLoyaltiesTable extends Migration
{
    public function up()
    {
        Schema::table('loyalties', function (Blueprint $table) {
            $table->unsignedBigInteger('last_reward_level_id')->nullable()
                ->comment('Last reward which user has redeemed')
                ->after('reward_level_id');

            // Foreign keys
            $table->foreign('last_reward_level_id')
                ->references('id')
                ->on('reward_levels')
                ->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::table('loyalties', function (Blueprint $table) {
            $table->dropForeign(['last_reward_level_id']);
            $table->dropColumn(['last_reward_level_id']);
        });
    }
}
