<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MuscleType extends Model
{
    use HasFactory;
    const BackMuscle = 1;
    const FrontMuscle = 2;

}
