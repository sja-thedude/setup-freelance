<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspaceAppMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspace_app_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->boolean('active')->default(false)
                ->comment('Allow values: 0, 1. Note 0: false; 1: true');
            $table->integer('order')->nullable()
                ->comment('Desired order');
            $table->unsignedBigInteger('workspace_app_id')
                ->comment('Related to workspace_apps table');

            $table->boolean('default')->default(false)
                ->comment('Allow values: 0, 1. Note 0: false; 1: true');
            $table->string('name');
            $table->tinyInteger('type')->default(1)
                ->comment('1: Show description, URL; 2: Show description; 3: Show description, title, content');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('icon')->nullable();
            $table->text('url')->nullable();
            $table->longText('meta_data')->nullable();

            // Foreign keys
            $table->foreign('workspace_app_id')->references('id')->on('workspace_apps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workspace_app_meta');
    }
}
