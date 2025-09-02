<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogsToPrinterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('printer_jobs', function (Blueprint $table) {
            $table->longText('logs')->nullable()->default(null)->after('retries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('printer_jobs', function (Blueprint $table) {
            $table->dropColumn('logs');
        });
    }
}
