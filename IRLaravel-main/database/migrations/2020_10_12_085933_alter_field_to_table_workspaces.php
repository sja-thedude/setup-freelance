<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFieldToTableWorkspaces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->bigInteger('account_manager_id');
            $table->string('gsm', 255)->nullable();
            $table->string('manager_name', 255)->nullable();
            $table->longText('address')->nullable()->comment('Autocomplete');
            $table->string('btw_nr', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('language', 255)->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->timestamp('first_login')->nullable()->comment('Actief sinds');
            $table->tinyInteger('status')->nullable()->comment('0: uitnodiging verstuurd, 1: first login');
            $table->string('address_lat', 255)->nullable();
            $table->string('address_long', 255)->nullable();
            $table->boolean('is_online')->default(true);
            $table->boolean('is_test_mode')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn([
                'gsm',
                'manager_name',
            ]);
        });
    }
}
