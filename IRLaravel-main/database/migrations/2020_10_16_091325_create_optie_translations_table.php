<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptieTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('optie_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opties_id');
            $table->string('locale', 10);
            $table->string('name');
            $table->timestamps();

            $table->foreign('opties_id')->references('id')->on('opties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('optie_translations');
    }
}
