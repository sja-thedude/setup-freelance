<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingPreferenceTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('setting_preference_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('setting_preference_id')->index();
            $table->string('locale')->index();

            $table->longText('holiday_text')->nullable();
            $table->longText('table_ordering_pop_up_text')->nullable();
            $table->longText('self_ordering_pop_up_text')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('setting_preference_id')
                ->references('id')
                ->on('setting_preferences')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('setting_preference_translations');
    }
}
