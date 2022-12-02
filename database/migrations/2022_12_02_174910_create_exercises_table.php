<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ExerciseDifficulty;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('exercise_title');
            $table->string('cover_image');
            $table->string('youtube_url')->default('');
            $table->integer('set_count');

            $table->unsignedBigInteger('difficulty')->default(ExerciseDifficulty::Beginner);
            $table->foreign('difficulty')->references('id')->on('exercise_difficulties')->onDelete('cascade');

            $table->unsignedBigInteger('muscle_group');
            $table->foreign('muscle_group')->references('id')->on('muscle_groups')->onDelete('cascade');

            $table->unsignedBigInteger('exercise_type');
            $table->foreign('exercise_type')->references('id')->on('exercise_types')->onDelete('cascade');

            


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
        Schema::dropIfExists('exercises');
    }
};
