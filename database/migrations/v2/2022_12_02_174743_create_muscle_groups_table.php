<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\MuscleType;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muscle_groups', function (Blueprint $table) {
            $table->id();
            $table->string('muscle_group_name');
            $table->unsignedBigInteger('muscle_type');
            $table->foreign('muscle_type')->references('id')->on('muscle_types')->onDelete('cascade');
            $table->timestamps();
        });

        \DB::table('muscle_groups')->insert([
            ['id'=> 10,    'muscle_type' => MuscleType::BackMuscle,      'muscle_group_name' => 'Hamstrings'],
            ['id'=> 15,    'muscle_type' => MuscleType::FrontMuscle,     'muscle_group_name' => 'Chest'],
            ['id'=> 20,    'muscle_type' => MuscleType::FrontMuscle,     'muscle_group_name' => 'Biceps'],
            ['id'=> 25,    'muscle_type' => MuscleType::FrontMuscle,     'muscle_group_name' => 'Triceps'],
            ['id'=> 30,    'muscle_type' => MuscleType::FrontMuscle,     'muscle_group_name' => 'Shoulders'],
            ['id'=> 35,    'muscle_type' => MuscleType::FrontMuscle,     'muscle_group_name' => 'Core'],
            ['id'=> 40,    'muscle_type' => MuscleType::BackMuscle,      'muscle_group_name' => 'Glutes'],
            
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('muscle_groups');
    }
};
