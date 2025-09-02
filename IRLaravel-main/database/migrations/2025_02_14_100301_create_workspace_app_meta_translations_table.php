<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkspaceAppMetaTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('workspace_app_meta_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('workspace_app_meta_id')->index();
            $table->string('locale')->index();

            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('url')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('workspace_app_meta_id')
                ->references('id')
                ->on('workspace_app_meta')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('workspace_app_meta_translations');
    }
}
