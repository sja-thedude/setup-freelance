<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToOpenTimeslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('open_timeslots', function (Blueprint $table) {
            $table->index(['foreign_id', 'foreign_model']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('open_timeslots', function (Blueprint $table) {
            $table->dropIndex(['foreign_id', 'foreign_model']);	
        });
    }
}
