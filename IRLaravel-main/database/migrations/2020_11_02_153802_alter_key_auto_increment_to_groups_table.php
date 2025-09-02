<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKeyAutoIncrementToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('groups', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('groups', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
        });
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
