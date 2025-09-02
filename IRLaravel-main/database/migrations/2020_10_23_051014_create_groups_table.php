<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->unsignedBigInteger('workspace_id')
                ->comment('Related to workspaces table');
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('company_street')->nullable();
            $table->string('company_number')->nullable();
            $table->string('company_vat_number')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_postcode')->nullable();
            $table->tinyInteger('payment_mollie')->nullable();
            $table->tinyInteger('payment_payconiq')->nullable();
            $table->tinyInteger('payment_cash')->nullable();
            $table->tinyInteger('payment_factuur')->nullable();
            $table->time('close_time')->nullable();
            $table->time('receive_time')->nullable();
            $table->tinyInteger('type')->nullable()
                ->comment('0: takeout, 1: delivery');
            $table->string('contact_email')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_surname')->nullable();
            $table->string('contact_gsm')->nullable();

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
        Schema::dropIfExists('groups');
    }
}
