<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowForceDeleteWorkspaceInOpenTimeslotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('open_timeslots', function (Blueprint $table) {
            // Drop old foreign keys
            $table->dropForeign(['workspace_id']);

            // Init new foreign keys
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
        Schema::table('open_timeslots', function (Blueprint $table) {
            // Rollback old stage
            $table->dropForeign(['workspace_id']);
            $table->foreign('workspace_id')->references('id')->on('workspaces');
        });
    }
}
