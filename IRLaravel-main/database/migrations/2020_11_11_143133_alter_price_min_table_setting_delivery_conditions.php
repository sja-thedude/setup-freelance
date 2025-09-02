<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPriceMinTableSettingDeliveryConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_delivery_conditions', function (Blueprint $table) {
            $table->decimal('price_min', 10, 0)->nullable()->after('area_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_delivery_conditions', function (Blueprint $table) {
            $table->dropColumn('price_min');
        });
    }
}
