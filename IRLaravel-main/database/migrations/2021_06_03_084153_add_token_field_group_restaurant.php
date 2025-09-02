<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenFieldGroupRestaurant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_restaurant', function (Blueprint $table) {
            $table->string('token')->nullable()->comment('Unique token which used in the App template')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_restaurant', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
}
