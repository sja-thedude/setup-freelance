<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspaceExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspace_extras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('workspace_id')->unsigned()->nullable();
            $table->boolean('active')->default(false)->comment('Allow values: 0, 1. Notes: 0: false, 1: true. Default = 0.');
            $table->integer('type')->nullable()->comment('0: payconiq, 1: group order, 2: loyalty card, 3: Allergenen, 4: SMS/Whatsapp, 5: show in common app, 6: personalized app');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workspace_extras');
    }
}
