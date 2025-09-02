<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('foreign_id');
            $table->string('foreign_model');
            $table->string('foreign_type')->nullable();
            $table->string('field_name')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->decimal('file_size', 32, 2)->nullable();
            $table->text('file_path');
            $table->text('full_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medias');
    }
}
