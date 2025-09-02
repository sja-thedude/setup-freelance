<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscountFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('redeem_history_id')->nullable()
                ->after('coupon_discount');
            $table->decimal('redeem_discount')->nullable()
                ->after('redeem_history_id');

            // Foreign keys
            $table->foreign('redeem_history_id')->references('id')->on('redeem_histories')
                ->onDelete('set null');
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
            $table->dropForeign(['redeem_history_id']);

            $table->dropColumn(['redeem_history_id', 'redeem_discount']);
        });
    }
}
