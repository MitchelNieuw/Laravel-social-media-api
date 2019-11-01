<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageColumnToMessageTable extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::table('message', static function (Blueprint $table) {
            $table->string('image', 255)->after('content')->nullable();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
