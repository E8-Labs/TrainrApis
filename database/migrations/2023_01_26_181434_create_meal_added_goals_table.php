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
        // Goals for a particular Meal that the trainr selected while adding that meal
        Schema::create('meal_added_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meal_goal');
            $table->foreign('meal_goal')->references('id')->on('meal_defined_goals')->onDelete('cascade');

            $table->unsignedBigInteger('meal_id');
            $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
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
        Schema::dropIfExists('meal_added_goals');
    }
};
