<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelfOrderingToSettingPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('setting_payments', function (Blueprint $table) {
            $table->boolean('self_ordering')
                ->default(false)
                ->after('in_house');
        });
    }

    public function down()
    {
        Schema::table('setting_payments', function (Blueprint $table) {
            $table->dropColumn('self_ordering');
        });
    }
}
