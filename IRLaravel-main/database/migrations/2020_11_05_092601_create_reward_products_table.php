<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_products', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('reward_id');
            $table->unsignedBigInteger('product_id');

            $table->unique(['reward_id', 'product_id']);

            // Foreign keys
            $table->foreign('reward_id')->references('id')->on('reward_levels')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reward_products');
    }
}
