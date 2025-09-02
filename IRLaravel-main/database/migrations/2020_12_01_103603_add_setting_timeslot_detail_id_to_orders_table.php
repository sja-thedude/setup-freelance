<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingTimeslotDetailIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['setting_timeslot_detail_id']);
        });
    }
}
