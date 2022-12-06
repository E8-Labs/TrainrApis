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
        Schema::create('all_expertises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon_image');
            $table->timestamps();
        });
        \DB::table('all_expertises')->insert([
            ['id'=> 10,     'icon_image' => "assets/Expertise/exp group.png",               'name' => 'Group Exercise'],
            ['id'=> 15,     'icon_image' => "assets/Expertise/exp body weight.png",         'name' => 'Bodyweight with equipments'],
            ['id'=> 20,     'icon_image' => "assets/Expertise/exp mind and body.png",       'name' => 'Mind and body fitness'],
            ['id'=> 25,     'icon_image' => "assets/Expertise/exp strength.png",            'name' => 'Strength and conditioning'],
            ['id'=> 30,     'icon_image' => "assets/Expertise/exp weight loss.png",         'name' => 'Weight Loss'],
            ['id'=> 35,     'icon_image' => "assets/Expertise/exp health coaching.png",     'name' => 'Health Coaching'],
            
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('all_expertises');
    }
};
