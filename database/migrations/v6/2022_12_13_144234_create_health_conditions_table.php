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
        Schema::create('health_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        \DB::table('health_conditions')->insert([
            ['id'=> 1, 'name' => 'Health Condition 1'],
            ['id'=> 2, 'name' => 'Health Condition 2'],
            ['id'=> 3, 'name' => 'Health Condition 3'],
            ['id'=> 4, 'name' => 'Health Condition 4'],
            ['id'=> 5, 'name' => 'Health Condition 5'],
            ['id'=> 6, 'name' => 'Health Condition 6'],
            ['id'=> 7, 'name' => 'Health Condition 7'],
            ['id'=> 8, 'name' => 'Health Condition 8'],
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_conditions');
    }
};
