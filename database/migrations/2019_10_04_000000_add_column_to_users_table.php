<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AddColumnToUsersTable extends Migration
{
    /**
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'profilePicture')) {
            Schema::table('users', static function (Blueprint $table) {
                $table->string('profilePicture')->after('email')->default('profile.png');
            });
        }
    }

    /**
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'profilePicture')) {
            Schema::table('users', static function (Blueprint $table) {
                $table->dropColumn('profilePicture');
            });
        }
    }
}