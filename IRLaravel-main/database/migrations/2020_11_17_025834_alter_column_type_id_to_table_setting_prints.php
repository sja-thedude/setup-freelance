<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnTypeIdToTableSettingPrints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_prints', function (Blueprint $table) {
            $table->tinyInteger('type_id')->nullable()->default(0)->comment('0: Tijdslot, 1: Identieke producten')->after('auto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_prints', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });
    }
}
