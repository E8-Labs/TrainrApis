<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Chat\ChatType;

class CreateChatTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_types', function (Blueprint $table) {
            $table->id();
            $table->string('chat_type');
            $table->string('comment');
            $table->timestamps();
        });
        \DB::table('chat_types')->insert([
            ['id'=> ChatType::OneToOne, 'name' => '1-1 Chat'],
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_types');
    }
}
