<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToGroupsTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table)
        {
            $table->index('group_id');
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table)
        {
            $table->dropIndex('group_id');
        });
    }
}
