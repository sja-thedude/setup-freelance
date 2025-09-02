<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->longText('note')->nullable();
            $table->tinyInteger('address_type')->nullable();
            $table->string('address')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->dateTime('date_time')->nullable();
            $table->unsignedBigInteger('open_timeslot_id')->nullable();
            $table->unsignedBigInteger('setting_payment_id')->nullable();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
            $table->foreign('open_timeslot_id')->references('id')->on('open_timeslots')->onDelete('set null');
            $table->foreign('setting_payment_id')->references('id')->on('setting_payments')->onDelete('set null');

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
        Schema::dropIfExists('carts');
    }
}
