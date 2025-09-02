<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('reward_id');
            $table->unsignedBigInteger('category_id');

            $table->unique(['reward_id', 'category_id']);

            // Foreign keys
            $table->foreign('reward_id')->references('id')->on('reward_levels')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_categories');
    }
}
