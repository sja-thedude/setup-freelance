<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id')
                ->comment('Related to workspaces table');
            $table->tinyInteger('type')->default(2)->comment('0: mollie, 1: payconiq, 2: cash...');
            $table->string('api_token')->nullable();
            $table->tinyInteger('takeout')->default(false);
            $table->tinyInteger('delivery')->default(false);
            $table->tinyInteger('in_house')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_payments');
    }
}
