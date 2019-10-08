<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddIndexToFollowTable extends Migration
{
    /**
     */
    public function up(): void
    {
        Schema::table('follow', static function (Blueprint $table) {
            $table->index('user_id');
            $table->index('follow_user_id');
        });
    }

    /**
     */
    public function down(): void
    {
        Schema::table('follow', static function (Blueprint $table) {
           $table->dropIndex('user_id');
           $table->dropIndex('follow_user_id');
        });
    }
}