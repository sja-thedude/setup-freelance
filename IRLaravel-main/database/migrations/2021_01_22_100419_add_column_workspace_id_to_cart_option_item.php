<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkspaceIdToCartOptionItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_option_items', function (Blueprint $table) {
            $table->unsignedBigInteger('workspace_id')->nullable()
                ->comment('Related to workspaces table');

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
        Schema::table('cart_option_items', function (Blueprint $table) {
            $table->dropUnique('workspace_id');
        });
    }
}
