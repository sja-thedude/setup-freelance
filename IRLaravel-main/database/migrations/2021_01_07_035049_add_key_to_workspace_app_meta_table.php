<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyToWorkspaceAppMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workspace_app_meta', function (Blueprint $table) {
            $table->string('key')->nullable()
                ->after('default')
                ->comment('Fix key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workspace_app_meta', function (Blueprint $table) {
            $table->dropColumn(['key']);
        });
    }
}
