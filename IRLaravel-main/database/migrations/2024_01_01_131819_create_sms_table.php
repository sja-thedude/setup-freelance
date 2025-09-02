<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('workspace_id')->nullable()->default(null)->comment('workspaces id');
            $table->tinyInteger('status')->default(1)->comment('1: pending, 2: sent, 3: error');
            $table->text('message')->nullable()->default(null);
            $table->dateTime('sent_at')->nullable()->default(null);
            $table->string('foreign_model')->nullable()->default(null);
            $table->unsignedBigInteger('foreign_id')->nullable()->default(null);
            $table->timestamps();
            $table->index('foreign_model');
            $table->index('foreign_id');
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
        Schema::dropIfExists('sms');
    }
}
