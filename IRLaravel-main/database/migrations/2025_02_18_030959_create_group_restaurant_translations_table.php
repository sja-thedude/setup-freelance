<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupRestaurantTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('group_restaurant_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('group_restaurant_id')->index();
            $table->string('locale')->index();

            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('group_restaurant_id')
                ->references('id')
                ->on('group_restaurant')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('group_restaurant_translations');
    }
}
