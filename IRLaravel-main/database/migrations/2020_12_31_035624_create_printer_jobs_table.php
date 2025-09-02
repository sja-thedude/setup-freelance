<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrinterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('workspace_id')->nullable()->default(null)->comment('workspaces id');
            $table->unsignedBigInteger('printer_id')->nullable()->default(null)->comment('setting prints id');
            $table->tinyInteger('status')->default(1)->comment('1: pending, 2: printing, 3: done, 4: error');
            $table->tinyInteger('job_type')->default(1)->comment('0: kassabon, 1: werkbon, 2: sticker');
            $table->string('foreign_model')->nullable()->default(null)->comment('parent model');
            $table->unsignedBigInteger('foreign_id')->nullable()->default(null)->comment('parent id');
            $table->text('content')->nullable()->default(null);
            $table->longText('meta_data')->nullable()->default(null);
            $table->dateTime('printed_at')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('printer_id')->references('id')->on('setting_prints')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('printer_jobs');
    }
}
