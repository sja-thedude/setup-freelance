<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id')->nullable();
            $table->integer('takeout_min_time')->nullable();
            $table->integer('takeout_day_order')->nullable();
            $table->integer('delivery_min_time')->nullable();
            $table->integer('delivery_day_order')->nullable();
            $table->integer('mins_before_notify')->nullable();
            $table->tinyInteger('use_sms_whatsapp')->nullable();
            $table->tinyInteger('use_email')->nullable();
            $table->tinyInteger('receive_notify')->nullable();
            $table->tinyInteger('sound_notify')->nullable();
            $table->unsignedBigInteger('opties_id')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
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
        Schema::dropIfExists('setting_preferences');
    }
}
