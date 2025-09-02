<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertFkSomethingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opties', function (Blueprint $table) {
            $table->dropForeign('opties_workspace_id_foreign');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_workspace_id_foreign');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });

        Schema::table('category_opties', function (Blueprint $table) {
            $table->dropForeign('category_opties_category_id_foreign');
            $table->dropForeign('category_opties_opties_id_foreign');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('opties_id')->references('id')->on('opties')->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_workspace_id_foreign');
            $table->dropForeign('products_category_id_foreign');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opties', function (Blueprint $table) {
            $table->dropForeign('opties_workspace_id_foreign');
            $table->foreign('workspace_id')->references('id')->on('workspaces');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_workspace_id_foreign');
            $table->foreign('workspace_id')->references('id')->on('workspaces');
        });

        Schema::table('category_opties', function (Blueprint $table) {
            $table->dropForeign('category_opties_category_id_foreign');
            $table->dropForeign('category_opties_opties_id_foreign');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('opties_id')->references('id')->on('opties');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_workspace_id_foreign');
            $table->dropForeign('products_category_id_foreign');
            $table->foreign('workspace_id')->references('id')->on('workspaces');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }
}
