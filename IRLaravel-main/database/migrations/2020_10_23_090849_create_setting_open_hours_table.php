<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingOpenHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_open_hours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->tinyInteger('type')
                ->comment('0: takeout, 1: delivery, 2: in-house');
            $table->boolean('active')->default(false);
            $table->unsignedBigInteger('workspace_id')
                ->comment('Related to workspaces table');

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
        Schema::dropIfExists('setting_open_hours');
    }
}
