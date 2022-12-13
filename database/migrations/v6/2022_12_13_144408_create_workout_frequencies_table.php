<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Exercise\WorkoutFrequency;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workout_frequencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        \DB::table('workout_frequencies')->insert([
            ['id'=> WorkoutFrequency::JustStarting, 'name' => 'Just Starting'],
            ['id'=> WorkoutFrequency::OnceAWeek, 'name' => 'Once a week'],
            ['id'=> WorkoutFrequency::TwoThreeTimes, 'name' => '2-3x a week'],
            ['id'=> WorkoutFrequency::FourFiveTimes, 'name' => '3-4x a week'],
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workout_frequencies');
    }
};
