<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrinterGroupWorkspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer_group_workspaces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('printer_group_id');
            $table->unsignedBigInteger('workspace_id');
            $table->foreign('printer_group_id')->references('id')->on('printer_groups')->onDelete('cascade');
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
        Schema::dropIfExists('printer_group_workspaces');
    }
}
