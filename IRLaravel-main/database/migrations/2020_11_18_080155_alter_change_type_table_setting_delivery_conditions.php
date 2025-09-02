<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterChangeTypeTableSettingDeliveryConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_delivery_conditions', function (Blueprint $table) {
            $table->decimal('price_min', 10, 2)->default(0)->change();
            $table->decimal('price', 10, 2)->default(0)->change();
            $table->decimal('free', 10, 2)->default(null)->change();
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
            $table->decimal('price_min', 10, 0)->default(null)->change();
            $table->decimal('price', 10, 2)->default(null)->change();
            $table->decimal('free', 10, 0)->default(null)->change();
        });
    }
}
