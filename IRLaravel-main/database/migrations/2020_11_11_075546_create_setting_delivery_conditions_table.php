<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingDeliveryConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_delivery_conditions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('area_start')->nullable();
            $table->integer('area_end')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('free', 10, 0)->nullable();
            $table->unsignedBigInteger('workspace_id')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_delivery_conditions');
    }
}
