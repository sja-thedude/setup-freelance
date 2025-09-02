<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTemplateIdToNotificationDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_devices', function (Blueprint $table) {
            /*// Detach foreign key
            $table->dropForeign(['workspace_id']);

            // Rename column
            $table->renameColumn('workspace_id', 'template_id');*/

            // Use statement for unknown issue from migration
            DB::statement("ALTER TABLE `notification_devices` DROP FOREIGN KEY `notification_devices_workspace_id_foreign`");
            DB::statement("ALTER TABLE `notification_devices` 
                CHANGE `workspace_id` `template_id` BIGINT(20) UNSIGNED DEFAULT NULL 
                    COMMENT 'Related to workspaces table. Template App ID.'");

            // Foreign keys
            $table->foreign('template_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_devices', function (Blueprint $table) {
            /*// Detach foreign key
            $table->dropForeign(['template_id']);

            // Rename column
            $table->renameColumn('template_id', 'workspace_id');*/

            // Use statement for unknown issue from migration
            DB::statement("ALTER TABLE `notification_devices` DROP FOREIGN KEY `notification_devices_template_id_foreign`");
            DB::statement("ALTER TABLE `notification_devices` 
                CHANGE `template_id` `workspace_id` BIGINT(20) UNSIGNED DEFAULT NULL 
                    COMMENT 'Related to workspaces table'");

            // Foreign keys
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
    }
}
