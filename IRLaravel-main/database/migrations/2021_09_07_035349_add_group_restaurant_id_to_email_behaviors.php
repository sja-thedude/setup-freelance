<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupRestaurantIdToEmailBehaviors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_behaviors', function (Blueprint $table) {
            $table->unsignedBigInteger('group_restaurant_id')->nullable()->comment('Related to group restaurant');
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
        Schema::table('email_behaviors', function (Blueprint $table) {
            $table->dropForeign(['group_restaurant_id']);
            $table->dropColumn('group_restaurant_id');
        });
    }
}
