<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->longText('meta_data')->nullable()->comment('backup all information of this order')->after('user_id');
            $table->tinyInteger('type')->default(0)->comment('0: takeout, 1: delivery, 2: in-house')->after('user_id');
            $table->tinyInteger('address_type')->default(0)->comment('0: login address, 1: enter address')->after('user_id');
            $table->string('address')->nullable()->after('user_id');
            $table->date('date')->nullable()->comment('step 2')->after('user_id');
            $table->time('time')->nullable()->comment('step 2')->after('user_id');
            $table->dateTime('date_time')->nullable()->comment('step 2')->after('user_id');
            $table->string('coupon_code')->nullable()->after('user_id');
            $table->tinyInteger('payment_status')->nullable()->default(null)->comment('0: error, 1: success')->after('user_id');
            $table->tinyInteger('payment_method')->nullable()->default(null)->comment('cash, ....')->after('user_id');
            $table->integer('daily_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('coupon_id')->nullable()->default(null)->after('user_id');
            $table->unsignedBigInteger('group_id')->nullable()->default(null)->comment('null if is individual, not null if is group')->after('user_id');
            $table->unsignedBigInteger('open_timeslot_id')->nullable()->default(null)->comment('step 2')->after('user_id');
            $table->unsignedBigInteger('setting_payment_id')->nullable()->default(null)->comment('step 3')->after('user_id');

            // Foreign keys
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
            $table->foreign('open_timeslot_id')->references('id')->on('open_timeslots')->onDelete('set null');
            $table->foreign('setting_payment_id')->references('id')->on('setting_payments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_setting_payment_id_foreign');
            $table->dropForeign('orders_open_timeslot_id_foreign');
            $table->dropForeign('orders_group_id_foreign');
            $table->dropForeign('orders_coupon_id_foreign');
            $table->dropColumn([
                'setting_payment_id',
                'open_timeslot_id',
                'group_id',
                'coupon_id',
                'daily_id',
                'payment_method',
                'payment_status',
                'coupon_code',
                'date_time',
                'time',
                'date',
                'address',
                'address_type',
                'type',
                'meta_data'
            ]);
        });
    }
}
