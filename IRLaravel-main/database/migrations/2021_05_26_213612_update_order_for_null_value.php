<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderForNullValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medias', function (Blueprint $table) {
            DB::statement('ALTER TABLE `medias` MODIFY COLUMN `order` int(11) NULL DEFAULT NULL');
        });

        DB::statement(DB::raw("UPDATE medias SET medias.order = medias.id WHERE medias.foreign_type = 'api_galleries' AND ISNULL(medias.order)"));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
