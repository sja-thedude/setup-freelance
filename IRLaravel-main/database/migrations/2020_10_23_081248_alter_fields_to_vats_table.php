<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFieldsToVatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vats', function (Blueprint $table) {
            $table->dropColumn('take_out_delivery');
            $table->bigInteger('country_id')->unsigned()->nullable()->after('in_house');
            $table->decimal('delivery', 10, 2)->nullable()->after('in_house');
            $table->decimal('take_out', 10, 2)->nullable()->after('in_house');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vats', function (Blueprint $table) {
            $table->dropForeign('vats_country_id_foreign');
            $table->dropColumn([
                'delivery',
                'take_out',
                'country_id'
            ]);
            $table->decimal('take_out_delivery', 10, 2)->nullable()->after('in_house');
        });
    }
}
