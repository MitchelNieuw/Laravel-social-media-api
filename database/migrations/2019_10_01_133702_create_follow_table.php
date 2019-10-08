<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowTable extends Migration
{
    /**
     */
    public function up(): void
    {
        Schema::create('follow', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('follow_user_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('follow_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     */
    public function down(): void
    {
        Schema::dropIfExists('follow');
    }
}
