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
            $table->bigInteger('message_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->text('content');
            $table->string('image', 255)->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('message')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
}
