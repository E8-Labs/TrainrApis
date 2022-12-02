<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseDifficulty extends Model
{
    use HasFactory;
    const Beginner = 1;
    const Intermediate = 2;
    const Advanced = 3;
}
