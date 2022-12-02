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
        Schema::create('exercise_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon_image');
            $table->timestamps();
        });

        \DB::table('exercise_types')->insert([
            ['id'=> 10,     , 'icon_image' => "Images/exercise_types/asset1.png",      'name' => 'Gym'],
            ['id'=> 15,     , 'icon_image' => "Images/exercise_types/asset2.png",      'name' => 'Bodyweight with equipments'],
            ['id'=> 20,     , 'icon_image' => "Images/exercise_types/asset3.png",      'name' => 'Cardio'],
            ['id'=> 25,     , 'icon_image' => "Images/exercise_types/asset4.png",      'name' => 'Body Weight'],
            ['id'=> 30,     , 'icon_image' => "Images/exercise_types/asset5.png",      'name' => 'Stretching & Mobility'],
            ['id'=> 35,     , 'icon_image' => "Images/exercise_types/asset6.png",      'name' => 'Resistance Band'],
            
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercise_types');
    }
};
