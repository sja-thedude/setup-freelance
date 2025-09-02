<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id')->comment('Related to workspaces table');
            $table->string('title');
            $table->string('description')->nullable();
            $table->tinyInteger('type')->default(1);
            $table->integer('score')->default(0);
            $table->decimal('reward', 10, 2)->default(0);
            $table->date('expire_date');
            $table->boolean('repeat')->default(true);
            $table->timestamps();

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
        Schema::dropIfExists('reward_levels');
    }
}
