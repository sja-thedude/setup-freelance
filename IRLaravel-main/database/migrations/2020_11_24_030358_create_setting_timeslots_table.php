<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingTimeslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_timeslots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id')->nullable()->default(null);
            $table->tinyInteger('type')->nullable()->default(0)->comment('0: takeout, 1: delivery, 2: in-house');
            $table->integer('order_per_slot')->nullable()->default(null)->comment('Aantal bestellingen/tijdslot');
            $table->integer('max_price_per_slot')->nullable()->default(null)->comment('Maximum bedrag/tijdslot');
            $table->integer('interval_slot')->nullable()->default(null)->comment('Interval tussen tijdslots');
            $table->tinyInteger('max_mode')->nullable()->default(0)->comment('Bestellen tot maximaal');
            $table->time('max_time')->nullable()->default(null);
            $table->integer('max_before')->nullable()->default(0)->comment('0, 1, 2 days');
            $table->text('max_days')->nullable()->default(null)->comment('0,1,2,3,4,5,6');
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
        Schema::dropIfExists('setting_timeslots');
    }
}
