<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupAppRestaurantIdToOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('group_restaurant_id')->nullable()->after('template_id')
                ->comment('Related to group_restaurant table. Group App ID.');

            // Foreign keys
            $table->foreign('group_restaurant_id')->references('id')->on('group_restaurant')->onDelete('cascade');
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
            $table->dropForeign(['group_restaurant_id']);
            $table->dropColumn('group_restaurant_id');
        });
    }
}
