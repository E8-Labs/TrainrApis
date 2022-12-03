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
        Schema::create('exercise_difficulties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        \DB::table('exercise_difficulties')->insert([
            ['id'=> ExerciseDifficulty::Beginner, 'name' => 'Beginner'],
            ['id'=> ExerciseDifficulty::Intermediate, 'name' => 'Intermediate'],
            ['id'=> ExerciseDifficulty::Advanced, 'name' => 'Advanced'],
            
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercise_difficulties');
    }
};
