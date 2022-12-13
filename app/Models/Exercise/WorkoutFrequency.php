<?php

namespace App\Models\Exercise;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutFrequency extends Model
{
    use HasFactory;
    const JustStarting = 1;
	const OnceAWeek = 2;
	const TwoThreeTimes = 3;
	const FourFiveTimes = 4; 
}
