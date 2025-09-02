<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('banner_id')->unsigned();
            $table->string('locale')->index();
            $table->text('title_1')->nullable();
            $table->text('title_2')->nullable();
            $table->string('button_1')->nullable();
            $table->string('button_2')->nullable();
            $table->text('link_1')->nullable();
            $table->text('link_2')->nullable();
            $table->text('description')->nullable();

            $table->unique(['banner_id', 'locale']);
            $table->foreign('banner_id')->references('id')->on('banners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner_translations');
    }
}
