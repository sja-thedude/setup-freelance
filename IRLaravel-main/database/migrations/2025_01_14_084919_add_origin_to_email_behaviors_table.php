<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginToEmailBehaviorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_behaviors', function (Blueprint $table) {
            $table->string('origin')->nullable()->default(\App\Models\EmailBehavior::ORIGIN_LARAVEL);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_behaviors', function (Blueprint $table) {
            $table->dropColumn('origin');
        });
    }
}
