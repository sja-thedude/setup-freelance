<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspaceObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspace_objects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('active')->default(true)
                ->comment('Allow values: 0, 1. Where: 0: false, 1: true. Default = 1.');
            $table->bigInteger('workspace_id')->unsigned()
                ->comment('Reference with workspaces table.');
            $table->string('model')
                ->comment('Model name.');
            $table->bigInteger('foreign_key')->unsigned()
                ->comment('Foreign key to model.');
            $table->text('meta_data')->nullable()
                ->comment('JSON object to store meta data.');
            $table->timestamps();

            // Indexing
            $table->index(['workspace_id', 'model', 'foreign_key']);
            // Unique
            $table->unique(['workspace_id', 'model', 'foreign_key']);
            // Primary keys
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
        Schema::dropIfExists('workspace_objects');
    }
}
