<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingTimeslotDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_timeslot_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id')->nullable()->default(null);
            $table->unsignedBigInteger('setting_timeslot_id')->nullable()->default(null);
            $table->tinyInteger('type')->nullable()->default(0)->comment('0: takeout, 1: delivery, 2: in-house');
            $table->tinyInteger('active')->nullable()->default(true);
            $table->time('time')->nullable()->default(null);
            $table->integer('max')->nullable()->default(null);
            $table->date('date')->nullable()->default(null);
            $table->integer('day_number')->nullable()->default(null);
            $table->tinyInteger('repeat')->nullable()->default(false)->comment('Repeat each weeks');
            $table->timestamps();

            // Foreign keys
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('setting_timeslot_id')->references('id')->on('setting_timeslots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_timeslot_details');
    }
}
