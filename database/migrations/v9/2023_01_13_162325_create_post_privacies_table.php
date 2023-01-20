<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Community\PostPrivacy;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_privacies', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->timestamps();
        });

        \DB::table('post_privacies')->insert([
            ['id'=> PostPrivacy::PrivacyPrivate, 'name' => 'Private'],
            ['id'=> PostPrivacy::PrivacyPublic, 'name' => 'Public'],
            ['id'=> PostPrivacy::PrivacyFriends, 'name' => 'Friends'],
            
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_privacies');
    }
};
