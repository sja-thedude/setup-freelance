<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeyToVatsTable extends Migration
{
    public function up()
    {
        Schema::table('vats', function (Blueprint $table) {
            $table->string('key')->nullable()
                ->after('id')
                ->comment('Fix key');

            $table->index('key');
        });

        // Check exist column and call command
        if (Schema::hasColumn('vats', 'key')) {
            Artisan::call('translate:vat', [
                '--force' => 1,
            ]);
        }
    }

    public function down()
    {
        Schema::table('vats', function (Blueprint $table) {
            $table->dropIndex(['key']);
            $table->dropColumn(['key']);
        });
    }
}
