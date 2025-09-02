<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('photo')
                ->comment('Slide Image. The recommended size is 1920x875.');
            $table->text('title_1')->nullable();
            $table->text('title_2')->nullable();
            $table->string('button_1')->nullable()
                ->comment('Title of button 1.');
            $table->string('button_2')->nullable()
                ->comment('Title of button 2.');
            $table->text('link_1')->nullable()
                ->comment('Link of button 1.');
            $table->text('link_2')->nullable()
                ->comment('Link of button 2.');
            $table->tinyInteger('align')->default(0)
                ->comment('Allow values: -1, 0, 1. Where: -1: left, 0: center, 1: right.');
            $table->integer('order')->default(0)
                ->comment('Order when show.');
            $table->text('description')->nullable()
                ->comment('Description for slider.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
}
