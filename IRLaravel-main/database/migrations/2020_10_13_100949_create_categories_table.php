<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id');
            $table->tinyInteger('individual')->default(0)->nullable();
            $table->tinyInteger('group')->default(0)->nullable();
            $table->tinyInteger('available_delivery')->nullable();
            $table->tinyInteger('favoriet_friet')->nullable();
            $table->tinyInteger('kokette_kroket')->nullable();
            $table->boolean('time_no_limit')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
