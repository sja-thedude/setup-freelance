<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRetriesToPrinterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('printer_jobs', function (Blueprint $table) {
            $table->integer('retries')->nullable()
                ->default(0)
                ->after('meta_data');
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
            $table->dropColumn('retries');
        });
    }
}
