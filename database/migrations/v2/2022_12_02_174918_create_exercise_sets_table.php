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
        Schema::create('exercise_sets', function (Blueprint $table) {
            $table->id();
            $table->integer('rep_count');
            $table->double('weight')->nullable();

            $table->unsignedBigInteger('exercise_id');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');

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
        Schema::dropIfExists('exercise_sets');
    }
};
