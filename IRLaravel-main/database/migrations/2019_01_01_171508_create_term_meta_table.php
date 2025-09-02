<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_meta', function (Blueprint $table) {
            $table->bigIncrements('meta_id')->unsigned();
            $table->bigInteger('term_id')->default(0)->unsigned();
            $table->string('meta_key');
            $table->longText('meta_value');
            $table->index('term_id');
            $table->index('meta_key');

            // Foreign key
            $table->foreign('term_id')->references('term_id')->on('terms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_meta');
    }
}
