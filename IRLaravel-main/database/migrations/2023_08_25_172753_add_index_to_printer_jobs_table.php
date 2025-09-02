<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToPrinterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('printer_jobs', function (Blueprint $table) {
            $table->index('status');
            $table->index('mac_address');
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
            $table->dropIndex('printer_jobs_status_index');
            $table->dropIndex('printer_jobs_mac_address_index');
        });
    }
}
