<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptieItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('optie_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opties_id');
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 10)->nullable();
            $table->boolean('available')->default(1);
            $table->boolean('master')->default(0);
            $table->timestamps();

            $table->foreign('opties_id')->references('id')->on('opties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('optie_items');
    }
}
