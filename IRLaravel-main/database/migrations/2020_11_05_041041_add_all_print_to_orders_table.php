<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllPrintToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('printed_werkbon')->nullable()->default(false)->after('printed');
            $table->tinyInteger('printed_kassabon')->nullable()->default(false)->after('printed');
            $table->tinyInteger('printed_sticker')->nullable()->default(false)->after('printed');
            $table->tinyInteger('printed_a4')->nullable()->default(false)->after('printed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'printed_werkbon',
                'printed_kassabon',
                'printed_sticker',
                'printed_a4'
            ]);
        });
    }
}
