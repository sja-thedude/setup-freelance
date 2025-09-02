<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateTimeIndexToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table)
        {
            $table->index('date_time');
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table)
        {
            $table->dropIndex('orders_date_time_index');
        });
    }
}
