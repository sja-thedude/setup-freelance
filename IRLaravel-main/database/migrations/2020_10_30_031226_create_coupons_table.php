<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('workspace_id')
                ->comment('Related to workspaces table');
            $table->string('code');
            $table->integer('max_time_all')->nullable();
            $table->integer('max_time_single')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->dateTime('expire_time')->nullable();

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
        Schema::dropIfExists('coupons');
    }
}
