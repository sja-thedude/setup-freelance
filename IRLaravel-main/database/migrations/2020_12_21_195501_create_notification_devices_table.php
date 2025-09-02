<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->boolean('active')->default(true)
                ->comment('Allow values: 0, 1. Note: 0: false, 1: true.');
            $table->unsignedBigInteger('user_id')->nullable()
                ->comment('Related to users table');
            $table->string('device_id')
                ->comment('Device ID string from app');
            $table->text('token')
                ->comment('Token string of device.');
            $table->boolean('notified')->nullable()->default(false)
                ->comment('Enable push notification or not. Allow values: 0, 1. Note: 0: false, 1: true.');
            $table->tinyInteger('type')
                ->default(0)->comment('Allow values: 0, 1, 2, 3. Note: 0: Unknown; 1: Web Browser; 2: IOS; 3: Android');

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_devices');
    }
}
