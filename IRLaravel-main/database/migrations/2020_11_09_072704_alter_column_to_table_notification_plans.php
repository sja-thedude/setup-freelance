<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnToTableNotificationPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_plans', function (Blueprint $table) {
            DB::statement('ALTER TABLE `notification_plans` CHANGE `gender_dest` `gender_dest_male` TINYINT(4) NULL DEFAULT NULL;');
            $table->tinyInteger('gender_dest_female')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_plans', function (Blueprint $table) {
            DB::statement('ALTER TABLE `notification_plans` CHANGE `gender_dest_male` `gender_dest` TINYINT(4) NULL DEFAULT NULL;');
            $table->dropColumn('gender_dest_female');
        });
    }
}
