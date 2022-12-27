<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_users', function (Blueprint $table) {
            $table->increments('chat_user_id');
            $table->unsignedInteger('chat_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('unread_count')->default(0);
                       $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                       $table->foreign('chat_id')->references('chat_id')->on('chats')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_users');
    }
}
