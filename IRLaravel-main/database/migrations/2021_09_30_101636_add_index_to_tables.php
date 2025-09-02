<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Column: order 
         */
        Schema::table('products', function (Blueprint $table) {
            $table->index(['order', 'created_at']);
        });

        Schema::table('product_opties', function (Blueprint $table) {
            $table->index('is_checked');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['order', 'created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'payment_method', 'printed_sticker']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['order', 'created_at']);
        });

        Schema::table('product_opties', function (Blueprint $table) {
            $table->dropIndex(['is_checked']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['order', 'created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'payment_method', 'printed_sticker']);
        });
    }
}
