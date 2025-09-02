<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTriggeredScheduledFieldsToOrderReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_references', function (Blueprint $table) {
            $table->dateTime('auto_triggered_at')->nullable()->comment('When triggered automatically during order process')->after('remote_id');
            $table->dateTime('auto_scheduled_at')->nullable()->comment('When scheduled by cronjob')->after('auto_triggered_at');
            $table->dateTime('manually_triggered_at')->nullable()->comment('When triggered by manager or by cli manually')->after('auto_scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_references', function (Blueprint $table) {
            $table->dropColumn(['auto_triggered_at', 'auto_scheduled_at', 'manually_triggered_at']);
        });
    }
}
