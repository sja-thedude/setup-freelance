<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id')->nullable();
            $table->tinyInteger('platform')->nullable()->comment('Allow values: 0 Admin, Account Manager, 1 Manager, 2 Client');
            $table->string('title', 255)->nullable();
            $table->longText('description')->nullable();
            $table->tinyInteger('is_send_everyone')->nullable();
            $table->text('location')->nullable();
            $table->string('location_lat', 255)->nullable();
            $table->string('location_long', 255)->nullable();
            $table->integer('location_radius')->nullable();
            $table->integer('send_now')->nullable();
            $table->timestamp('send_datetime')->nullable();
            $table->tinyInteger('gender_dest')->nullable();
            $table->integer('start_age_dest')->nullable();
            $table->integer('end_age_dest')->nullable();
            
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
        Schema::dropIfExists('notification_plans');
    }
}
