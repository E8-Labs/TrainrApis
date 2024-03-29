<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Community\PostPrivacy;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->string('post_description');
            $table->string('post_image');
            $table->unsignedBigInteger('post_privacy')->default(PostPrivacy::PrivacyPublic);
            $table->foreign('post_privacy')->references('id')->on('post_privacies')->onDelete('cascade');
            $table->double('image_width')->nullable();
            $table->double('image_height')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('posts');
    }
};
