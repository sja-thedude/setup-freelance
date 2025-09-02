<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspaceAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspace_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->boolean('active')->default(false)
                ->comment('Allow values: 0, 1. Note 0: false; 1: true');
            $table->unsignedBigInteger('workspace_id')
                ->comment('Related to workspaces table');
            $table->tinyInteger('theme')->default(1)
                ->comment('Choose a theme. Number start from 1');

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
        Schema::dropIfExists('workspace_apps');
    }
}
