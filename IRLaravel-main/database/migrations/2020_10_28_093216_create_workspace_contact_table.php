<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspaceContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspace_contact', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('workspace_id')
                ->comment('Related to workspaces table');
            $table->unsignedBigInteger('contact_id')
                ->comment('Related to contacts table');

            $table->unique(['workspace_id', 'contact_id']);

            // Foreign keys
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workspace_contact');
    }
}
