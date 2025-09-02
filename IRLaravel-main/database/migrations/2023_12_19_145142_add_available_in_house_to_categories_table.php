<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvailableInHouseToCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('available_in_house')
                ->default(false)
                ->after('available_delivery');
            $table->boolean('exclusively_in_house')
                ->default(false)
                ->after('available_in_house');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['available_in_house', 'exclusively_in_house']);
        });
    }
}
