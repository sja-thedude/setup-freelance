<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecificTakeoutDeliveryInhouseFieldsToSettingConnectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_connectors', function (Blueprint $table) {
            $table->longText('takeout_endpoint')->after('token');
            $table->longText('takeout_key')->after('takeout_endpoint');
            $table->longText('takeout_token')->after('takeout_key');

            $table->longText('delivery_endpoint')->after('takeout_token');
            $table->longText('delivery_key')->after('delivery_endpoint');
            $table->longText('delivery_token')->after('delivery_key');

            $table->longText('inhouse_endpoint')->after('delivery_token');
            $table->longText('inhouse_key')->after('inhouse_endpoint');
            $table->longText('inhouse_token')->after('inhouse_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setting_connectors', function (Blueprint $table) {
            $table->dropColumn([
                'takeout_endpoint',
                'takeout_key',
                'takeout_token',

                'delivery_endpoint',
                'delivery_key',
                'delivery_token',

                'inhouse_endpoint',
                'inhouse_key',
                'inhouse_token'
            ]);
        });
    }
}
