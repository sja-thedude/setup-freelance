<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedeemHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redeem_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->unsignedBigInteger('loyalty_id')->nullable()
                ->comment('Related to loyalties table');
            $table->unsignedBigInteger('reward_level_id')->nullable()
                ->comment('Related to reward_levels table');

            $table->longText('reward_data')->nullable()
                ->comment('Backup reward data. Format is JSON encode.');

            // Foreign keys
            $table->foreign('loyalty_id')->references('id')->on('loyalties')
                ->onDelete('cascade');
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
        Schema::dropIfExists('redeem_histories');
    }
}
