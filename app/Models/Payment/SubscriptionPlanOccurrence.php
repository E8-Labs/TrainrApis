<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlanOccurrence extends Model
{
    use HasFactory;
    const Weekly = 1;
    const Monthly = 2;
    const Quarterly = 3;
    const HalfYearly = 4;
    const Yearly = 5;
    const None = 6;
}
