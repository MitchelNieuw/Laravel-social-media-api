<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReactionsTable extends Migration
{
    /**
     */
    public function up(): void
    {
        Schema::create('reactions', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('message_id')->references('id')->on('message');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->text('content');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
}
