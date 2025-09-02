<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardLevelTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_level_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reward_level_id');
            $table->string('locale')->index();
            $table->string('title');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('reward_level_id')
                ->references('id')
                ->on('reward_levels')
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
        Schema::dropIfExists('reward_level_translations');
    }
}
