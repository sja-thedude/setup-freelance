<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoPrintFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('auto_print_sticker')->nullable()->default(false);
            $table->tinyInteger('auto_print_werkbon')->nullable()->default(false);
            $table->tinyInteger('auto_print_kassabon')->nullable()->default(false);
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
                'auto_print_sticker',
                'auto_print_werkbon',
                'auto_print_kassabon'
            ]);
        });
    }
}
