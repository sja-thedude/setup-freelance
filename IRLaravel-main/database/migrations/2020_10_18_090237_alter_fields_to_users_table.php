<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gsm', 20)->nullable()->after('last_login');
            $table->tinyInteger('status')->default(2)->nullable()->after('last_login')
                ->comment('0: active, 1: invitation expired, 2: invitation sent');
            $table->dateTime('first_login')->nullable()->after('last_login');
            $table->integer('credit')->default(0)->nullable()->after('last_login');
            $table->bigInteger('workspace_id')->unsigned()->nullable()->after('last_login');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gsm',
                'status',
                'first_login',
                'credit'
            ]);
            
            $table->dropForeign('users_workspace_id_foreign');
            $table->foreign('workspace_id')->references('id')->on('workspaces');
        });
    }
}
