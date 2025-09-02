<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('role_id')->unsigned()->nullable()
                ->comment('Foreign key with roles table.');
            $table->string('email');
            $table->string('email_tmp')->nullable()
                ->comment('For change email function.');
            $table->string('password');
            $table->string('default_password')->nullable();
            $table->integer('is_super_admin')->default(0);
            $table->integer('is_admin')->default(0);
            $table->boolean('active')->default(true)
                ->comment('Allow values: 0, 1. Notes: 0: false, 1: true. Default = 1.');
            $table->boolean('is_verified')->default(false)
                ->comment('Verify status. Allow values: 0, 1. Notes: 0: false, 1: true. Default = 0.');
            $table->tinyInteger('platform')->default(0)
                ->comment('Allow values: 0 Admin, Account Manager, 1 Manager, 2 Client');
            $table->string('name', 510)->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->longText('photo')->nullable();
            $table->longText('description')->nullable();
            $table->date('birthday')->nullable();
            $table->tinyInteger('gender')->nullable()
                ->comment('Allow values: 0: none, 1: male, 2: female, 3: others.');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->dateTime('last_login')->nullable()
                ->comment('Last login datetime');
            $table->string('locale', 10)->nullable()
                ->comment('User locale.');
            $table->string('timezone')->nullable()
                ->comment('Timezone key strings (from PHP timezone list function).');
            $table->string('api_token', 60)->nullable()->unique();
            $table->rememberToken();
            $table->string('fb_id')->nullable();
            $table->text('fb_token')->nullable();
            $table->string('gg_id')->nullable();
            $table->text('gg_token')->nullable();
            $table->string('tw_id')->nullable();
            $table->text('tw_token')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
