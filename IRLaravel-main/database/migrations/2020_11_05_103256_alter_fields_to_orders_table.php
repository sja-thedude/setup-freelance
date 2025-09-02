<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->default(null)->after('user_id');
            $table->unsignedBigInteger('user_id')->nullable()->default(null)->change();

            // Foreign keys
            $table->foreign('parent_id')->references('id')->on('orders')->onDelete('cascade');
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
