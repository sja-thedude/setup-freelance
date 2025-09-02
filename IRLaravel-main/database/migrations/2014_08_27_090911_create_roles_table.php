<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('platform')->default(0)
                ->comment('Allow values: 0 Backoffice, 1 Client, 2 Patient');
            $table->boolean('active')->default(true)
                ->comment('Allow values: 0, 1. Notes: 0: false, 1: true. Default = 1.');
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('permission')
                ->comment('Permission json items');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
