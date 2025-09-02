<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscountFieldsToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable()
                ->after('vat_percent');
            $table->unsignedBigInteger('redeem_history_id')->nullable()
                ->after('coupon_discount');
            $table->decimal('redeem_discount')->nullable()
                ->after('redeem_history_id');

            // Foreign keys
            $table->foreign('coupon_id')->references('id')->on('coupons')
                ->onDelete('set null');
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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropForeign(['redeem_history_id']);

            $table->dropColumn(['coupon_id', 'redeem_history_id', 'redeem_discount']);
        });
    }
}
