<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrinterMonitorFieldsToWorkspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dateTime('failed_at_kassabon')->nullable()->default(null)->after('surname');
            $table->dateTime('failed_at_werkbon')->nullable()->default(null)->after('failed_at_kassabon');
            $table->dateTime('failed_at_sticker')->nullable()->default(null)->after('failed_at_werkbon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn('failed_at_kassabon');
            $table->dropColumn('failed_at_werkbon');
            $table->dropColumn('failed_at_sticker');
        });
    }
}
