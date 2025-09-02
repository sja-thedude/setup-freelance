<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailBehaviorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_behaviors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('action')
                ->comment('Behavior action');
            $table->string('email')
                ->comment('Email to');
            $table->unsignedBigInteger('workspace_id')->nullable()
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
        Schema::dropIfExists('email_behaviors');
    }
}
