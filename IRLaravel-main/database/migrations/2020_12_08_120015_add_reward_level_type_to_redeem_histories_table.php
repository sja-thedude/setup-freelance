<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRewardLevelTypeToRedeemHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('redeem_histories', function (Blueprint $table) {
            $table->tinyInteger('reward_level_type')->nullable()
                ->after('reward_level_id')
                ->comment('Original reward_levels.type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redeem_histories', function (Blueprint $table) {
            $table->dropColumn(['reward_level_type']);
        });
    }
}
