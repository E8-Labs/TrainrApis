<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('completed_workout_exercises', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('completed_workout_id');
            $table->foreign('completed_workout_id')->references('id')->on('completed_workouts')->onDelete('cascade');

            $table->unsignedBigInteger('exercise_id');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');

            $table->integer('reps')->default(0);
            $table->unsignedBigInteger('set_id');
            $table->foreign('set_id')->references('id')->on('exercise_sets')->onDelete('cascade');
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
        Schema::dropIfExists('completed_workout_exercises');
    }
};
