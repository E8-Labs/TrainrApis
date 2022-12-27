<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $primaryKey = 'chat_id';
    protected $guarded = ['id'];

    public function chatUser()
    {
        return $this->hasMany(ChatUser::class, 'chat_id', 'chat_id');
    }
}
