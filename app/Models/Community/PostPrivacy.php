<?php

namespace App\Models\Community;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostPrivacy extends Model
{
    use HasFactory;
    const PrivacyPublic = 1;
    const PrivacyPrivate = 2;
    const PrivacyFriends = 3;
}
