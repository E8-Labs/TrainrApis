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
        Schema::create('completed_workouts', function (Blueprint $table) {
            $table->id();
            $table->string('completed_date');//m-d-y
            $table->unsignedBigInteger('workout_id');
            $table->foreign('workout_id')->references('id')->on('workouts')->onDelete('cascade');
            $table->double('percentage')->default(0);
            $table->integer('calories')->default(0);

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
        Schema::dropIfExists('completed_workouts');
    }
};
