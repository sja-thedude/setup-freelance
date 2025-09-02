<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceCostToSettingPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_preferences', function (Blueprint $table) {
            $table->tinyInteger('service_cost_set')->default(0);
            $table->decimal('service_cost', 10, 2)->nullable()->default(0);
            $table->decimal('service_cost_amount', 10, 2)->nullable()->default(0);
            $table->tinyInteger('service_cost_always_charge')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_preferences', function (Blueprint $table) {
            $table->dropColumn(['service_cost_set', 'service_cost', 'service_cost_amount', 'service_cost_always_charge']);
        });
    }
}
