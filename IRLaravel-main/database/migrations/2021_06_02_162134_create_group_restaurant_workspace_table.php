<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupRestaurantWorkspaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_restaurant_workspace', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group_restaurant_id');
            $table->unsignedBigInteger('workspace_id');
            $table->foreign('group_restaurant_id')->references('id')->on('group_restaurant')->onDelete('cascade');
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
        Schema::dropIfExists('group_restaurant_workspace');
    }
}
