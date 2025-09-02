<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSettingDeliveryConditionIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('setting_delivery_condition_id')
                ->nullable()
                ->after('setting_timeslot_detail_id')
                ->comment('Related to setting_delivery_conditions table');

            // Foreign keys
            $table->foreign('setting_delivery_condition_id')
                ->references('id')
                ->on('setting_delivery_conditions')
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
            $table->dropColumn(['setting_delivery_condition_id']);
        });
    }
}
