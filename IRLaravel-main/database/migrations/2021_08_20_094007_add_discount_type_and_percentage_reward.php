<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscountTypeAndPercentageReward extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reward_levels', function (Blueprint $table) {
            $table->tinyInteger('discount_type')->comment('Allows: 1 => Fix amount, 2 => Percentage')->default(1);
            $table->integer('percentage')->nullable()->comment('Percentage discount');
            $table->string('reward')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reward_levels', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('percentage');
            $table->string('reward')->nullable(false)->change();
        });
    }
}
