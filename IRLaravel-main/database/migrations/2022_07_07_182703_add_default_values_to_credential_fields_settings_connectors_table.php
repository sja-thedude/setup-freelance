<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValuesToCredentialFieldsSettingsConnectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_connectors', function (Blueprint $table) {
            $table->longText('takeout_endpoint')->default('')->change();
            $table->longText('takeout_key')->default('')->change();
            $table->longText('takeout_token')->default('')->change();

            $table->longText('delivery_endpoint')->default('')->change();
            $table->longText('delivery_key')->default('')->change();
            $table->longText('delivery_token')->default('')->change();

            $table->longText('inhouse_endpoint')->default('')->change();
            $table->longText('inhouse_key')->default('')->change();
            $table->longText('inhouse_token')->default('')->change();
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
            $table->longText('takeout_endpoint')->change();
            $table->longText('takeout_key')->change();
            $table->longText('takeout_token')->change();

            $table->longText('delivery_endpoint')->change();
            $table->longText('delivery_key')->change();
            $table->longText('delivery_token')->change();

            $table->longText('inhouse_endpoint')->change();
            $table->longText('inhouse_key')->change();
            $table->longText('inhouse_token')->change();
        });
    }
}
