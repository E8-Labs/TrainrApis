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
        // predefined goals or the trainr can also add more to the list
        Schema::create('meal_defined_goals', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('user_id')->nullable(); // could be null for predefined goals. The Trainr can add it's own
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
        \DB::table('meal_defined_goals')->insert([
            ['id'=> 1, 'name' => 'Weight Loss'],
            ['id'=> 2, 'name' => 'Sculpt'],
            ['id'=> 3, 'name' => 'Muscle Gains'],
            ['id'=> 4, 'name' => 'Mental Sharpness'],
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meal_defined_goals');
    }
};
