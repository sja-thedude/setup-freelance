<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCollationToTableRestaurantCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE restaurant_categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurant_categories', function (Blueprint $table) {
            DB::statement("ALTER TABLE restaurant_categories CONVERT TO CHARACTER SET utf8mb4_bin COLLATE utf8mb4");
            $table->string('name')->collation('utf8mb4')->change();
        });
    }
}
