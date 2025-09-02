<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSettingTimeslotDetailIdToCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('setting_timeslot_detail_id')->nullable()
                ->after('open_timeslot_id')
                ->comment('Related to setting_timeslot_details table');

            // Foreign keys
            $table->foreign('setting_timeslot_detail_id')->references('id')->on('setting_timeslot_details')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['setting_timeslot_detail_id']);
        });
    }
}
