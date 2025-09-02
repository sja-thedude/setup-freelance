<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCreateNewDiscountFieldsGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->tinyInteger('is_product_limit')->comment('Allows: 0, 1. 0 => Unlimited, 1 => Limited')->default(0);
            $table->tinyInteger('discount_type')->comment('Allows: 0, 1, 2. 0 => No discount, 1 => Fixed amount, 2 => Percentage')->default(1);
            $table->decimal('discount')->nullable()->comment('The group discount in fixed number');
            $table->integer('percentage')->nullable()->comment('The group discount in percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('is_product_limit');
            $table->dropColumn('discount_type');
            $table->dropColumn('discount');
            $table->dropColumn('percentage');
        });
    }
}
