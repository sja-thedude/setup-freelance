<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMaxPricePerSlotToSettingTimeslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_timeslots', function (Blueprint $table) {
            $table->decimal('max_price_per_slot')->nullable()->default(null)->comment('Maximum bedrag/tijdslot')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_timeslots', function (Blueprint $table) {
            $table->integer('max_price_per_slot')->nullable()->default(null)->comment('Maximum bedrag/tijdslot')->change();
        });
    }
}
