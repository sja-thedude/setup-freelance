<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_relation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('foreign_model')->nullable()->default(null)->comment('Model class');
            $table->bigInteger('foreign_id')->nullable()->default(null)->comment('Model id');
            $table->bigInteger('category_id')->nullable()->default(null)->comment('Category id');
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
        Schema::dropIfExists('categories_relation');
    }
}
